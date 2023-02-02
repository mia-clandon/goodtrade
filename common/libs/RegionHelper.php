<?php

namespace common\libs;

use common\libs\traits\Singleton;
use common\models\Location;

/**
 * Class RegionHelper
 * @package common\libs
 * @author Артём Широких kowapssupport@gmail.com
 */
class RegionHelper {
    use Singleton;

    /** @var bool - использовать ли страну. */
    private $use_country = true;
    /** @var bool - использовать ли город. */
    private $use_city = true;
    /** @var bool - использовать ли регион. */
    private $use_region = true;
    /** @var bool - использовать ли адрес. */
    private $use_location = true;

    /** @var null|int */
    private $country_id;
    /** @var null|int */
    private $city_id;
    /** @var null|int */
    private $region_id;

    /** @var string|null - адрес. */
    private $address;

    /**
     * Собирает информацию о местонахождении.
     * @return string
     */
    public function get() {
        $location_parts = [];
        $location_model = new Location();
        // использовать ли на выводе название страны.
        if ($this->use_country && !is_null($this->country_id)) {
            $location_parts[] = $location_model->getCountryNameById($this->country_id);
        }
        // использовать ли область / регион.
        if ($this->use_region && !is_null($this->region_id)) {
            $location_parts[] = $location_model->getRegionNameById($this->region_id);
        }
        // использовать ли город.
        if ($this->use_city && !is_null($this->city_id)) {
            $location_parts[] = $location_model->getCityNameById($this->city_id);
        }
        // использовать ли адрес.
        if ($this->use_location && !empty($this->address)) {
            $location_parts[] = (string)$this->address;
        }
        return implode(', ', $location_parts);
    }

    /**
     * @param bool $flag
     * @return $this
     */
    public function setUseCity($flag = true) {
        $this->use_city = (bool)$flag;
        return $this;
    }

    /**
     * @param bool $flag
     * @return $this
     */
    public function setUseRegion($flag = true) {
        $this->use_region = (bool)$flag;
        return $this;
    }

    /**
     * @param bool $flag
     * @return $this
     */
    public function setUseLocation($flag = true) {
        $this->use_location = (bool)$flag;
        return $this;
    }

    /**
     * @param bool $flag
     * @return $this
     */
    public function setUseCountry($flag = true) {
        $this->use_country = (bool)$flag;
        return $this;
    }

    /**
     * @param string $address
     * @return $this
     */
    public function setAddress($address) {
        if (!empty($address)) {
            $this->address = (string)$address;
        }
        return $this;
    }

    /**
     * @param int $region_id
     * @return $this
     */
    public function setRegionId($region_id) {
        $region_id = (int)$region_id;
        if ($region_id) {
            $this->region_id = (int)$region_id;
        }
        return $this;
    }

    /**
     * @param int $city_id
     * @return $this
     */
    public function setCityId($city_id) {
        $city_id = (int)$city_id;
        if ($city_id) {
            $this->city_id = (int)$city_id;
        }
        return $this;
    }

    /**
     * @param int $country_id
     * @return $this
     */
    public function setCountryId($country_id) {
        $country_id = (int)$country_id;
        if ($country_id) {
            $this->country_id = (int)$country_id;
        }
        return $this;
    }
}