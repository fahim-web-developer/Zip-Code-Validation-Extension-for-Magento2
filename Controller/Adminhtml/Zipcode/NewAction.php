<?php

namespace Fahim\ZipCodeValidator\Controller\Adminhtml\Zipcode;

use Fahim\ZipCodeValidator\Controller\Adminhtml\Zipcode as ZipcodeController;
use Magento\Framework\Controller\ResultFactory;

class NewAction extends ZipcodeController
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
