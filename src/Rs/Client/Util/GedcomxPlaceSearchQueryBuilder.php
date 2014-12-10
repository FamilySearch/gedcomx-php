<?php

namespace Gedcomx\Rs\Client\Util;

/**
 * This is a helper utility for building syntactically correct place search query strings.
 *
 * Class GedcomxPlaceSearchQueryBuilder
 *
 * @package Gedcomx\Rs\Client\Util
 */
class GedcomxPlaceSearchQueryBuilder extends GedcomxBaseSearchQueryBuilder
{
    /**
     * The name parameter in place search queries.
     */
    const NAME          = "name";
    /**
     * The date parameter in place search queries.
     */
    const DATE          = "date";
    /**
     * The parent parameter in place search queries.
     */
    const PARENT_ID     = "parentId";
    /**
     * The type parameter in place search queries.
     */
    const TYPE_ID       = "typeId";
    /**
     * The type group parameter in place search queries.
     */
    const TYPE_GROUP_ID = "typeGroupId";
    /**
     * The latitude parameter in place search queries.
     */
    const LATITUDE      = "latitude";
    /**
     * The longitude parameter in place search queries.
     */
    const LONGITUDE     = "longitude";
    /**
     * The distance parameter in place search queries.
     */
    const DISTANCE      = "distance";

    /**
     * Creates a generic search parameter with the specified name and value.
     *
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