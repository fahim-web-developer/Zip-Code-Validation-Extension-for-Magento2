<?php

namespace Fahim\ZipCodeValidator\Controller\Adminhtml;

use Magento\Backend\App\Action;

abstract class Region extends Action
{
    /**
     * Check for is allowed
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Fahim_ZipCodeValidator::region');
    }
}
