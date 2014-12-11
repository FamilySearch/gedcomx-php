<?php

namespace Gedcomx\Rs\Client\Util;

use Gedcomx\Gedcomx;
use Gedcomx\Rs\Client\Rel;

/**
 * This class extracts specific links from Gedcomx::getPersons() and Gedcomx::getRelationships(). Each of these are lists of instances
 * that derive from SupportsLinks, thus the links are pulled directly from SupportsLinks getLinks().
 * It is important to note that not all links will be extracted. Only the following will be extracted by default:
 * * CHILD_RELATIONSHIPS
 * * CONCLUSIONS
 * * EVIDENCE_REFERENCES
 * * MEDIA_REFERENCES
 * * NOTES
 * * PARENT_RELATIONSHIPS
 * * SOURCE_REFERENCES
 * * SPOUSE_RELATIONSHIPS
 *
 * Class EmbeddedLinkLoader
 *
 * @package Gedcomx\Rs\Client\Util
 */
class EmbeddedLinkLoader
{
    private $defaultEmbeddedLinkRels;

    /**
     * Constructs a new instance of EmbeddedLinkLoader with default links.
     */
    public function __construct()
    {
        $this->defaultEmbeddedLinkRels = array(
            Rel::CHILD_RELATIONSHIPS,
            Rel::CONCLUSIONS,
            Rel::EVIDENCE_REFERENCES,
            Rel::MEDIA_REFERENCES,
            Rel::NOTES,
            Rel::PARENT_RELATIONSHIPS,
            Rel::SOURCE_REFERENCES,
            Rel::SPOUSE_RELATIONSHIPS
        );
    }

    /**
     * Gets the list of embedded links that will be extracted when loadEmbeddedLinks() is called.
     *
     * @return array
     */
    protected function getEmbeddedLinkRels()
    {
        return $this->defaultEmbeddedLinkRels;
    }

    /**
     * Return all the link objects for embedded resources
     *
     * @param Gedcomx $entity
     *
     * @return array
     */
    public function loadEmbeddedLinks(Gedcomx $entity)
    {
        $embeddedLinks = array();
        $embeddedRels = $this->getEmbeddedLinkRels();

        $persons = $entity->getPersons();
        if ($persons != null) {
            foreach ($persons as $person) {
                foreach ($embeddedRels as $rel) {
                    $link = $person->getLink($rel);
                    if ($link != null) {
                        $embeddedLinks[] = $link;
                    }
                }
            }
        }

        $relationships = $entity->getRelationships();
        if ($relationships != null) {
            foreach ($relationships as $relationship) {
                foreach ($embeddedRels as $rel) {
                    $link = $relationship->getLink($rel);
                    if ($link != null) {
                        $embeddedLinks[] = $link;
                    }
                }
            }
        }

        return $embeddedLinks;
    }
}