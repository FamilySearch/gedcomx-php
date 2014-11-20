<?php

namespace Gedcomx\Rs\Client\Util;

use Gedcomx\Util\Filter;
use Guzzle\Http\Message\Request;
use Guzzle\Http\Message\RequestInterface;
use Logger;

class Log4PhpLoggingFilter implements Filter
{
    /**
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