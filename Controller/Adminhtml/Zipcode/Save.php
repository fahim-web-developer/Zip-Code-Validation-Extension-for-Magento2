<?php

namespace Fahim\ZipCodeValidator\Controller\Adminhtml\Zipcode;

use Magento\Backend\App\Action;

class Save extends Action
{
    /**
     * @var \Fahim\ZipCodeValidator\Model\RegionFactory
     */
    protected $_region;

    /**
     * @var \Fahim\ZipCodeValidator\Model\ZipcodeFactory
     */
    protected $_zipcode;

    /**
     * @param Action\Context $context
     * @param \Fahim\ZipCodeValidator\Model\RegionFactory $region
     * @param \Fahim\ZipCodeValidator\Model\ZipcodeFactory $zipcode
     */
    public function __construct(
        Action\Context $context,
        \Fahim\ZipCodeValidator\Model\RegionFactory $region,
        \Fahim\ZipCodeValidator\Model\ZipcodeFactory $zipcode
    ) {
        $this->_region = $region;
        $this->_zipcode = $zipcode;
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
     * Save action.
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $data = $this->getRequest()->getParams();
        $zipcodeId = $data['id'] ?? 0;
        $regionId = $data['region_id'];
        $zipcodeCheckId = $this->checkZipcode($data['region_zipcode_from'], $data['region_zipcode_to'], $regionId);
        if ($zipcodeCheckId && $zipcodeCheckId != $zipcodeId) {
            $this->messageManager->addError(
                __(
                    'Zipcode from %1 to %2 already exists.',
                    $data['region_zipcode_from'],
                    $data['region_zipcode_to']
                )
            );
            return $this->resultRedirectFactory->create()->setPath('*/*/index', ['region_id' => $regionId]);
        }
        if (!empty($data['id'])) {
            $this->updateZipcodes();
        } else {
            $data['created_at'] = date('Y-m-d H:i:s');
            $data['updated_at'] = date('Y-m-d H:i:s');
            $data['serial_no'] = $this->getSerialNumber($regionId);
            $region = $this->_zipcode->create()->setData($data)->save();
            $this->messageManager->addSuccess(__('Zipcodes Added Successfully'));
        }
        return $this->resultRedirectFactory->create()->setPath('*/*/index', ['region_id' => $regionId]);
    }

    /**
     * Update Zipcode Details
     *
     * @return array
     */
    public function updateZipcodes()
    {
        $data = $this->getRequest()->getParams();
        $this->_zipcode->create()
            ->load($data['id'])
            ->setRegionZipcodeFrom($data['region_zipcode_from'])
            ->setRegionZipcodeTo($data['region_zipcode_to'])
            ->setUpdatedAt(date('Y-m-d H:i:s'))
            ->save();
        $this->messageManager->addSuccess(__('Zipcodes Updated Successfully'));
    }

    /**
     * Check zipcode already saved or not
     *
     * @param string $zipcodeFrom
     * @param string $zipcodeTo
     * @param integer $regionId
     * @return integer
     */
    public function checkZipcode($zipcodeFrom, $zipcodeTo, $regionId)
    {
        $zipcodeId = 0;
        $collection = $this->_zipcode->create()
            ->getCollection()
            ->addFieldToFilter('region_id', $regionId)
            ->addFieldToFilter('region_zipcode_from', $zipcodeFrom)
            ->addFieldToFilter('region_zipcode_to', $zipcodeTo);
        if ($collection->getSize()) {
            $zipcodeId = $collection->getFirstItem()->getId();
        }
        return $zipcodeId;
    }

    /**
     * Get Serial Number
     *
     * @param integer $regionId
     * @return integer
     */
    public function getSerialNumber($regionId)
    {
        $serialNumber = 0;
        $data = $this->getRequest()->getParams();
        $collection = $this->_zipcode->create()
            ->getCollection()
            ->addFieldToFilter('region_id', $regionId);
        foreach ($collection as $model) {
            if ($model->getSerialNo() > $serialNumber) {
                $serialNumber = $model->getSerialNo();
            }
        }
        return ++$serialNumber;
    }
}
