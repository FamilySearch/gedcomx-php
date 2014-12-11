<?php

namespace Gedcomx\GedcomxFile;

use Gedcomx\Extensions\FamilySearch\FamilySearchPlatform;
use Gedcomx\Gedcomx;

/**
 * A class for performing JSON serialization and deserialization.
 *
 * Class DefaultJsonSerialization
 *
 * @package Gedcomx\GedcomxFile
 */
class DefaultJsonSerialization implements GedcomxEntrySerializer, GedcomxEntryDeserializer
{
    /**
     * Deserialize the resource from the specified input stream.
     *
     * @param string $incoming The text to deserialize.
     *
     * @return mixed the resource.
     */
    public function deserialize($incoming)
    {
        $resources = null;

        $json = json_decode($incoming);
        foreach ($json as $key => $value){
            if (JsonMapper::isKnownType($key)) {
                $class = XmlMapper::getClassName($key);
                $resources[] = new $class($value);
            }
        }

        return $resources;

    }

    /**
     * Serialize the resource to the specified output stream.
     *
     * @param mixed $resource
     *
     * @return void
     */
    public function serialize($resource)
    {
        return $resource->toJson();
    }

    /**
     * Whether the specified content type is a known content type and therefore
     * does not need to be written to the entry attributes.
     *
     * @param string $contentType The content type.
     *
     * @return boolean Whether the content type is "known".
     */
    public function isKnownContentType($contentType)
    {
        return in_array($contentType, array(
            Gedcomx::JSON_MEDIA_TYPE,
            FamilySearchPlatform::JSON_MEDIA_TYPE,
            FamilySearchPlatform::JSON_APPLICATION_TYPE
        ));
}}