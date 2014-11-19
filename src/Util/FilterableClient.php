<?php

namespace Gedcomx\Util;

use Guzzle\Http\Client;
use Guzzle\Http\Message\RequestInterface;

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
     * @param Filter $filter
     */
    public function addFilter(Filter $filter)
    {
        $this->filters[] = $filter;
    }

    public function send($requests)
    {
        foreach ($this->filters as $filter) {
            $filter->apply($requests);
        }
        return parent::send($requests);
    }
}

interface Filter
{
    /**
     * @param array|RequestInterface $requests
     * @return array|RequestInterface

     */
    function apply($requests);
}