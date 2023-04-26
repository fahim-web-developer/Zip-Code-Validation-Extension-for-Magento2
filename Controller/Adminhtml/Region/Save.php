<?php

namespace Fahim\ZipCodeValidator\Controller\Adminhtml\Region;

use Magento\Backend\App\Action;
use Magento\Framework\Filesystem\Driver\File;

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
     * @var \Magento\MediaStorage\Model\File\UploaderFactory
     */
    protected $_fileUploader;

    /**
     * @param Action\Context                                 $context
     * @param Fahim\ZipCodeValidator\Model\RegionFactory   $region
     * @param Fahim\ZipCodeValidator\Model\ZipcodeFactory  $zipcode
     * @param Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory
     * @param \Magento\Framework\File\Csv $csvReader
     */
    public function __construct(
        Action\Context $context,
        \Fahim\ZipCodeValidator\Model\RegionFactory $region,
        \Fahim\ZipCodeValidator\Model\ZipcodeFactory $zipcode,
        \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory,
        \Magento\Framework\File\Csv $csvReader
    ) {
        $this->_region = $region;
        $this->_zipcode = $zipcode;
        $this->_fileUploader = $fileUploaderFactory;
        $this->_csvReader = $csvReader;
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
        if (!empty($data['region_id'])) {
            $this->updateRegion();
        } else {
            $regionId = 0;
            $collection = $this->_region->create()->getCollection();

            foreach ($collection as $key => $value) {
                if (strcasecmp($data['region_name'], $value->getRegionName()) == 0) {
                    $regionId = $value->getId();
                }
            }
            if (!$regionId) {
                $data['created_at'] = date('Y-m-d H:i:s');
                $data['updated_at'] = date('Y-m-d H:i:s');
                $region = $this->_region->create()->setData($data)->save();
                $regionId = $region->getId();
                $this->messageManager->addSuccess(__('Region Added Successfully'));
            }
            $this->processCsvDataNew($regionId);
        }
        return $this->resultRedirectFactory->create()->setPath('*/*/index');
    }

    /**
     * Update Region Details
     *
     * @return array
     */
    public function updateRegion()
    {
        $data = $this->getRequest()->getParams();
        $files = $this->getRequest()->getFiles();
        $this->_region->create()
            ->load($data['region_id'])
            ->setRegionName($data['region_name'])
            ->setStatus($data['status'])
            ->setUpdatedAt(date('Y-m-d H:i:s'))
            ->save();
        $this->messageManager->addSuccess(__('Region Updated Successfully'));
        if (!empty($files['zipcodes-csv'])
            && !empty($files['zipcodes-csv']['tmp_name'])
        ) {
            $this->processCsvDataNew($data['region_id']);
        }
    }

    /**
     * Process csv data
     *
     * @param integer $regionId
     * @return array
     */
    public function processCsvDataNew($regionId)
    {
        try {
            $paramsData = $this->getRequest()->getParams();

            $csvUploader = $this->_fileUploader->create(['fileId' => 'zipcodes-csv']);
            $csvUploader->setAllowedExtensions(['csv']);
            $result = $csvUploader->validateFile();
            
            $rows = [];
            $file = $result['tmp_name'];
            $fileNameArray = explode('.', $result['name']);
            $ext = end($fileNameArray);
            $ext = strtolower($ext);
            $status = true;
            $headerArray = [
                "zip_from",
                "zip_to"
            ];
            if ($file != '' && $ext == 'csv') {
                $csvFileData = $this->_csvReader->getData($file);
                $count = 0;
                if (!empty($csvFileData) && count($csvFileData) > 1) {
                    $this->importFileCsv($csvFileData, $count, $headerArray, $regionId);
                } elseif (!isset($paramsData['region_id'])) {
                    $check = $this->checkZipcode($data['region_name'], $data['region_name'], $regionId);
                    if (!$check) {
                        $this->_zipcode->create()
                            ->setRegionZipcode($data['region_name'])
                            ->setRegionId($regionId)
                            ->setCreatedAt(date('Y-m-d H:i:s'))
                            ->setUpdatedAt(date('Y-m-d H:i:s'))
                            ->save();
                    }
                }
                if (!empty($rows)) {
                    $this->messageManager->addError(
                        __('Following rows are not valid rows : %1. ', implode(', ', $rows))
                    );
                }
            } else {
                $this->messageManager->addError(__('Please upload CSV file'));
            }
        } catch (\Exception $e) {
            $this->messageManager->addError(__($e->getMessage()));
        }
    }

    /**
     * Csv Header, format check and save
     *
     * @param array $csvFileData
     * @param int $count
     * @param array $headerArray
     * @param int $regionId
     */
    public function importFileCsv($csvFileData, $count, $headerArray, $regionId)
    {
        foreach ($csvFileData as $key => $rowData) {
            if ($count==0) {
                if (!empty($rowData) && count($rowData) < 2) {
                    $this->messageManager->addError(__('CSV file is not a valid file!'));
                    return $this->resultRedirectFactory->create()->setPath('*/*/index');
                } else {
                    $data = $rowData;
                    $status = (empty(array_diff($headerArray, $rowData)) && count($headerArray) == count($rowData));
                    if (!$status) {
                        $this->messageManager->addError(__('Please write the correct header formation of CSV file!'));
                        return $this->resultRedirectFactory->create()->setPath('*/*/index');
                    }
                    ++$count;
                }
            } else {
                $wholedata = [];
                foreach ($rowData as $filekey => $filevalue) {
                    $wholedata[$data[$filekey]] = $filevalue;
                }
                list($updatedWholedata, $errors) = $this->validateCsvDataToSave($wholedata);
                if (empty($errors)) {
                    $this->saveCsvData($updatedWholedata, $regionId, $key);
                } else {
                    $rows[] = $key.': '.$errors[0];
                }
            }
        }
    }

    /**
     * Save csv zipcodes
     *
     * @param array $csvData
     * @param integer $regionId
     * @param integer $row
     * @return array
     */
    public function saveCsvData($csvData, $regionId, $row)
    {
        $msg = [];
        $count = 1;
        $serialNo = $this->getSerialNumber($regionId);
        if ($serialNo && $serialNo > 0) {
            $count = $serialNo;
        }
        
        if (!empty($csvData)
            && !empty($csvData['zip_from'])
            && !empty($csvData['zip_to'])
        ) {
            $zipcodeFrom = $csvData['zip_from'];
            $zipcodeTo = $csvData['zip_to'];
            $check = $this->checkZipcode($zipcodeFrom, $zipcodeTo, $regionId);

            if ($check) {
                $this->messageManager->addError(
                    __(
                        'Skipped row %1. As zipcode from %2 to %3  already exists.',
                        $row,
                        $zipcodeFrom,
                        $zipcodeTo
                    )
                );
                $count--;
            } elseif ($zipcodeFrom && $zipcodeTo) {
                $zipData['region_id'] = $regionId;
                $zipData['region_zipcode_from'] = $zipcodeFrom;
                $zipData['region_zipcode_to'] = $zipcodeTo;
                $zipData['created_at'] = date('Y-m-d H:i:s');
                $zipData['updated_at'] = date('Y-m-d H:i:s');
                $zipData['serial_no'] = $count++;
                $this->saveZipcode($zipData);
            } else {
                $this->messageManager->addError(
                    __('Skipped row %1.', $row)
                );
            }
        }
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
        $collection = $this->_zipcode->create()
            ->getCollection()
            ->addFieldToFilter('region_id', $regionId)
            ->addFieldToFilter('region_zipcode_from', $zipcodeFrom)
            ->addFieldToFilter('region_zipcode_to', $zipcodeTo);
        return count($collection);
    }

    /**
     * Save zip code
     *
     * @param array $data
     * @return void
     */
    public function saveZipcode($data)
    {
        $this->_zipcode->create()->setData($data)->save();
    }
    
    /**
     * Validate csv file
     *
     * @param array $csvData
     * @return integer
     */
    public function validateCSV($csvData)
    {
        $value = 0;

        foreach ($csvData as $key => $zipcode) {
            if (is_numeric($key) && count($zipcode) == 2) {
                $value = 0;
            } else {
                $value = 1;
            }
            break;
        }
        return $value;
    }

    /**
     * Get Serial number of region
     *
     * @param integer $regionId
     * @return integer
     */
    public function getSerialNumber($regionId)
    {
        $collection = $this->_zipcode->create()
            ->getCollection()
            ->addFieldToFilter('region_id', $regionId)
            ->getLastItem();
        if ($collection->getSerialNo()) {
            return $collection->getSerialNo()+1;
        }
    }

    /**
     * Validate Csv Data before Saving
     *
     * @param array $wholedata
     * @return array
     */
    public function validateCsvDataToSave($wholedata)
    {
        $data = [];
        $errors = [];
        if (count($wholedata) < 2) {
            if (empty($wholedata['zip_from'])) {
                $errors[] = __('zip_from field can not be empty');
            } elseif (empty($wholedata['zip_to'])) {
                $errors[] = __('zip_to field can not be empty');
            } else {
                $errors[] = __('invalid format');
            }
        } else {
            foreach ($wholedata as $key => $value) {
                switch ($key) {
                    case 'zip_from':
                        if ($value == '') {
                            $errors[] = __('zip_from field can not be empty');
                        } else {
                            $data[$key] = $value;
                        }
                        break;
                    case 'zip_to':
                        if ($value == '') {
                            $errors[] = __('zip_to field can not be empty');
                        } elseif (isset($data['zip_from']) && $data['zip_from'] > $value) {
                            $errors[] = __('zip_to field should be greater then zip_from field');
                        } else {
                            $data[$key] = $value;
                        }
                        break;
                }
            }
        }
        return [$data, $errors];
    }
}
