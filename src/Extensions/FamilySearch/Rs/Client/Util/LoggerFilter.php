<?php

namespace Gedcomx\Extensions\FamilySearch\Rs\Client\Util;

use Gedcomx\Util\Filter;
use Guzzle\Http\Message\RequestInterface;
use Guzzle\Common\Event;
use Psr\Log\LoggerInterface;

/**
 * This filter enables a logger to be notified of all HTTP requests sent
 * and responses recieved from the FamilySearch API.
 *
 * Class LoggerFilter
 *
 * @package Gedcomx\Extensions\FamilySearch\Rs\Client\Util
 */
class LoggerFilter implements Filter
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * Constructs a new logger filter with the specified logger.
     *
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct($logger)
    {
        $this->logger = $logger;
    }

    /**
     * This method enables the logger to watch the specified requests.
     *
     * @param array|RequestInterface $requests
     * @return array|RequestInterface
     */
    public function apply($requests)
    {
        if (is_array($requests)) {
            foreach($requests as $request) {
                $this->apply($request);
            }
        } else {
            
            // Log request url and headers
            $this->logger->info($requests->getUrl());
            $this->logger->debug($requests->getRawHeaders());
            
            $logger = $this->logger;
            
            $requests->getEventDispatcher()->addListener('request.complete', function(Event $e) use($logger) {
                
                $response = $e['response'];
                
                // Log response headers
                $logger->debug($response->getRawHeaders());
                
                // Log warning header
                if($e['response']->getHeader('warning')){
                    $logger->warning($response->getHeader('warning'));
                }
                
                // Log 400 errors
                if($response->isClientError()){
                    $logger->error($response->getStatusCode() . ' ' . $response->getReasonPhrase());
                }
                
                // Log 500 errors
                if($response->isServerError()){
                    $logger->critical($response->getStatusCode() . ' ' . $response->getReasonPhrase() . "\n" . $response->getBody());
                }
                
                // Log 503s
                if($response->getStatusCode() == 503){
                    $logger->alert($response->getStatusCode() . ' ' . $response->getReasonPhrase());
                }
            });
        }
        
        return $requests;
    }
}