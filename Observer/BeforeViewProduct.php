<?php

namespace Fahim\ZipCodeValidator\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\RequestInterface;

/**
 * BeforeViewProduct Observer Class
 */
class BeforeViewProduct implements ObserverInterface
{
    /**
     * @param RequestInterface $request
     * @param \Fahim\ZipCodeValidator\Helper\Data $helper
     * @param \Magento\Catalog\Model\ProductFactory $product
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        RequestInterface $request,
        \Fahim\ZipCodeValidator\Helper\Data $helper,
        \Magento\Catalog\Model\ProductFactory $product,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->request = $request;
        $this->helper = $helper;
        $this->product = $product;
        $this->logger = $logger;
    }

    /**
     * Observer executes before viewing the product
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        try {
            $data = $this->request->getParams();
            $productId = $data['id'];
            $applyStatus = $this->helper->getApplyStatus();
            if ($applyStatus) {
                $availableregions = $this->helper->getConfigRegion();
                if ($availableregions) {
                    $regionIds = explode(',', $availableregions);
                    if (!empty($regionIds)) {
                        $data = [
                            'available_region' => $regionIds
                        ];
                    }
                }
            }
        } catch (\Exception $e) {
            $this->logger->info('Fahim_ZipcodeValidator_Observer: '.$e->getMessage());
        }
    }
}
