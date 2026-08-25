<?php
/**
 * Copyright Intellectual Reserve, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Gedcomx\Extensions\FamilySearch\Platform\Places;

/**
 * A place attribute.
 *
 * This is a simple data object with no serialization annotations.
 *
 * @package Gedcomx\Extensions\FamilySearch\Platform\Places
 */
class PlaceAttribute
{
    /**
     * The attribute ID.
     *
     * @var string
     */
    private $attributeId;

    /**
     * The type name.
     *
     * @var string
     */
    private $typeName;

    /**
     * The type ID.
     *
     * @var string
     */
    private $typeId;

    /**
     * The description ID.
     *
     * @var string
     */
    private $descriptionId;

    /**
     * The value.
     *
     * @var string
     */
    private $value;

    /**
     * The year.
     *
     * @var integer
     */
    private $year;

    /**
     * The locale.
     *
     * @var string
     */
    private $locale;

    /**
     * Get the attribute ID.
     *
     * @return string
     */
    public function getAttributeId()
    {
        return $this->attributeId;
    }

    /**
     * Set the attribute ID.
     *
     * @param string $attributeId
     */
    public function setAttributeId($attributeId)
    {
        $this->attributeId = $attributeId;
    }

    /**
     * Get the type name.
     *
     * @return string
     */
    public function getTypeName()
    {
        return $this->typeName;
    }

    /**
     * Set the type name.
     *
     * @param string $typeName
     */
    public function setTypeName($typeName)
    {
        $this->typeName = $typeName;
    }

    /**
     * Get the type ID.
     *
     * @return string
     */
    public function getTypeId()
    {
        return $this->typeId;
    }

    /**
     * Set the type ID.
     *
     * @param string $typeId
     */
    public function setTypeId($typeId)
    {
        $this->typeId = $typeId;
    }

    /**
     * Get the description ID.
     *
     * @return string
     */
    public function getDescriptionId()
    {
        return $this->descriptionId;
    }

    /**
     * Set the description ID.
     *
     * @param string $descriptionId
     */
    public function setDescriptionId($descriptionId)
    {
        $this->descriptionId = $descriptionId;
    }

    /**
     * Get the value.
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set the value.
     *
     * @param string $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * Get the year.
     *
     * @return integer
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * Set the year.
     *
     * @param integer $year
     */
    public function setYear($year)
    {
        $this->year = $year;
    }

    /**
     * Get the locale.
     *
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * Set the locale.
     *
     * @param string $locale
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
    }
}
