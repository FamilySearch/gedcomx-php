<?php 

namespace Gedcomx\Rs\Client\Util;

use Gedcomx\Util\Collection;

/**
 * Represents an RDF specific collection.
 *
 * Class RdfCollection
 *
 * @package Gedcomx\Rs\Client\Util
 */
class RdfCollection extends Collection
{
    /**
     * Returns an array of quads with subjects matching the specified URI.
     *
     * @param string $uri
     *
     * @return RdfCollection
     */
    public function quadsMatchingSubject($uri)
    {
        $matchingQuads = array();
        foreach ($this->items as $quad) {
            if ($quad->getSubject()->equals($uri)) {
                $matchingQuads[] = $quad;
            }
        }

        return new static($matchingQuads);
    }

    /**
     * Returns an array of quads with properties matching the specified URI.
     *
     * @param string $uri
     *
     * @return RdfCollection
     */
    public function quadsMatchingProperty($uri)
    {
        $matchingQuads = array();
        foreach ($this->items as $quad) {
            if ($quad->getProperty()->equals($uri)) {
                $matchingQuads[] = $quad;
            }
        }

        return new static($matchingQuads);
    }

    /**
     * Returns the first quad with a subject matching the specified URI.
     *
     * @param string $uri
     *
     * @return \ML\JsonLD\Quad
     */
    public function getSubjectQuad($uri)
    {
        foreach ($this->items as $quad) {
            if ($quad->getSubject()->equals($uri)) {
                return $quad;
            }
        }

        return null;
    }

    /**
     * Returns the first quad with a property matching the specified URI.
     *
     * @param string $uri
     *
     * @return \ML\JsonLD\Quad
     */
    public function getPropertyQuad($uri)
    {
        foreach ($this->items as $quad) {
            if ($quad->getProperty()->equals($uri)) {
                return $quad;
            }
        }

        return null;
    }
}