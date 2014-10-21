<?php

namespace Gedcomx\Tests;

use Gedcomx\Extensions\FamilySearch\Platform\Discussions\Discussion;
use Gedcomx\Extensions\FamilySearch\Platform\Tree\DiscussionReference;

class DiscussionBuilder extends TestBuilder
{
    /**
     * @param String $userTreeId
     *
     * @return Discussion
     */
    public static function createDiscussion($userTreeId)
    {
        $data = array(
            'title' => self::faker()->sentence(5),
            'details' => self::faker()->sentence(12),
            'contributor' => array(
                'resource' => 'https://familysearch.org/platform/users/agents/' . $userTreeId,
                'resourceId' => $userTreeId
            )
        );

        return new Discussion($data);
    }

    public static function buildReference()
    {
        $data = array(
            "resourceId" => "",
            "resource" => "",
            "attribution" => new Attribution(
                array(
                    "changeMessage" => self::faker()->sentence(10)
                )
            )
        );
        return new DiscussionReference($data);
    }
}