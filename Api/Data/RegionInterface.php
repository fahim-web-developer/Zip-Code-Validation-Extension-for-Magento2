<?php
namespace Fahim\ZipCodeValidator\Api\Data;

/**
 * Zipcode Validator Region interface.
 * @api
 */
interface RegionInterface
{
    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const ID           = 'id';
    /**#@-*/

    const REGION_NAME  = 'region_name';
    
    const STATUS       = 'status';
    
    const CREATED_AT   = 'created_at';
    
    const UPDATED_AT   = 'updated_at';

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId();

    /**
     * Set ID
     *
     * @param int $id
     * @return \Fahim\MpZipCodeValidator\Api\Data\RegionInterface
     */
    public function setId($id);

    /**
     * Get Region Name
     *
     * @return int|null
     */
    public function getRegionName();

    /**
     * Set Region Name
     *
     * @param int $regionName
     * @return \Fahim\MpZipCodeValidator\Api\Data\RegionInterface
     */
    public function setRegionName($regionName);

    /**
     * Get Status
     *
     * @return int|null
     */
    public function getStatus();

    /**
     * Set Status
     *
     * @param int $status
     * @return \Fahim\MpZipCodeValidator\Api\Data\RegionInterface
     */
    public function setStatus($status);

    /**
     * Get Created Time
     *
     * @return int|null
     */
    public function getCreatedAt();

    /**
     * Set Created Time
     *
     * @param int $createdAt
     * @return \Fahim\MpZipCodeValidator\Api\Data\RegionInterface
     */
    public function setCreatedAt($createdAt);

    /**
     * Get Updated Time
     *
     * @return int|null
     */
    public function getUpdatedAt();

    /**
     * Set Updated Time
     *
     * @param int $updatedAt
     * @return \Fahim\MpZipCodeValidator\Api\Data\RegionInterface
     */
    public function setUpdatedAt($updatedAt);
}
