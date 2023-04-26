<?php

namespace Fahim\ZipCodeValidator\Plugin\Quote\Model;

class ShippingMethodManagement
{
    /**
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     * @param \Fahim\ZipCodeValidator\Model\ResourceModel\Zipcode\CollectionFactory $zipcodeCollection
     * @param \Fahim\ZipCodeValidator\Helper\Data $helper
     */
    public function __construct(
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Fahim\ZipCodeValidator\Model\ResourceModel\Zipcode\CollectionFactory $zipcodeCollection,
        \Fahim\ZipCodeValidator\Helper\Data $helper
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->zipcodeCollection = $zipcodeCollection;
        $this->helper = $helper;
    }

    /**
     * Plugin function to execute before estimateByExtendedAddress
     *
     * @param \Magento\Quote\Model\ShippingMethodManagement $subject
     * @param int $cartId
     * @param object $address
     * @return array
     */
    public function beforeEstimateByExtendedAddress(
        \Magento\Quote\Model\ShippingMethodManagement $subject,
        $cartId,
        $address
    ) {
        try {
            if ($this->helper->getEnableDisable()) {
                $enteredPostCode = $address->getPostCode();
                $quote = $this->quoteRepository->getActive($cartId);
                foreach ($quote->getAllVisibleItems() as $item) {
                    $productId = $item->getProductId();
                    $available = $this->getProductRegion($productId, $enteredPostCode);

                    if (!$available && $enteredPostCode && $enteredPostCode!=="") {
                        $address->setPostCode('');
                        $address->setCountryId('');
                        return [$cartId, $address];
                    }
                }
            } else {
                return [$cartId, $address];
            }
        } catch (\Exception $e) {
            $this->helper->logDataInLogger(
                "Plugin_Quote_Model_ShippingMethodManagement_beforeEstimateByExtendedAddress Exception : "
                .$e->getMessage()
            );
            return [$cartId, $address];
        }
    }

    /**
     * Get Product Region
     *
     * @param integer $id
     * @param string $zip
     * @return integer
     */
    public function getProductRegion($id, $zip)
    {
        $result = 1;
        try {
            $regionIds = $this->helper->validateZipCode($id);
            if (!empty($regionIds)) {
                $zipcodeModel = $this->zipcodeCollection->create()
                    ->addFieldToFilter('region_zipcode_from', ['lteq' => $zip])
                    ->addFieldToFilter('region_zipcode_to', ['gteq' => $zip])
                    ->addFieldToFilter('region_id', ['in', $regionIds]);
                $result = $zipcodeModel->getSize();
            }
        } catch (\Exception $e) {
            $this->helper->logDataInLogger(
                "Plugin_Quote_Model_ShippingMethodManagement_beforeEstimateByExtendedAddress Exception : "
                .$e->getMessage()
            );
        }
        return $result;
    }
}
