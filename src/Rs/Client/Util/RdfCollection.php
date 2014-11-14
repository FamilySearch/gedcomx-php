<?php 

namespace Gedcomx\Rs\Client\Util;

use Gedcomx\Util\Collection;

class RdfCollection extends Collection
{
    /**
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