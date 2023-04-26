<?php

namespace Fahim\ZipCodeValidator\Controller\Adminhtml\Region;

use Fahim\ZipCodeValidator\Controller\Adminhtml\Region as RegionController;
use Magento\Framework\Controller\ResultFactory;

class NewAction extends RegionController
{
    /**
     * @return \Magento\Backend\Model\View\Result\Forward
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Forward $result */
        $result = $this->resultFactory->create(ResultFactory::TYPE_FORWARD);
        $result->forward('edit');
        return $result;
    }
}
