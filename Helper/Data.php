<?php

namespace Fahim\ZipCodeValidator\Helper;

use Magento\Store\Model\ScopeInterface;
use Fahim\ZipCodeValidator\Model\Zipcode;
use Fahim\ZipCodeValidator\Model\ResourceModel\Region\CollectionFactory as RegionCollection;

/**
 * Helper class
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Catalog\Model\ProductRepository $productRepository
     * @param \Fahim\ZipCodeValidator\Logger\Logger $logger
     * @param RegionCollection $regionCollection
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Fahim\ZipCodeValidator\Logger\Logger $logger,
        RegionCollection $regionCollection
    ) {
        $this->productRepository = $productRepository;
        $this->logger = $logger;
        $this->regionCollection = $regionCollection;
        parent::__construct($context);
    }

    /**
     * Logs data in logger
     *
     * @param string $data
     * @return void
     */
    public function logDataInLogger($data)
    {
        $this->logger->info($data);
    }

    /**
     * Get value of configurations for the module
     *
     * @return string|boolean
     */
    public function getConfigValue($field = false)
    {
        if ($field) {
            return $this->scopeConfig->getValue(
                'zipcodevalidator/wk_zipcodevalidatorstatus/'.$field,
                ScopeInterface::SCOPE_STORE
            );
        } else {
            return false;
        }
    }

    /**
     * Get Module enable/disable value from configuration
     *
     * @return int
     */
    public function getEnableDisable()
    {
        return $this->getConfigValue('wk_zipcodevalidatorstatus');
    }

    /**
     * Get ApplyTo Configuration value
     *
     * @return int
     */
    public function getApplyStatus()
    {
        return $this->getConfigValue('applyto');
    }

    /**
     * Get region from configuration
     *
     * @return string
     */
    public function getConfigRegion()
    {
        return $this->getConfigValue('regions');
    }

       /**
     * Get message from configuration
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->getConfigValue('message');
    }

    /**
     * Validates zip code according to configuration
     *
     * @param int $productId
     * @return array
     */
    public function validateZipCode($productId)
    {
        $regionIds = [];
        try {
            $product = $this->productRepository->getById($productId);
            $productZipCodeValidation = $product->getZipCodeValidation();

            $applyStatus = $this->getApplyStatus();
            if ($applyStatus) {
                if ($productZipCodeValidation == null) {
                    $product->setZipCodeValidation(Zipcode::DEFAULT_CONFIG);
                    $this->productRepository->save($product);
                    $product = $this->productRepository->getById($productId);
                    $productZipCodeValidation = $product->getZipCodeValidation();
                }
                if ($productZipCodeValidation == Zipcode::DEFAULT_CONFIG) {
                    $availableregions = $this->getConfigRegion();
                    $regionIds = explode(',', $availableregions);
                } elseif ($productZipCodeValidation == Zipcode::PARTICULAR_PRODUCT) {
                    $availableregions = $product->getAvailableRegion();
                    $regionIds = explode(',', $availableregions);
                }
            } else {
                $availableregions = $product->getAvailableRegion();
                if ($availableregions && !empty($availableregions) && $availableregions!=="") {
                    $regionIds = explode(',', $availableregions);
                }
            }

            if (!empty($regionIds)) {
                $enabledRegions = $this->regionCollection->create()
                    ->addFieldToFilter('id', ['in' => $regionIds])
                    ->addFieldToFilter('status', ['eq' => 1])
                    ->addFieldToSelect('id');
                $regionIds = $enabledRegions->getColumnValues('id');
            }
        } catch (\Exception $e) {
            $this->logDataInLogger(
                "Helper_Data_validateZipCode Exception : ".$e->getMessage()
            );
        }
        return $regionIds;
    }

    /**
     * Check login user capcha Enable/Disable
     *
     * @return void
     */
    public function getCapchaConfig()
    {
        $status = $this->scopeConfig->getValue(
            'customer/captcha/enable',
            ScopeInterface::SCOPE_STORE
        );
        if ($status) {
            $forms = $this->scopeConfig->getValue(
                'customer/captcha/forms',
                ScopeInterface::SCOPE_STORE
            );
                $formArray = explode(',', $forms);
            if (in_array('user_login', $formArray)) {
                return true;
            }
                return false;
        }
        return false;
    }
}
