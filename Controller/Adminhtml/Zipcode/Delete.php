<?php

namespace Fahim\ZipCodeValidator\Controller\Adminhtml\Zipcode;

use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Fahim\ZipCodeValidator\Model\ResourceModel\Zipcode\CollectionFactory;

class Delete extends \Magento\Backend\App\Action
{
    /**
     * @var Filter
     */
    protected $_filter;

    /**
     * @var CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param \Magento\Backend\Model\Session $backendSession
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        \Magento\Backend\Model\Session $backendSession
    ) {
        $this->_filter = $filter;
        $this->_collectionFactory = $collectionFactory;
        $this->backendSession = $backendSession;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Fahim_ZipCodeValidator::zipcode');
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $regionId = $this->backendSession->getViewZipRegionId();
        $this->backendSession->unsViewZipRegionId();

        $collection = $this->_filter->getCollection($this->_collectionFactory->create());
        $collection->addFieldToFilter('region_id', $regionId);
        $regionIds = array_unique($collection->getColumnValues('region_id'));
        $collection->walk('delete');
        if (count($regionIds)==1) {
            $regionId = implode("", $regionIds);
        }
        $this->messageManager->addSuccess(__('Zipcode deleted successfully'));
        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath('*/*/index', ['region_id' => $regionId]);
    }
}
