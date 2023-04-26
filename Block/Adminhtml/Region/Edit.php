<?php

namespace Fahim\ZipCodeValidator\Block\Adminhtml\Region;

class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * Initialize ZipCodeValidator zipcode Edit Block
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_blockGroup = 'Fahim_ZipCodeValidator';
        $this->_controller = 'adminhtml_region';
        parent::_construct();
        if ($this->_isAllowedAction('Fahim_ZipCodeValidator::region')) {
            $this->buttonList->update('save', 'label', __('Save Region'));
        } else {
            $this->buttonList->remove('save');
        }
    }

    /**
     * Retrieve text for header element depending on loaded Region
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        if ($this->_coreRegistry->registry('zipcodevalidator')->getId()) {
            $title = $this->_coreRegistry->registry('zipcodevalidator')->getTitle();
            $title = $this->escapeHtml($title);
            return __("Edit Region '%1'", $title);
        } else {
            return __('New Region');
        }
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
