<?php


namespace Gedcomx\Records;

/**
 * An interface enabling the getting and setting an array of fields.
 *
 * Interface HasFields
 *
 * @package Gedcomx\Records
 */
interface HasFields {

    /**
     * Gets the array of fields.
     *
     * @return Field[]
     */
    public function getFields();

    /**
     * Sets the array of fields.
     *
     * @param Field[] $fields
     */
    public function setFields(array $fields);
} 