<?php 

namespace Gedcomx\Rs\Client\Exception;

class GedcomxInvalidQueryParameter extends \Exception
{
    /**
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