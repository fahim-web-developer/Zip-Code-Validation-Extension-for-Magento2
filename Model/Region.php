<?php

namespace Fahim\ZipCodeValidator\Model;

use Fahim\ZipCodeValidator\Api\Data\RegionInterface;

class Region extends \Magento\Framework\Model\AbstractModel implements RegionInterface
{
    /**
     * No route page id
     */
    const NOROUTE_ENTITY_ID = 'no-route';

    /**
     * Region cache tag
     */
    const CACHE_TAG = 'zipcodevalidator_region';

    /**
     * @var string
     */
    protected $_cacheTag = 'zipcodevalidator_region';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'zipcodevalidator_region';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Fahim\ZipCodeValidator\Model\ResourceModel\Region::class);
    }

    /**
     * Load object data
     *
     * @param int|null $id
     * @param string $field
     * @return $this
     */
    public function load($id, $field = null)
    {
        if ($id === null) {
            return $this->noRouteRegion();
        }
        return parent::load($id, $field);
    }

    /**
     * Object for noRouteRegion
     *
     * @return $this
     */
    public function noRouteRegion()
    {
        return $this->load(self::NOROUTE_ENTITY_ID, $this->getIdFieldName());
    }

    /**
     * Get identities
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * Get ID
     *
     * @return int
     */
    public function getId()
    {
        return parent::getData(self::ID);
    }

    /**
     * Set ID.
     *
     * @param int $id
     *
     * @return \Fahim\ZipCodeValidator\Api\Data\RegionInterface
     */
    public function setId($id)
    {
        return $this->setData(self::ID, $id);
    }

    /**
     * Get Region Name
     *
     * @return int|null
     */
    public function getRegionName()
    {
        return parent::getData(self::REGION_NAME);
    }

    /**
     * Set Region Name
     *
     * @param int $regionName
     * @return \Fahim\ZipCodeValidator\Api\Data\RegionInterface
     */
    public function setRegionName($regionName)
    {
        return $this->setData(self::REGION_NAME, $regionName);
    }

    /**
     * Get Status
     *
     * @return int|null
     */
    public function getStatus()
    {
        return parent::getData(self::STATUS);
    }

    /**
     * Set Status
     *
     * @param int $status
     * @return \Fahim\ZipCodeValidator\Api\Data\RegionInterface
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * Get Created Time
     *
     * @return int|null
     */
    public function getCreatedAt()
    {
        return parent::getData(self::CREATED_AT);
    }

    /**
     * Set Created Time
     *
     * @param int $createdAt
     * @return \Fahim\ZipCodeValidator\Api\Data\RegionInterface
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    /**
     * Get Updated Time
     *
     * @return int|null
     */
    public function getUpdatedAt()
    {
        return parent::getData(self::UPDATED_AT);
    }

    /**
     * Set Updated Time
     *
     * @param int $updatedAt
     * @return \Fahim\ZipCodeValidator\Api\Data\RegionInterface
     */
    public function setUpdatedAt($updatedAt)
    {
        return $this->setData(self::UPDATED_AT, $updatedAt);
    }
}
