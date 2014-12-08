<?php

namespace Gedcomx\Util;

/**
 * Class JsonMapper
 *
 * @package Gedcomx\Util
 *
 *          Stores the mapping between class names and JSON attribute names
 */
class JsonMapper
{
    private static $collection;

    /**
     * Initialize the collection object with the map
     */
    private static function init(){
        self::$collection = new Collection(array(
            'Gedcomx\Gedcomx' => 'gedcomx',
            'Gedcomx\Agent\Agent' => 'agents',
            'Gedcomx\Atom\Entry' => 'entries',
            'Gedcomx\Common\Attribution' => 'attribution',
            'Gedcomx\Common\CustomKeyedItem' => 'customKeys',
            'Gedcomx\Common\EvidenceReference' => 'evidence',
            'Gedcomx\Common\Note' => 'notes',
            'Gedcomx\Common\ResourceReference' => 'resourceReference',
            'Gedcomx\Common\UniqueCustomKeyedItem' => 'ucustomKeys',
            'Gedcomx\Conclusion\Document' => 'documents',
            'Gedcomx\Conclusion\Event' => 'events',
            'Gedcomx\Conclusion\Fact' => 'facts',
            'Gedcomx\Conclusion\Gender' => 'genders',
            'Gedcomx\Conclusion\Name' => 'names',
            'Gedcomx\Conclusion\Person' => 'persons',
            'Gedcomx\Conclusion\Relationship' => 'relationships',
            'Gedcomx\Extensions\FamilySearch\FamilySearchPlatform' => 'familysearch',
            'Gedcomx\Extensions\FamilySearch\Platform\Error' => 'errors',
            'Gedcomx\Extensions\FamilySearch\Platform\HealthConfig' => 'healthConfig',
            'Gedcomx\Extensions\FamilySearch\Platform\Tag' => 'tags',
            'Gedcomx\Extensions\FamilySearch\Platform\Artifacts\ArtifactMetadata' => 'artifactMetaData',
            'Gedcomx\Extensions\FamilySearch\Platform\Discussions\Discussion' => 'discussions',
            'Gedcomx\Extensions\FamilySearch\Platform\Tree\ChangeInfo' => 'changeInfo',
            'Gedcomx\Extensions\FamilySearch\Platform\Tree\ChildAndParentsRelationship' => 'child-and-parents-relationships',
            'Gedcomx\Extensions\FamilySearch\Platform\Tree\DiscussionReference' => 'discussion-references',
            'Gedcomx\Extensions\FamilySearch\Platform\Tree\MatchInfo' => 'matchInfo',
            'Gedcomx\Extensions\FamilySearch\Platform\Tree\Merge' => 'merge',
            'Gedcomx\Extensions\FamilySearch\Platform\Tree\MergeAnalysis' => 'mergeAnalysis',
            'Gedcomx\Extensions\FamilySearch\Platform\Tree\mergeConflict' => 'mergeConflict',
            'Gedcomx\Extensions\FamilySearch\Platform\Users\User' => 'users',
            'Gedcomx\Links\Link' => 'links',
            'Gedcomx\Records\Collection' => 'collections',
            'Gedcomx\Records\CollectionContent' => 'collectionContent',
            'Gedcomx\Records\Field' => 'fields',
            'Gedcomx\Records\RecordDescription' => 'recordDescriptors',
            'Gedcomx\Records\RecordSet' => 'records',
            'Gedcomx\Source\Coverage' => 'coverage',
            'Gedcomx\Source\SourceDescription' => 'sourceDescriptions',
            'Gedcomx\Source\SourceReference' => 'sourceReferences',
        ));
    }

    /**
     * Get the collection or initialize it if empty.
     *
     * @return Collection
     */
    private static function collection(){
        if (self::$collection == null) {
            self::init();
        }

        return self::$collection;
    }

    /**
     * Return whether or not we recognize the tag name
     *
     * @param string $key
     *
     * @return bool
     */
    public static function isKnownType($key)
    {
        return self::collection()->contains($key);
    }

    /**
     * Return the JSON attribute name for a given class name
     *
     * @param $class
     *
     * @return string
     */
    public static function getJsonKey($class)
    {
        return self::collection()->get($class);
    }

    /**
     * Return the class name associated with a JSON attribute name
     *
     * @param $json
     *
     * @return mixed
     */
    public static function getClassName($json)
    {
        return self::collection()->getKey($json);
    }
}