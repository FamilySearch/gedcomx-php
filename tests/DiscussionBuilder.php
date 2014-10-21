<?php

namespace Gedcomx\Tests;

use Gedcomx\Extensions\FamilySearch\Platform\Discussions\Discussion;

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
}