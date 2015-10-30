<?php

namespace Gedcomx\Extensions\FamilySearch\Rs\Client\Util;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\MessageInterface;

/**
 * Middleware to automatically handle throttling.
 */
class ThrottlingMiddleware
{

    public static $maxRetries = 2;
    
    /**
     * @return callable Returns a function that accepts the next handler.
     */
    public static function middleware()
    {
    	return function (callable $handler) {
    		return new ThrottlingMiddleware($handler);
    	};
    }

    /** @var callable  */
    private $nextHandler;

    /**
     * @param callable $nextHandler Next handler to invoke.
     */
    public function __construct(callable $nextHandler)
    {
        $this->nextHandler = $nextHandler;
    }

    /**
     * @param RequestInterface $request
     * @param array            $options
     *
     * @return PromiseInterface
     */
    public function __invoke(RequestInterface $request, array $options)
    {
        $fn = $this->nextHandler;

        return $fn($request, $options)
            ->then(function (ResponseInterface $response) use ($request, $options) {
                return $this->checkThrottling($request, $options, $response);
            });
    }

    /**
     * @param RequestInterface  $request
     * @param array             $options
     * @param ResponseInterface|PromiseInterface $response
     *
     * @return ResponseInterface|PromiseInterface
     */
    public function checkThrottling(
        RequestInterface $request,
        array $options,
        ResponseInterface $response
    ) {
    	
    	if(!isset($options['__throttle_retries'])){
    		$options['__throttle_retries'] = $this::$maxRetries;
    	}
        
        // Return the response if it wasn't throttled or if we don't have any
        // retries left
        if ($response->getStatusCode() !== 429 || !($options['__throttle_retries'] > 0)) {
            return $response;
        }
        
        $options['__throttle_retries']--;

		// If the response was throttled, wait the specified time
		// and repeat the request.
		sleep($response->getHeader('Retry-After')[0]);

        /** @var PromiseInterface|ResponseInterface $promise */
        $promise = $this($request, $options);

		// Add a header to the final response that says it was throttled
        return $promise->then(
        	function (ResponseInterface $response) {
        		return $response->withHeader('X-FS-THROTTLED', '');
        	}	
        );
    }
}