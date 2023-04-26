<?php

namespace Fahim\ZipCodeValidator\Block\Adminhtml\Zipcode\Edit;

class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * Init form
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('zipcode_form');
        $this->setTitle(__('Zipcode Information'));
    }

    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        $regionId = $this->getRequest()->getParam('region_id');
        $model = $this->_coreRegistry->registry('zipcodevalidator_zip');
        $form = $this->_formFactory->create(['data' => [
            'id' => 'edit_form',
            'enctype' => 'multipart/form-data',
            'action' => $this->getData('action'),
            'method' => 'post']
        ]);
        $form->setHtmlIdPrefix('zipcode_');
        $fieldset = $form->addFieldset(
            'base_fieldset',
            [
                'legend' => __('Zipcode Information'),
                'class' => 'fieldset-wide'
            ]
        );
        if ($model->getId()) {
            $fieldset->addField('id', 'hidden', ['name' => 'id']);
        }
        $fieldset->addField(
            'region_zipcode_from',
            'text',
            [
                'name' => 'region_zipcode_from',
                'label' => __('Zipcode From'),
                'title' => __('Zipcode From'),
                'required' => true,
                'class' => 'validate-no-html-tags'
            ]
        );
        $fieldset->addField(
            'region_zipcode_to',
            'text',
            [
                'name' => 'region_zipcode_to',
                'label' => __('Zipcode To'),
                'title' => __('Zipcode To'),
                'required' => true,
                'class' => 'validate-no-html-tags'
            ]
        );
        $form->setValues($model->getData());
        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
