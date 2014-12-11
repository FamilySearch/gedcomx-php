<?php 

namespace Gedcomx\Rs\Client\Exception;

/**
 * An exception representing an invalid query parameter.
 *
 * Class GedcomxInvalidQueryParameter
 *
 * @package Gedcomx\Rs\Client\Exception
 */
class GedcomxInvalidQueryParameter extends \Exception
{
    /**
     * Constructs a new invalid query parameter exception using the specified mixed variable.
     *
     * @param mixed $var
     */
    public function __construct($var){
        if (is_object($var)) {
            $type = get_class($var);
        } else {
            $type = gettype($var);
        }

        parent::__construct("Unknown query parameter type: {$type}");
    }

}