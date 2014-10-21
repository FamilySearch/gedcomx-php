<?php


namespace Gedcomx\Support;


interface HasJsonKey {
    /**
     * Whether the json-keyed value is supposed to have a unique key in the list.
     *
     * @return Whether the json-keyed value is supposed to have a unique key in the list.
     */
    public function isHasUniqueKey();

    /**
     * The JSON key in the map for this object.
     *
     * @return The key in the map.
     */
    public function getJsonKey();

    /**
     * The JSON key in the map for this object.
     *
     * @param string jsonKey The key in the map.
     */
    public function setJsonKey($jsonKey);

}