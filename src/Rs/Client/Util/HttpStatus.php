<?php


	namespace Gedcomx\Rs\Client\Util;

	/**
	 * A helper class with a predefined list of HTTP status codes and names.
	 *
	 * Class HttpStatus
	 *
	 * @package Gedcomx\Rs\Client\Util
	 */
	class HttpStatus
	{
		/**
		 * Equivalent to HTTP status 100. CONTINUE_ indicates that the client can continue with its request.
		 */
		const CONTINUE_ = 100;
		/**
		 * Equivalent to HTTP status 101. SWITCHING_PROTOCOLS indicates that the protocol version or protocol is being changed.
		 */
		const SWITCHING_PROTOCOLS = 101;
		/**
		 * Equivalent to HTTP status 102. PROCESSING indicates that the server has received and is processing the request, but no response is available yet.
		 */
		const PROCESSING = 102;
		/**
		 * Equivalent to HTTP status 200. OK indicates that the request succeeded and that the requested information is in the response. This is the most common status code to receive.
		 */
		const OK = 200;
		/**
		 * Equivalent to HTTP status 201. CREATED indicates that the request resulted in a new resource created before the response was sent.
		 */
		const CREATED = 201;
		/**
		 * Equivalent to HTTP status 202. ACCEPTED indicates that the request has been accepted for further processing.
		 */
		const ACCEPTED = 202;
		/**
		 * Equivalent to HTTP status 203. NON_AUTHORITATIVE_INFORMATION indicates that the returned metainformation is from a cached copy instead of the origin server and therefore may be incorrect.
		 */
		const NON_AUTHORITATIVE_INFORMATION = 203;
		/**
		 * Equivalent to HTTP status 204. NO_CONTENT indicates that the request has been successfully processed and that the response is intentionally blank.
		 */
		const NO_CONTENT = 204;
		/**
		 * Equivalent to HTTP status 205. RESET_CONTENT indicates that the client should reset (not reload) the current resource.
		 */
		const RESET_CONTENT = 205;
		/**
		 * Equivalent to HTTP status 206. PARTIAL_CONTENT indicates that the response is a partial response as requested by a GET request that includes a byte range.
		 */
		const PARTIAL_CONTENT = 206;
		/**
		 * Equivalent to HTTP status 207. MULTI_STATUS indicates the message body that follows is an XML message and can contain a number of separate response codes, depending on how many sub-requests were made.
		 */
		const MULTI_STATUS = 207;
		/**
		 * Equivalent to HTTP status 208. ALREADY_REPORTED indicates the members of a DAV binding have already been enumerated in a previous reply to this request, and are not being included again.
		 */
		const ALREADY_REPORTED = 208;
		/**
		 * Equivalent to HTTP status 226. IM_USED indicates the server has fulfilled a request for the resource, and the response is a representation of the result of one or more instance-manipulations applied to the current instance.
		 */
		const IM_USED = 226;
		/**
		 * Equivalent to HTTP status 300. MULTIPLE_CHOICES (or AMBIGUOUS) indicates that the requested information has multiple representations. The default action is to treat this status as a redirect and follow the contents of the Location header associated with this response.
		 */
		const MULTIPLE_CHOICES = 300;
		/**
		 * Equivalent to HTTP status 301. MOVED_PERMANENTLY (or MOVED) indicates that the requested information has been moved to the URI specified in the Location header. The default action when this status is received is to follow the Location header associated with the response.
		 */
		const MOVED_PERMANENTLY = 301;
		/**
		 * Equivalent to HTTP status 302. FOUND (or REDIRECT) indicates that the requested information is located at the URI specified in the Location header. The default action when this status is received is to follow the Location header associated with the response. When the original request method was POST, the redirected request will use the GET method.
		 */
		const FOUND = 302;
		/**
		 * Equivalent to HTTP status 303. SEE_OTHER (or REDIRECT_METHOD) automatically redirects the client to the URI specified in the Location header as the result of a POST. The request to the resource specified by the Location header will be made with a GET.
		 */
		const SEE_OTHER = 303;
		/**
		 * Equivalent to HTTP status 304. NOT_MODIFIED indicates that the client's cached copy is up to date. The contents of the resource are not transferred.
		 */
		const NOT_MODIFIED = 304;
		/**
		 * Equivalent to HTTP status 305. USE_PROXY indicates that the request should use the proxy server at the URI specified in the Location header.
		 */
		const USE_PROXY = 305;
		/**
		 * Equivalent to HTTP status 307. TEMPORARY_REDIRECT (or REDIRECT_KEEP_VERB) indicates that the request information is located at the URI specified in the Location header. The default action when this status is received is to follow the Location header associated with the response. When the original request method was POST, the redirected request will also use the POST method.
		 */
		const TEMPORARY_REDIRECT = 307;
		/**
		 * Equivalent to HTTP status 308. PERMANENT_REDIRECT indicates the request, and all future requests should be repeated using another URI. 307 and 308 parallel the behaviours of 302 and 301, but do not allow the HTTP method to change.
		 */
		const PERMANENT_REDIRECT = 308;
		/**
		 * Equivalent to HTTP status 400. BAD_REQUEST indicates that the request could not be understood by the server. BAD_REQUEST is sent when no other error is applicable, or if the exact error is unknown or does not have its own error code.
		 */
		const BAD_REQUEST = 400;
		/**
		 * Equivalent to HTTP status 401. UNAUTHORIZED indicates that the requested resource requires authentication. The WWW-Authenticate header contains the details of how to perform the authentication.
		 */
		const UNAUTHORIZED = 401;
		/**
		 * Equivalent to HTTP status 402. PAYMENT_REQUIRED is reserved for future use.
		 */
		const PAYMENT_REQUIRED = 402;
		/**
		 * Equivalent to HTTP status 403. FORBIDDEN indicates that the server refuses to fulfill the request.
		 */
		const FORBIDDEN = 403;
		/**
		 * Equivalent to HTTP status 404. NOT_FOUND indicates that the requested resource does not exist on the server.
		 */
		const NOT_FOUND = 404;
		/**
		 * Equivalent to HTTP status 405. METHOD_NOT_ALLOWED indicates that the request method (POST or GET) is not allowed on the requested resource.
		 */
		const METHOD_NOT_ALLOWED = 405;
		/**
		 * Equivalent to HTTP status 406. NOT_ACCEPTABLE indicates that the client has indicated with Accept headers that it will not accept any of the available representations of the resource.
		 */
		const NOT_ACCEPTABLE = 406;
		/**
		 * Equivalent to HTTP status 407. PROXY_AUTHENTICATION_REQUIRED indicates that the requested proxy requires authentication. The Proxy-authenticate header contains the details of how to perform the authentication.
		 */
		const PROXY_AUTHENTICATION_REQUIRED = 407;
		/**
		 * Equivalent to HTTP status 408. REQUEST_TIMEOUT indicates that the client did not send a request within the time the server was expecting the request.
		 */
		const REQUEST_TIMEOUT = 408;
		/**
		 * Equivalent to HTTP status 409. CONFLICT indicates that the request could not be carried out because of a conflict on the server.
		 */
		const CONFLICT = 409;
		/**
		 * Equivalent to HTTP status 410. GONE indicates that the requested resource is no longer available.
		 */
		const GONE = 410;
		/**
		 * Equivalent to HTTP status 411. LENGTH_REQUIRED indicates that the required Content-length header is missing.
		 */
		const LENGTH_REQUIRED = 411;
		/**
		 * Equivalent to HTTP status 412. PRECONDITION_FAILED indicates that a condition set for this request failed, and the request cannot be carried out. Conditions are set with conditional request headers like If-Match, If-None-Match, or If-Unmodified-Since.
		 */
		const PRECONDITION_FAILED = 412;
		/**
		 * Equivalent to HTTP status 413. REQUEST_ENTITY_TOO_LARGE indicates that the request is too large for the server to process.
		 */
		const REQUEST_ENTITY_TOO_LARGE = 413;
		/**
		 * Equivalent to HTTP status 414. REQUEST_URI_TOO_LONG indicates that the URI is too long.
		 */
		const REQUEST_URI_TOO_LONG = 414;
		/**
		 * Equivalent to HTTP status 415. UNSUPPORTED_MEDIA_TYPE indicates that the request is an unsupported type.
		 */
		const UNSUPPORTED_MEDIA_TYPE = 415;
		/**
		 * Equivalent to HTTP status 416. REQUESTED_RANGE_NOT_SATISFIABLE indicates that the range of data requested from the resource cannot be returned, either because the beginning of the range is before the beginning of the resource, or the end of the range is after the end of the resource.
		 */
		const REQUESTED_RANGE_NOT_SATISFIABLE = 416;
		/**
		 * Equivalent to HTTP status 417. EXPECTATION_FAILED indicates that an expectation given in an Expect header could not be met by the server.
		 */
		const EXPECTATION_FAILED = 417;
		/**
		 * Equivalent to HTTP status 422. UNPROCESSABLE_ENTITY indicates the server cannot meet the requirements of the Expect request-header field.
		 */
		const UNPROCESSABLE_ENTITY = 422;
		/**
		 * Equivalent to HTTP status 423. LOCKED indicates the resource that is being accessed is locked.
		 */
		const LOCKED = 423;
		/**
		 * Equivalent to HTTP status 424. FAILED_DEPENDENCY indicates the request failed due to failure of a previous request.
		 */
		const FAILED_DEPENDENCY = 424;
		/**
		 * Equivalent to HTTP status 425.
		 */
		const RESERVED_FOR_WEBDAV_ADVANCED_COLLECTIONS_EXPIRED_PROPOSAL = 425;
		/**
		 * Equivalent to HTTP status 426. UPGRADE_REQUIRED indicates the client should switch to a different protocol such as TLS/1.0.
		 */
		const UPGRADE_REQUIRED = 426;
		/**
		 * Equivalent to HTTP status 428. PRECONDITION_REQUIRED indicates the origin server requires the request to be conditional.
		 */
		const PRECONDITION_REQUIRED = 428;
		/**
		 * Equivalent to HTTP status 429. TOO_MANY_REQUESTS indicates the user has sent too many requests in a given amount of time.
		 */
		const TOO_MANY_REQUESTS = 429;
		/**
		 * Equivalent to HTTP status 431. REQUEST_HEADER_FIELDS_TOO_LARGE indicates the server is unwilling to process the request because either an individual header field, or all the header fields collectively, are too large.
		 */
		const REQUEST_HEADER_FIELDS_TOO_LARGE = 431;
		/**
		 * Equivalent to HTTP status 500. INTERNAL_SERVER_ERROR indicates that a generic error has occurred on the server.
		 */
		const INTERNAL_SERVER_ERROR = 500;
		/**
		 * Equivalent to HTTP status 501. NOT_IMPLEMENTED indicates that the server does not support the requested function.
		 */
		const NOT_IMPLEMENTED = 501;
		/**
		 * Equivalent to HTTP status 502. BAD_GATEWAY indicates that an intermediate proxy server received a bad response from another proxy or the origin server.
		 */
		const BAD_GATEWAY = 502;
		/**
		 * Equivalent to HTTP status 503. SERVICE_UNAVAILABLE indicates that the server is temporarily unavailable, usually due to high load or maintenance.
		 */
		const SERVICE_UNAVAILABLE = 503;
		/**
		 * Equivalent to HTTP status 504. GATEWAY_TIMEOUT indicates that an intermediate proxy server timed out while waiting for a response from another proxy or the origin server.
		 */
		const GATEWAY_TIMEOUT = 504;
		/**
		 * Equivalent to HTTP status 505. HTTP_VERSION_NOT_SUPPORTED indicates that the requested HTTP version is not supported by the server.
		 */
		const HTTP_VERSION_NOT_SUPPORTED = 505;
		/**
		 * Equivalent to HTTP status 506. VARIANT_ALSO_NEGOTIATES_EXPERIMENTAL indicates the transparent content negotiation for the request results in a circular reference.
		 */
		const VARIANT_ALSO_NEGOTIATES_EXPERIMENTAL = 506;
		/**
		 * Equivalent to HTTP status 507. INSUFFICIENT_STORAGE indicates the server is unable to store the representation needed to complete the request.
		 */
		const INSUFFICIENT_STORAGE = 507;
		/**
		 * Equivalent to HTTP status 508. LOOP_DETECTED indicates the server detected an infinite loop while processing the request (sent in lieu of 208 Already Reported).
		 */
		const LOOP_DETECTED = 508;
		/**
		 * Equivalent to HTTP status 510. NOT_EXTENDED indicates that further extensions to the request are required for the server to fulfil it.
		 */
		const NOT_EXTENDED = 510;
		/**
		 * Equivalent to HTTP status 511. NETWORK_AUTHENTICATION_REQUIRED indicates the client needs to authenticate to gain network access.
		 */
		const NETWORK_AUTHENTICATION_REQUIRED = 511;

		private static $statusText = array(
			100 => 'Continue',
			101 => 'Switching Protocols',
			102 => 'Processing',
			200 => 'OK',
			201 => 'Created',
			202 => 'Accepted',
			203 => 'Non-Authoritative Information',
			204 => 'No Content',
			205 => 'Reset Content',
			206 => 'Partial Content',
			207 => 'Multi-Status',
			208 => 'Already Reported',
			226 => 'IM Used',
			300 => 'Multiple Choices',
			301 => 'Moved Permanently',
			302 => 'Found',
			303 => 'See Other',
			304 => 'Not Modified',
			305 => 'Use Proxy',
			307 => 'Temporary Redirect',
			308 => 'Permanent Redirect',
			400 => 'Bad Request',
			401 => 'Unauthorized',
			402 => 'Payment Required',
			403 => 'Forbidden',
			404 => 'Not Found',
			405 => 'Method Not Allowed',
			406 => 'Not Acceptable',
			407 => 'Proxy Authentication Required',
			408 => 'Request Timeout',
			409 => 'Conflict',
			410 => 'Gone',
			411 => 'Length Required',
			412 => 'Precondition Failed',
			413 => 'Request Entity Too Large',
			414 => 'Request-URI Too Long',
			415 => 'Unsupported Media Type',
			416 => 'Requested Range Not Satisfiable',
			417 => 'Expectation Failed',
			422 => 'Unprocessable Entity',
			423 => 'Locked',
			424 => 'Failed Dependency',
			425 => 'Reserved for WebDAV advanced collections expired proposal',
			426 => 'Upgrade required',
			428 => 'Precondition Required',
			429 => 'Too Many Requests',
			431 => 'Request Header Fields Too Large',
			500 => 'Internal Server Error',
			501 => 'Not Implemented',
			502 => 'Bad Gateway',
			503 => 'Service Unavailable',
			504 => 'Gateway Timeout',
			505 => 'HTTP Version Not Supported',
			506 => 'Variant Also Negotiates (Experimental)',
			507 => 'Insufficient Storage',
			508 => 'Loop Detected',
			510 => 'Not Extended',
			511 => 'Network Authentication Required',
		);

		/**
		 * Gets the status name for the specified HTTP status code.
		 *
		 * @param $code
		 *
		 * @return mixed
		 */
		public static function getText($code)
		{
			return self::$statusText[$code];
		}
	}