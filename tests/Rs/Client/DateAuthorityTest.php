<?php

namespace Gedcomx\Rs\Client;

use Gedcomx\Extensions\FamilySearch\Rs\Client\FamilySearchStateFactory;
use Gedcomx\Tests\ApiTestCase;

class DateAuthorityTest extends ApiTestCase
{
    public function testDate(){
        $factory = new FamilySearchStateFactory();
        $collections = $factory->newDiscoveryState()->readSubcollections()->getCollections();
        $link = null;
        foreach ($collections as $record)
        {
            if ($record->getId() == "FSDA")
            {
                $link = $record->getLink(Rel::SELF);
                break;
            }
        }
        $dateState = $factory->newCollectionState($link->getHref());
        $normalized = $dateState->normalizeDate("26 Nov 1934");
        $this->assertEquals(
            'gedcomx-date:+1934-11-26',
            $normalized->getFormal(),
            "Formalized date format incorrect: " . $normalized->getFormal()
        );
        $extensions = $normalized->getNormalizedExtensions();
        $this->assertEquals(
            '26 November 1934',
            $extensions[0]->getValue(),
            "Normalized date format incorrect: " . $extensions[0]->getValue()
        );
    }
}