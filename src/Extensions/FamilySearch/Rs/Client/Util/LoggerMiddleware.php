<?php

namespace Gedcomx\Extensions\FamilySearch\Rs\Client\Util;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\MessageInterface;

/**
 * Create Guzzle middleware that enables logging of all HTTP requests and responses.
 *
 * Class LoggerMiddleware
 *
 * @package Gedcomx\Extensions\FamilySearch\Rs\Client\Util
 */
class LoggerMiddleware
{
    
    /**
     * Return Guzzle middleware for logging
     */
    public static function middleware($logger)
    {
        return function(callable $handler) use ($logger) {
            return function(RequestInterface $request, array $options) use ($handler, $logger) {
                $logger->info($request->getMethod() . ' ' . $request->getUri());
                $logger->debug(self::printHeaders($request));
                
                $promise = $handler($request, $options);
                return $promise->then(function(ResponseInterface $response) use ($logger) {
                    
                    // Log response headers
                    $logger->debug(self::printHeaders($response));
                    
                    // Log warning header
                    if($response->getHeader('warning')){
                        $logger->warning($response->getHeaderLine('warning'));
                    }
                    
                    $statusCode = $response->getStatusCode();
                    $reason = $response->getReasonPhrase();
                    
                    // Log 400 errors
                    if($statusCode >= 400 && $statusCode < 500){
                        $logger->error($statusCode . ' ' . $reason);
                    }
                    
                    // Log 500 errors
                    if($statusCode >= 500){
                        $logger->critical($statusCode . ' ' . $reason . "\n" . $response->getBody());
                    }
                    
                    // Log 503s
                    if($statusCode == 503){
                        $logger->alert($statusCode . ' ' . $reason);
                    }
                    
                    return $response;
                });
            };
        };
    }
    
    /**
     * @param Psr\Http\Message\MessageInterface $message
     * 
     * @return string
     */
    private static function printHeaders(MessageInterface $message)
    {
        $headers = [];
        foreach($message->getHeaders() as $name => $values){
            $headers[] = $name . ': ' . implode(', ', $values);
        }
        return implode("\n", $headers);
    }
}