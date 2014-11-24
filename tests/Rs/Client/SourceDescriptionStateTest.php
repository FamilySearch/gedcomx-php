<?php


namespace Gedcomx\Tests\Rs\Client;


use Gedcomx\Common\Attribution;
use Gedcomx\Common\Note;
use Gedcomx\Common\ResourceReference;
use Gedcomx\Common\TextValue;
use Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree\FamilyTreeStateFactory;
use Gedcomx\Rs\Client\SourceDescriptionState;
use Gedcomx\Rs\Client\StateFactory;
use Gedcomx\Rs\Client\Util\HttpStatus;
use Gedcomx\Source\SourceCitation;
use Gedcomx\Source\SourceDescription;
use Gedcomx\Tests\ApiTestCase;

class SourceDescriptionStateTest extends ApiTestCase
{

    /**
     * @link https://familysearch.org/developers/docs/api/sources/Create_Source_Description_usecase
     * @link https://familysearch.org/developers/docs/api/sources/Read_Source_Description_usecase
     * @link https://familysearch.org/developers/docs/api/sources/Update_Source_Description_usecase
     * @link https://familysearch.org/developers/docs/api/sources/Delete_Source_Description_usecase
     */
    public function testSourceDescriptionCRUD()
    {
        $this->collectionState(new StateFactory());
        /* CREATE */
        /** @var SourceDescriptionState $sourceState */
        $sourceState = $this->createSource();
        $this->assertAttributeEquals(HttpStatus::CREATED, "statusCode", $sourceState->getResponse(), $this->buildFailMessage(__METHOD__ . "(CREATE)", $sourceState));

        /* READ */
        $sourceState = $sourceState->get();
        $this->assertAttributeEquals(HttpStatus::OK, "statusCode", $sourceState->getResponse(), $this->buildFailMessage(__METHOD__ . "(READ)", $sourceState));

        /* UPDATE */
        $source = $sourceState->getSourceDescription();
        $source->setAttribution(new Attribution(array(
            "changeMessage" => $this->faker->sentence(6)
        )));
        $updated = $sourceState->update($source);
        $this->assertAttributeEquals(HttpStatus::NO_CONTENT, "statusCode", $updated->getResponse(), $this->buildFailMessage(__METHOD__ . "(UPDATE)", $updated));

        /* DELETE */
        $deleted = $sourceState->delete();
        $this->assertAttributeEquals(HttpStatus::NO_CONTENT, "statusCode", $deleted->getResponse(), $this->buildFailMessage(__METHOD__ . "(DELETE)", $deleted));
    }


    public function testReadSourceDescription()
    {
        $this->collectionState(new FamilyTreeStateFactory());
        $sd = $this->createSourceDescription();
        /** @var SourceDescriptionState $state */
        $state = $this->collectionState()->addSourceDescription($sd)->get();

        $this->assertNotNull($state->ifSuccessful());
        $this->assertEquals(HttpStatus::OK, $state->getResponse()->getStatusCode());
        $this->assertNotNull($state->getSourceDescription());
    }

    public function testUpdateSourceDescription()
    {
        $this->collectionState(new FamilyTreeStateFactory());
        $sd = $this->createSourceDescription();
        /** @var SourceDescriptionState $description */
        $description = $this->collectionState()->addSourceDescription($sd)->get();
        $state = $description->update($description->getSourceDescription());

        $this->assertNotNull($state->ifSuccessful());
        $this->assertEquals(HttpStatus::NO_CONTENT, $state->getResponse()->getStatusCode());
    }

    private function createSourceDescription()
    {
        $sd = new SourceDescription();
        $citation = new SourceCitation();
        $citation->setValue("\"United States Census, 1900.\" database and digital images, FamilySearch (https://familysearch.org/: accessed 17 Mar 2012), Ethel Hollivet, 1900; citing United States Census Office, Washington, D.C., 1900 Population Census Schedules, Los Angeles, California, population schedule, Los Angeles Ward 6, Enumeration District 58, p. 20B, dwelling 470, family 501, FHL microfilm 1,240,090; citing NARA microfilm publication T623, roll 90.");
        $sd->setCitations(array($citation));
        $title = new TextValue();
        $title->setValue("1900 US Census, Ethel Hollivet");
        $sd->setTitles(array($title));
        $note = new Note();
        $note->setText("Ethel Hollivet (line 75) with husband Albert Hollivet (line 74); also in the dwelling: step-father Joseph E Watkins (line 72), mother Lina Watkins (line 73), and grandmother -- Lina's mother -- Mary Sasnett (line 76).  Albert's mother and brother also appear on this page -- Emma Hollivet (line 68), and Eddie (line 69).");
        $sd->setNotes(array($note));
        $attribution = new Attribution();
        $rr = new ResourceReference();
        $rr->setResource("https://familysearch.org/platform/users/agents/MM6M-8QJ");
        $rr->setResourceId("MM6M-8QJ");
        $attribution->setContributor($rr);
        $attribution->setModified(time());
        $attribution->setChangeMessage("This is the change message");
        $sd->setAttribution($attribution);

        return $sd;
    }
}