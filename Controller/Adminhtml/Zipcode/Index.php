<?php

namespace Fahim\ZipCodeValidator\Controller\Adminhtml\Zipcode;

use Fahim\ZipCodeValidator\Controller\Adminhtml\Zipcode as ZipcodeController;
use Magento\Framework\Controller\ResultFactory;

class Index extends ZipcodeController
{
    /**
     * @var \Fahim\ZipCodeValidator\Model\Region
     */
    protected $_region;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Fahim\ZipCodeValidator\Model\Region $region
     * @param \Magento\Backend\Model\Session $backendSession
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Fahim\ZipCodeValidator\Model\Region $region,
        \Magento\Backend\Model\Session $backendSession
    ) {
        $this->_region = $region;
        $this->backendSession = $backendSession;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $request = $this->getRequest();
        $regionId = $request->getParam('region_id');
        $this->backendSession->setViewZipRegionId($regionId);

        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $regionId = $this->getRequest()->getParam('region_id');
        $regionName = $this->_region->load($regionId)->getRegionName();
        $resultPage->getConfig()->getTitle()->prepend(__($regionName.' Zipcode List'));
        return $resultPage;
    }
}
