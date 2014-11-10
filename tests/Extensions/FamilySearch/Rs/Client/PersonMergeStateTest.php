<?php 

namespace Gedcomx\Tests\Extensions\FamilySearch\Rs\Client;

use Gedcomx\Extensions\FamilySearch\Platform\Tree\Merge;
use Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree\FamilyTreeStateFactory;
use Gedcomx\Rs\Client\Util\HttpStatus;
use Gedcomx\Tests\ApiTestCase;
use Gedcomx\Tests\PersonBuilder;

class PersonMergeStateTest extends ApiTestCase
{
    /**
     * @link https://familysearch.org/developers/docs/api/tree/Merge_Person_usecase
     */
    public function testMergePerson()
    {
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);

        $person = PersonBuilder::buildPerson('male');
        $person1 = $this->collectionState()->addPerson($person)->get();
        $person2 = $this->collectionState()->addPerson($person)->get();

        /** @var  \Gedcomx\Extensions\FamilySearch\Rs\Client\PersonMergeState $result */
        $result = $person1->readMergeAnalysis($person2);
        /** @var  \Gedcomx\Extensions\FamilySearch\Platform\Tree\MergeAnalysis $analysis */
        $analysis = $result->getAnalysis();

        $merge = new Merge();
        $merge->setResourcesToCopy($analysis->getDuplicateResources());
        /** @var \Gedcomx\Extensions\FamilySearch\Platform\Tree\MergeConflict $resource */
        foreach ($analysis->getConflictingResources() as $resource) {
            if ($resource->getDuplicateResource()) {
                $ref = $resource->getDuplicateResource();
                $merge->addResourceToCopy($ref);
            }
        }

        $state = $result->doMerge($merge);
        $this->assertEquals(
            HttpStatus::NO_CONTENT,
            $state->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__,$state)
        );

        $person1 = $person1->get();
        $person2 = $person2->get();
        $this->assertEquals(
            $person1->getSelfUri(),
            $person2->getSelfUri(),
            "Person URIs don't match."
        );

        $person1->delete();
        $person2->delete();
    }
}