<?php

namespace Gedcomx\Rs\Client\Util;

use Gedcomx\Gedcomx;
use Gedcomx\Rs\Client\Rel;

class EmbeddedLinkLoader
{
    private $defaultEmbeddedLinkRels;

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