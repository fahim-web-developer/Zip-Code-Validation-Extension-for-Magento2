<?php

namespace Fahim\ZipCodeValidator\Controller\Zipcode;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;

class Shippingresult extends Action
{
    /**
     * @var \Fahim\ZipCodeValidator\Model\ResourceModel\Zipcode\CollectionFactory
     */
    protected $zipcodeCollection;

    /**
     * @var Magento\Catalog\Model\Product
     */
    protected $product;

    /**
     * @var Fahim\ZipCodeValidator\Helper
     */
    protected $helper;

    /**
     * @param Context $context
     * @param Magento\Customer\Model\Session $session
     * @param Magento\Catalog\Model\Product $product
     * @param Fahim\ZipCodeValidator\Model\ResourceModel\Zipcode\CollectionFactory $zipcode
     * @param \Fahim\ZipCodeValidator\Helper\Data $helper
     */
    public function __construct(
        Context $context,
        \Magento\Customer\Model\Session $session,
        \Magento\Catalog\Model\Product $product,
        \Fahim\ZipCodeValidator\Model\ResourceModel\Zipcode\CollectionFactory $zipcodeCollection,
        \Fahim\ZipCodeValidator\Helper\Data $helper
    ) {
        $this->zipcodeCollection = $zipcodeCollection;
        $this->session = $session;
        $this->product = $product;
        $this->helper = $helper;
        parent::__construct($context);
    }

    /**
     * @return json
     */
    public function execute()
    {
        $data = [];
        if ($this->helper->getConfigValue('wk_zipcodevalidatorstatus')) {
            try {
                $result = [];
                $zip = $this->getRequest()->getParam('zip');
                $productIds = $this->getRequest()->getParam('productId');
                foreach ($productIds as $id) {
                    list($available, $productName) = $this->getProductRegion($id, $zip);
                    if (!$available) {
                        $result[] = $productName;
                    }
                }
                
                if ($zip && $zip!=="" && !empty($result)) {
                    $productNames = implode(", ", $result);
                    $verb = count($result) > 1 ? "are" : "is" ;
                   $data['message'] = __("Please remove %1 from the cart as it is not serviceable at %3.", $productNames, $verb, $zip);
                    //$data['message'] = __("%1 %2 not available at %3", $productNames, $verb, $zip);
                }
            } catch (\Exception $e) {
                $this->helper->logDataInLogger(
                    "Controller_Zipcode_Shippingresult_execute Exception : ".$e->getMessage()
                );
            }
        }
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setData($data);
        return $resultJson;
    }
    
    /**
     * Get all address of logged Customer
     *
     * @return array
     */
    public function getAllAddressOfCustomer()
    {
        $customerAddress = [];
        try {
            if ($this->session->getCustomerId()) {
                $customer = $this->session->getCustomer();
                foreach ($customer->getAddresses() as $address) {
                    $addr = $address->toArray();
                    $postcode = $addr['postcode'];
                    $street = $addr['street'];
                    $city = $addr['city'];
                    if ($street && $city && $postcode) {
                        $custAddr = $postcode.' '.$street.' '.$city;
                    }
                    $customerAddress[] = substr($custAddr, 0, 20).'...';
                }
            }
        } catch (\Exception $e) {
            $this->helper->logDataInLogger(
                "Controller_Zipcode_Shippingresult_getAllAddressOfCustomer Exception : ".$e->getMessage()
            );
        }
        return $customerAddress;
    }

    /**
     * Get Product Region
     *
     * @param integer $id
     * @param string $zip
     * @return array
     */
    public function getProductRegion($id, $zip)
    {
        $result = 1;
        $product = $this->product->load($id);
        $productName = $product->getName();
        $regionIds = $this->helper->validateZipCode($id);
        if (!empty($regionIds)) {
            if (is_numeric($zip)) {
                $zip = (int)$zip;
                $zipcodeModel = $this->zipcodeCollection->create()
                    ->addFieldToFilter('region_zipcode_from', ['lteq' => $zip])
                    ->addFieldToFilter('region_zipcode_to', ['gteq' => $zip])
                    ->addFieldToFilter('region_id', ['in', $regionIds]);
            } else {
                $zipcodeModel = $this->zipcodeCollection->create()
                    ->addFieldToFilter('region_zipcode_from', ['eq' => $zip])
                    ->addFieldToFilter('region_zipcode_to', ['eq' => $zip])
                    ->addFieldToFilter('region_id', ['in', $regionIds]);
            }
            $result = $zipcodeModel->getSize();
        }
        
        return [
            $result,
            $productName
        ];
    }
}
