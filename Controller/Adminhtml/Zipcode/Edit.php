<?php

namespace Fahim\ZipCodeValidator\Controller\Adminhtml\Zipcode;

use Fahim\ZipCodeValidator\Controller\Adminhtml\Zipcode as ZipcodeController;
use Magento\Framework\Controller\ResultFactory;

class Edit extends ZipcodeController
{
    /**
     * @var \Magento\Backend\Model\Session
     */
    protected $_backendSession;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_registry;

    /**
     * @var \Fahim\ZipCodeValidator\Model\RegionFactory
     */
    protected $_region;

    /**
     * @var \Fahim\ZipCodeValidator\Model\ZipcodeFactory
     */
    protected $_zipcode;

    /**
     * @param \Magento\Backend\App\Action\Context            $context
     * @param \Magento\Framework\Registry                    $registry
     * @param \Fahim\ZipCodeValidator\Model\RegionFactory   $region
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $registry,
        \Fahim\ZipCodeValidator\Model\RegionFactory $region,
        \Fahim\ZipCodeValidator\Model\ZipcodeFactory $zipcode
    ) {
        $this->_backendSession = $context->getSession();
        $this->_registry = $registry;
        $this->_region = $region;
        $this->_zipcode = $zipcode;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $regionId = $this->getRequest()->getParam('region_id');
        $zipcode = $this->_zipcode->create();
        if ($this->getRequest()->getParam('id')) {
            $zipcode->load($this->getRequest()->getParam('id'));
        }
        $data = $this->_backendSession->getFormData(true);
        if (!empty($data)) {
            $zipcode->setData($data);
        }
        $this->_registry->register('zipcodevalidator_zip', $zipcode);
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->getConfig()->getTitle()->prepend(__('Zipcode Entry'));
        $resultPage->getConfig()->getTitle()->prepend(
            $zipcode->getId() ? __("Edit Entry %1", $zipcode->getSerialNo()) : __('New Entry')
        );
        $block = \Fahim\ZipCodeValidator\Block\Adminhtml\Zipcode\Edit::class;
        $content = $resultPage->getLayout()->createBlock($block);
        $resultPage->addContent($content);
        return $resultPage;
    }
}
