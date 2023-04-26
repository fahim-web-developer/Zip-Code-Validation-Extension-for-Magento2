<?php

namespace Fahim\ZipCodeValidator\Model\Config\Source;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;

/**
 * Model RegionOptions class
 */
class RegionOptions extends AbstractSource
{
    /**
     * Region Collection
     *
     * @var \Fahim\ZipCodeValidator\Model\ResourceModel\Region\CollectionFactory
     */
    protected $_regionCollection;

    /**
     * Constructor
     *
     * @param \Fahim\ZipCodeValidator\Model\ResourceModel\Region\CollectionFactory $regionCollectionFactory
     */
    public function __construct(
        \Fahim\ZipCodeValidator\Model\ResourceModel\Region\CollectionFactory $regionCollectionFactory
    ) {
        $this->_regionCollection = $regionCollectionFactory;
    }
    
    /**
     * Get all options
     *
     * @return array
     */
    public function getAllOptions()
    {
        $collections = $this->_regionCollection->create()
            ->addFieldToFilter('status', 1);
        if ($this->_options === null) {
            if ($collections->getSize()) {
                foreach ($collections as $region) {
                    $this->_options[] = [
                        'label' => __($region->getRegionName()),
                        'value' => $region->getId(),
                    ];
                }
            } else {
                $this->_options[] = [
                    'label' => __('No region available'),
                    'value' => 0,
                ];
            }
        }
        return $this->_options;
    }
}
