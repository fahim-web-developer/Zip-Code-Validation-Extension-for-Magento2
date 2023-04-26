<?php

namespace Fahim\ZipCodeValidator\Controller\Adminhtml\Region;

use Magento\Backend\App\Action\Context;

class Delete extends \Magento\Backend\App\Action
{
    /**
     * @param Context $context
     * @param \Fahim\ZipCodeValidator\Model\Region $region
     * @param \Fahim\ZipCodeValidator\Model\ResourceModel\Zipcode\CollectionFactory $zipcodeCollection
     */
    public function __construct(
        Context $context,
        \Fahim\ZipCodeValidator\Model\Region $region,
        \Fahim\ZipCodeValidator\Model\ResourceModel\Zipcode\CollectionFactory $zipcodeCollection
    ) {
        $this->region = $region;
        $this->zipcodeCollection = $zipcodeCollection;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Fahim_ZipCodeValidator::region');
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        try {
            $data = $this->getRequest()->getParams();
            if (!empty($data['id'])) {
                $id = $data['id'];
                $region = $this->region->load($id);
                if ($region && $region->getId() == $id) {
                    $region->delete();

                    $zipcodeCollection = $this->zipcodeCollection->create()
                        ->addFieldToFilter('region_id', $id);

                    if ($zipcodeCollection->getSize()) {
                        $zipcodeCollection->walk('delete');
                    }
                    $this->messageManager->addSuccess(__('Region deleted successfully'));
                } else {
                    $this->messageManager->addError(__("Region with '%1' Id does not exist", $id));
                }
            } else {
                $this->messageManager->addError(__('Region Id is Invalid'));
            }
        } catch (\Exception $e) {
            $this->messageManager->addError(__('Something went wrong !!!'));
        }
        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath('*/*/');
    }
}
