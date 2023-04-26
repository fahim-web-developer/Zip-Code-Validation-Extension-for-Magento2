<?php

namespace Fahim\ZipCodeValidator\Block\Adminhtml\Zipcode;

class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * Initialize ZipCodeValidator zipcode Edit Block
     *
     * @return void
     */
    protected function _construct()
    {
        $regionId = $this->getRequest()->getParam('region_id');
        $this->_blockGroup = 'Fahim_ZipCodeValidator';
        $this->_controller = 'adminhtml_zipcode';
        parent::_construct();
        if ($this->_isAllowedAction('Fahim_ZipCodeValidator::zipcode')) {
            $this->buttonList->update('save', 'label', __('Save Entry'));
        } else {
            $this->buttonList->remove('save');
        }

        $backUrl = $this->getUrl('*/zipcode/index', ['region_id' => $regionId]);
        $location = "setLocation('".$backUrl."')";
        $this->buttonList->update('back', 'onclick', $location);
        $this->buttonList->remove('delete');
    }

    /**
     * Retrieve text for header element depending on loaded Region
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        if ($this->_coreRegistry->registry('zipcodevalidator_zip')->getId()) {
            $title = $this->_coreRegistry->registry('zipcodevalidator_zip')->getSerialNo();
            $title = $this->escapeHtml($title);
            return __("Edit Entry %1", $title);
        } else {
            return __('New Entry');
        }
    }

    /**
     * Prepare form Html. call the phtm file with form.
     *
     * @return string
     */
    public function getFormHtml()
    {
        $html = parent::getFormHtml();
        $html .= $this->setTemplate('Fahim_ZipCodeValidator::zipcode/edit.phtml')->toHtml();
        return $html;
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
