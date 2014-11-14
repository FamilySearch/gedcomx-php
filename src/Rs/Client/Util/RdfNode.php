<?php

namespace Gedcomx\Rs\Client\Util;

/**
 * A decorator class to wrap the multiple class types that can exist
 * in the ML\JsonLD\Quad object property and give them consistent
 * accessor methods that can handle possible errors in the RDF data.
 *
 * Class RdfNode
 * @package Gedcomx\Rs\Client\Util
 */
class RdfNode
{
    const IRI = 'ML\IRI\IRI';
    const TYPED = 'ML\JsonLD\TypedValue';
    const LANG = 'ML\JsonLD\LanguageTaggedString';

    /**
     * @param mixed $node
     *
     * @return string|null
     */
    public static function getValue($node)
    {
        $class = get_class($node);
        switch ($class) {
            case self::IRI:
                return (string)$node;
            case self::TYPED:
            case self::LANG:
                return $node->getValue();
            default:
                return null;
        }
    }

    /**
     * @param mixed $node
     *
     * @return null|string
     */
    public static function getLanguage($node)
    {
        $class = get_class($node);
        if ($class == self::LANG) {
            return strtolower($node->getLanguage());
        }

        return null;
    }
}