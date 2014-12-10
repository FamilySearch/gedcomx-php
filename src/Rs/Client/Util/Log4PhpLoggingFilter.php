<?php

namespace Gedcomx\Rs\Client\Util;

use Gedcomx\Util\Filter;
use Guzzle\Http\Message\Request;
use Guzzle\Http\Message\RequestInterface;
use Logger;

/**
 * Enables log4php logging of REST API requests before the requests are executed.
 * Log4php is used to log information about requests. The information is output as a DEBUG string and the logger is of
 * type \Gedcomx\Rs\Client\Util\Log4PhpLoggingFilter.
 *
 * Class Log4PhpLoggingFilter
 *
 * @package Gedcomx\Rs\Client\Util
 */
class Log4PhpLoggingFilter implements Filter
{
    /**
     * This method uses log4php to output a DEBUG string containing the HTTP method and fully qualified URI that will be executed.
     *
     * @param array|RequestInterface $requests
     * @return array|RequestInterface
     */
    public function apply($requests)
    {
        /** @var Logger $logger */
        $logger = Logger::getLogger("Gedcomx\\Rs\\Client\\Util\\Log4PhpLoggingFilter");
        $reqs = $requests;

        if (!is_array($requests)){
            $reqs = array($requests);
        }

        /** @var Request $request */
        foreach($reqs as $request){
            $logger->debug(sprintf("%s %s", $request->getMethod(), $request->getUrl()));
        }

        return $requests;
    }
}