<?php

namespace Fahim\ZipCodeValidator\Controller\Adminhtml\Region;

use Fahim\ZipCodeValidator\Controller\Adminhtml\Region as RegionController;
use Magento\Framework\Controller\ResultFactory;

class Edit extends RegionController
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
     * @param \Magento\Backend\App\Action\Context            $context
     * @param \Magento\Framework\Registry                    $registry
     * @param \Fahim\ZipCodeValidator\Model\RegionFactory   $region
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $registry,
        \Fahim\ZipCodeValidator\Model\RegionFactory $region
    ) {
        $this->_backendSession = $context->getSession();
        $this->_registry = $registry;
        $this->_region = $region;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $region = $this->_region->create();
        if ($this->getRequest()->getParam('id')) {
            $region->load($this->getRequest()->getParam('id'));
        }
        $data = $this->_backendSession->getFormData(true);
        if (!empty($data)) {
            $region->setData($data);
        }
        $this->_registry->register('zipcodevalidator', $region);
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->getConfig()->getTitle()->prepend(__('Region Entries'));
        $resultPage->getConfig()->getTitle()->prepend(
            $region->getId() ? $region->getTitle() : __('New Region')
        );
        $block = \Fahim\ZipCodeValidator\Block\Adminhtml\Region\Edit::class;
        $content = $resultPage->getLayout()->createBlock($block);
        $resultPage->addContent($content);
        return $resultPage;
    }
}
