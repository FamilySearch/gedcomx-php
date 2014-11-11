<?php

namespace Gedcomx\Rs\Client\Util;

class GedcomxPlaceSearchQueryBuilder extends GedcomxBaseSearchQueryBuilder
{
    const NAME          = "name";
    const DATE          = "date";
    const PARENT_ID     = "parentId";
    const TYPE_ID       = "typeId";
    const TYPE_GROUP_ID = "typeGroupId";
    const LATITUDE      = "latitude";
    const LONGITUDE     = "longitude";
    const DISTANCE      = "distance";

    /**
     * @param string $name
     * @param string $value
     * @param string $prefix
     * @param bool $exact
     *
     * @return $this
     */
    private function param($prefix, $name, $value, $exact)
    {
        $this->parameters[] = new SearchParameter($prefix, $name, $value, $exact);
        return $this;
    }

    /**
     * Search where place name equals
     *
     * @param string $value
     * @param bool   $exact
     * @param bool   $required
     *
     * @return $this
     */
    public function name($value, $exact = true, $required = false)
    {
        return $this->param($required ? "+" : null, self::NAME, $value, $exact);
    }

    /**
     * Search where place name is not equal to
     *
     * @param string $value
     *
     * @return $this
     */
    public function nameNot($value)
    {
        return $this->param("-", NAME, $value, false);
    }

    /**
     * Search where place date equals
     *
     * @param string $value
     * @param bool   $exact
     * @param bool   $required
     *
     * @return $this
     */
    public function date($value, $exact = true, $required = false)
    {
        return $this->param($required ? "+" : null, self::DATE, $value, $exact);
    }

    /**
     * Search where place date is not equal to
     *
     * @param string $value
     *
     * @return $this
     */
    public function dateNot($value)
    {
        return $this->param("-", self::DATE, $value, false);
    }

    /**
     * Search where place parentId equals
     *
     * @param string $value
     * @param bool   $exact
     * @param bool   $required
     *
     * @return $this
     */
    public function parentId($value, $exact = true, $required = false)
    {
        return $this->param($required ? "+" : null, self::PARENT_ID, $value, $exact);
    }

    /**
     * Search where place parent id is not equal to
     *
     * @param string $value
     *
     * @return $this
     */
    public function parentIdNot($value)
    {
        return $this->param("-", self::PARENT_ID, $value, false);
    }

    /**
     * Search where place type id equals
     *
     * @param string $value
     * @param bool   $exact
     * @param bool   $required
     *
     * @return $this
     */
    public function typeId($value, $exact = true, $required = false)
    {
        return $this->param($required ? "+" : null, self::TYPE_ID, $value, $exact);
    }

    /**
     * Search where place type id is not equal to
     *
     * @param string $value
     *
     * @return $this
     */
    public function typeIdNot($value)
    {
        return $this->param("-", self::TYPE_ID, $value, false);
    }

    /**
     * Search where place type group id equals
     *
     * @param string $value
     * @param bool   $exact
     * @param bool   $required
     *
     * @return $this
     */
    public function typeGroupId($value, $exact = true, $required = false)
    {
        return $this->param($required ? "+" : null, self::TYPE_GROUP_ID, $value, $exact);
    }

    /**
     * Search where place group id is not equal to
     *
     * @param string $value
     *
     * @return $this
     */
    public function typeGroupIdNot($value)
    {
        return $this->param("-", self::TYPE_GROUP_ID, $value, false);
    }

    /**
     * Search where latitude equals
     *
     * @param string $value
     * @param bool   $exact
     * @param bool   $required
     *
     * @return $this
     */
    public function latitude($value, $exact = true, $required = false)
    {
        return $this->param($required ? "+" : null, self::LATITUDE, $value, $exact);
    }

    /**
     * Search where latitude is not equal to
     *
     * @param string $value
     *
     * @return $this
     */
    public function latitudeNot($value)
    {
        return $this->param("-", self::LATITUDE, $value, false);
    }

    /**
     * Search where longitude equals
     *
     * @param string $value
     * @param bool   $exact
     * @param bool   $required
     *
     * @return $this
     */
    public function longitude($value, $exact = true, $required = false)
    {
        return $this->param($required ? "+" : null, self::LONGITUDE, $value, $exact);
    }

    /**
     * Search where longitdue is not equal to
     *
     * @param string $value
     *
     * @return $this
     */
    public function longitudeNot($value)
    {
        return $this->param("-", self::LONGITUDE, $value, false);
    }

    /**
     * Search where distance equals
     *
     * @param string $value
     * @param bool   $exact
     * @param bool   $required
     *
     * @return $this
     */
    public function distance($value, $exact = true, $required = false)
    {
        return $this->param($required ? "+" : null, self::DISTANCE, $value, $exact);
    }

    /**
     * Search where distance is not equal to
     *
     * @param string $value
     *
     * @return $this
     */
    public function distanceNot($value)
    {
        return $this->param("-", self::DISTANCE, $value, false);
    }

}