<?php


namespace Gedcomx\Rs\Client\Exception;

use Guzzle\Http\Message\Response;


class GedcomxApplicationException extends \Exception
{

    /**
     * @var Response
     */
    protected $response;

	/**
	 * @param string $message [optional]
	 * @param Response $response [optional]
	 * @param null $previous
	 */
    function __construct($message = "", $response = null, $previous = null)
    {
        parent::__construct($message, 0, $previous);
        $this->response = $response;
    }

    /**
     * @return \Guzzle\Http\Message\Response
     */
    public function getResponse()
    {
        return $this->response;
    }


}