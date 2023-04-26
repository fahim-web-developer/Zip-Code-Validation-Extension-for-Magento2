<?php

namespace Fahim\ZipCodeValidator\Block\Product;

use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Customer\Model\Context as CustomerContext;

/**
 * Block ViewOnProduct class
 */
class ViewOnProduct extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;

    /**
     * @var Magento\Customer\Model\Address
     */
    private $address;

    /**
     * @var Magento\Catalog\Model\Product
     */
    private $product;

    /**
     * @var Fahim\ZipCodeValidator\Helper
     */
    private $helper;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    private $jsonHelper;

    /**
     * @var Magento\CatalogInventory\Api\StockRegistryInterface
     */
    private $stockRegistry;

    /**
     * @var HttpContext
     */
    private $httpContext;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Customer\Model\SessionFactory $SessionFactory
     * @param \Magento\Customer\Model\Address $address
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry
     * @param \Fahim\ZipCodeValidator\Helper\Data $helper
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param HttpContext $httpContext
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Model\SessionFactory $SessionFactory,
        \Magento\Customer\Model\Address $address,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Fahim\ZipCodeValidator\Helper\Data $helper,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        HttpContext $httpContext,
        array $data = []
    ) {
        $this->customerSession = $customerSession;
        $this->session=$SessionFactory;
        $this->address = $address;
        $this->stockRegistry = $stockRegistry;
        $this->helper = $helper;
        $this->_coreRegistry = $registry;
        $this->jsonHelper = $jsonHelper;
        $this->httpContext = $httpContext;
        parent::__construct($context, $data);
    }

    /**
     * Get Customer Zipcode
     *
     * @return string
     */
    public function getCustomerZipcode()
    {
        $session = $this->session->create();
        $session->getCustomer()->getDefaultShipping();
        if ($session->getCustomer()->getId()) {
            $customerAddressId = $session->getCustomer()->getDefaultShipping();
            $postcode = $this->address->load($customerAddressId)->getPostcode();
            return $postcode;
        }
        return '';
    }

    /**
     * Get Product
     *
     * @return Magento\Catalog\Model\Product
     */
    public function getProduct()
    {
        if (!$this->product) {
            $this->product = $this->_coreRegistry->registry('product');
        }

        return $this->product;
    }

    /**
     * Get Stock status
     *
     * @return boolean
     */
    public function getStockValue($productId)
    {
        try {
            $stockItem = $this->stockRegistry->getStockItem($productId);
            if ($stockItem) {
                return $stockItem->getIsInStock();
            }
        } catch (\Exception $e) {
            $this->helper->logDataInLogger(
                "Block_ViewOnProduct_getStockValue Exception : ".$e->getMessage()
            );
        }
    }

    /**
     * Is Validation required for Zipcode for the product
     *
     * @param int $productId
     * @return boolean
     */
    public function isDisplayValidatorField($productId)
    {
        $regionIds = $this->helper->validateZipCode($productId);
        
        if (!empty($regionIds)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Returns current url
     *
     * @return string
     */
    public function getCurrentUrl()
    {
        // Give the current url of recently viewed page
        return $this->_urlBuilder->getCurrentUrl();
    }

    /**
     * Get Customer Login Status
     *
     * @return boolean
     */
    public function getCustomerStatus()
    {
        return (bool)$this->httpContext->getValue(CustomerContext::CONTEXT_AUTH);
    }

    /**
     * Returns JSON form of data
     * @param array $data
     * @return JSON
     */
    public function getJsonEncodedData($data)
    {
        return $this->jsonHelper->jsonEncode($data);
    }

    /**
     * Get ZipCodeValidator Helper
     *
     * @return \Fahim\ZipCodeValidator\Helper\Data
     */
    public function getZipCodeHelper()
    {
        return $this->helper;
    }
}
