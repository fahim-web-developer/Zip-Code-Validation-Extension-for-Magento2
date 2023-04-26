<?php
namespace Fahim\ZipCodeValidator\Block\Adminhtml;

use Magento\Backend\Block\Widget\Grid\Container;

class Region extends Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_region';
        $this->_blockGroup = 'Fahim_ZipCodeValidator';
        $this->_headerText = __('Manage Region Entry');
        parent::_construct();
    }

    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}
