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
        $code = ($response == null ? 0 : $response->getStatusCode());
        parent::__construct($message, $code, $previous);
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