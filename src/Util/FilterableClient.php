<?php

namespace Gedcomx\Util;

use Guzzle\Http\Client;
use Guzzle\Http\Message\RequestInterface;

/**
 * Represents a REST API client that can have filters applied just before executing requests.
 * It is important to note, however, that in order for the filters to be applied, this class's send() method must be called.
 * Calling parent::send() directly will not result in these filters being applied.
 */
class FilterableClient extends Client
{
    /** @var Filter[] $filters */
    private $filters = array();

    /**
     * @param string $baseUrl Base URL of the web service
     * @param array|Collection $config Configuration settings
     */
    public function __construct($baseUrl = '', $config = null)
    {
        parent::__construct($baseUrl, $config);
    }

    /**
     * Adds a filter to the current REST API client.
     * The filter added here will be applied for all subsequent calls to send(). It is important to note, however, that in order for any
     * filter to be applied, this class's send() method must be called. Calling parent::send() directly will not
     * result in these filters being applied.
     * @param Filter $filter
     */
    public function addFilter(Filter $filter)
    {
        $this->filters[] = $filter;
    }

    /**
     * @param array|\Guzzle\Http\Message\RequestInterface $requests
     *
     * @return array|\Guzzle\Http\Message\Response|null
     */
    public function send($requests)
    {
        foreach ($this->filters as $filter) {
            $filter->apply($requests);
        }
        return parent::send($requests);
    }
}