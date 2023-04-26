<?php

namespace Fahim\ZipCodeValidator\Controller\Adminhtml\Region;

use Fahim\ZipCodeValidator\Controller\Adminhtml\Region as RegionController;
use Magento\Framework\Controller\ResultFactory;

class Index extends RegionController
{
    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->getConfig()->getTitle()->prepend(__('All Regions'));
        return $resultPage;
    }
}
