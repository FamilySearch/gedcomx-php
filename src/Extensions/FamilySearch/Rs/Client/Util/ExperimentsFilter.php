<?php

namespace Gedcomx\Extensions\FamilySearch\Rs\Client\Util;

use Gedcomx\Util\Filter;
use Guzzle\Http\Message\RequestInterface;

/**
 * This filter enables SDK consumers to enable specific FamilySearch features that are not yet enabled by default.
 *
 * Class ExperimentsFilter
 *
 * @package Gedcomx\Extensions\FamilySearch\Rs\Client\Util
 */
class ExperimentsFilter implements Filter
{
    /** @var string[] $experiments */
    private $experiments = array();

    /**
     * Constructs a new experiments filter with the specified experiments.
     *
     * @param string[] $experiments
     */
    public function __construct(array $experiments)
    {
        $experimentsList = array();
        foreach ($experiments as $experiment) {
            $experimentsList[] = $experiment;
        }
        $this->experiments = join(",", $experimentsList);
    }

    /**
     * This method applies the current collection of features to the specified REST API request.
     * The specific features will be added as a special header to the REST API request.
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
            $requests->addHeader("X-FS-Feature-Tag", $this->experiments);
        }

        return $requests;
    }
}