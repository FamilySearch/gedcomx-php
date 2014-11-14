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
            if ($quad->subject->equals($uri)) {
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
            if ($quad->property->equals($uri)) {
                $matchingQuads[] = $quad;
            }
        }

        return new static($matchingQuads);
    }

    /**
     * @param string $type
     *
     * @return RdfCollection
     */
    public function quadsMatchingObject($type)
    {
        $matchingQuads = array();
        foreach ($this->items as $quad) {
            if ($quad->object->equals($type)) {
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
            if ($quad->subject->equals($uri)) {
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
            if ($quad->property->equals($uri)) {
                return $quad;
            }
        }

        return null;
    }
}