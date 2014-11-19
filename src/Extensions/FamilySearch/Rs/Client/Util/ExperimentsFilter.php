<?php

namespace Gedcomx\Extensions\FamilySearch\Rs\Client\Util;

use Gedcomx\Util\Filter;
use Guzzle\Http\Message\RequestInterface;

class ExperimentsFilter implements Filter
{
    /** @var String $experiments */
    private $experiments = array();

    /**
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