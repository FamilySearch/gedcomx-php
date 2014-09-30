<?php


namespace Gedcomx\Records;


interface HasFields {

    /**
     * @return Field[]
     */
    public function getFields();

    /**
     * @param Field[] $fields
     */
    public function setFields( $fields );
} 