<?php


namespace Gedcomx\Rs\Api;

use GuzzleHttp\Message\Response;


class GedcomxApplicationException extends \Exception
{

    /**
     * @var Response
     */
    protected $response;

    /**
     * @param string $message [optional]
     * @param Response $response [optional]
     */
    function __construct($message = "", $response = NULL, $previous = null)
    {
        parent::__construct($message, 0, $previous);
        $this->response = $response;
    }

    /**
     * @return \GuzzleHttp\Message\Response
     */
    public function getResponse()
    {
        return $this->response;
    }


}