<?php


namespace Gedcomx\Rs\Client\Exception;

use GuzzleHttp\Psr7\Response;

/**
 * Represents an exception within the FamilySearch GEDCOM X application.
 *
 * Class GedcomxApplicationException
 *
 * @package Gedcomx\Rs\Client\Exception
 */
class GedcomxApplicationException extends \Exception
{
    /**
     * The response associated with the exception if applicable.
     *
     * @var Response
     */
    protected $response;

    /**
     * Constructs a new GEDCOM X application exception.
     *
	 * @param string $message [optional]
	 * @param Response $response [optional]
	 * @param null $previous
	 */
    function __construct($message = "", $response = null, $previous = null)
    {
        $code = ($response == null ? 0 : $response->getStatusCode());
        parent::__construct($message, $code, $previous);
        $this->response = $response;
    }

    /**
     * Gets the response associated with the exception if applicable.
     *
     * @return \GuzzleHttp\Psr7\Response
     */
    public function getResponse()
    {
        return $this->response;
    }
}