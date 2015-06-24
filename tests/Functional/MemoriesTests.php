<?php

namespace Gedcomx\Tests\Functional;

use Gedcomx\Extensions\FamilySearch\FamilySearchPlatform;
use Gedcomx\Extensions\FamilySearch\Platform\Discussions\Comment;
use Gedcomx\Extensions\FamilySearch\Rs\Client\DiscussionState;
use Gedcomx\Extensions\FamilySearch\Rs\Client\FamilySearchSourceDescriptionState;
use Gedcomx\Extensions\FamilySearch\Rs\Client\FamilySearchStateFactory;
use Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree\FamilyTreePersonState;
use Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree\FamilyTreeStateFactory;
use Gedcomx\Extensions\FamilySearch\Rs\Client\Memories\FamilySearchMemories;
use Gedcomx\Gedcomx;
use Gedcomx\Rs\Client\PersonState;
use Gedcomx\Rs\Client\SourceDescriptionState;
use Gedcomx\Rs\Client\StateFactory;
use Gedcomx\Rs\Client\Util\DataSource;
use Gedcomx\Rs\Client\Util\HttpStatus;
use Gedcomx\Tests\ApiTestCase;
use Gedcomx\Tests\ArtifactBuilder;
use Gedcomx\Tests\PersonBuilder;
use Gedcomx\Tests\SourceBuilder;
use Gedcomx\Types\ResourceType;

class MemoriesTests extends ApiTestCase
{
    /**
     * @link https://familysearch.org/developers/docs/api/tree/Create_Person_Memory_Reference_usecase
     */
    public function testCreatePersonMemoryReference()
    {
        $filename = ArtifactBuilder::makeImage();
        $artifact = new DataSource();
        $artifact->setFile($filename);

        $description = SourceBuilder::newSource();

        $factory = new FamilyTreeStateFactory();
        /** @var FamilySearchMemories $memories */
        $memories = $factory->newMemoriesState();
        $memories = $this->authorize($memories);
        $this->assertNotEmpty($memories->getAccessToken());

        /** @var \Gedcomx\Rs\Client\SourceDescriptionState $upload */
        $upload = $memories->addArtifact($artifact, $description);
        $this->queueForDelete($upload);
        $this->assertEquals(
            HttpStatus::CREATED,
            $upload->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__.':'.__LINE__, $upload)
        );
        $upload = $upload->get();
        $this->assertEquals(
            HttpStatus::OK,
            $upload->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__.':'.__LINE__, $upload)
        );

        $this->collectionState($factory);
        /** @var FamilyTreePersonState $person */
        $person = $this->createPerson('male');
        $this->queueForDelete($person);
        $this->assertEquals(
            HttpStatus::CREATED,
            $person->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__.':'.__LINE__, $person)
        );
        $person = $person->get();
        $this->assertEquals(
            HttpStatus::OK,
            $person->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__.':'.__LINE__, $person)
        );

        $persona = $upload->addPersonPersona(PersonBuilder::buildPerson('male'));
        $this->queueForDelete($persona);
        $this->assertEquals(
            HttpStatus::CREATED,
            $persona->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__.':'.__LINE__, $persona)
        );
        $persona = $persona->get();
        $this->assertEquals(
            HttpStatus::OK,
            $persona->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__.':'.__LINE__, $persona)
        );

        /** @var  FamilyTreePersonState $newState */
        $newState = $person->addPersonaPersonState($persona);
        $this->assertEquals(
            HttpStatus::CREATED,
            $newState->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__.':'.__LINE__, $newState)
        );
        $newState = $person->get();
        $this->assertEquals(
            HttpStatus::OK,
            $newState->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__.':'.__LINE__, $newState)
        );
        /** @var FamilySearchPlatform $entity */
        $entity = $newState->getEntity();
        $sources = $entity->getSourceDescriptions();
        $this->assertNotEmpty($sources);
        $this->assertEquals($sources[0]->getResourceType(), ResourceType::PERSON);
    }

    /**
     * @link https://familysearch.org/developers/docs/api/memories/Create_Memories_Comment_usecase
     */
    public function testCreateMemoriesComment()
    {
        $factory = new FamilySearchStateFactory();
        /** @var FamilySearchMemories $memories */
        $memories = $factory->newMemoriesState();
        $this->authorize($memories);
        $this->assertNotEmpty($memories->getResponse()->getStatusCode());

        $ds = new DataSource();
        $ds->setTitle("Sample Memory");
        $ds->setFile(ArtifactBuilder::makeTextFile());
        /** @var FamilySearchSourceDescriptionState $artifact */
        $artifact = $memories->addArtifact($ds);
        $this->queueForDelete($artifact);
        $this->assertEquals(HttpStatus::CREATED, $artifact->getResponse()->getStatusCode());
        $artifact = $artifact->get();
        $this->assertEquals(HttpStatus::OK, $artifact->getResponse()->getStatusCode());

        $comments = $artifact->readComments();
        $this->assertEquals(HttpStatus::OK, $comments->getResponse()->getStatusCode());

        $commentText = "Test comment.";
        $comment = new Comment();
        $comment->setText($commentText);
        $state = $comments->addComment($comment);

        $this->assertNotNull($state->ifSuccessful());
        $this->assertEquals(HttpStatus::CREATED, $state->getResponse()->getStatusCode());
        $comments = $artifact->readComments();
        $this->assertEquals(HttpStatus::OK, $comments->getResponse()->getStatusCode());
        $this->assertNotNull($comments->getDiscussion());
        $commentList = $comments->getDiscussion()->getComments();
        $this->assertNotEmpty($commentList);
        $commentItem = array_shift($commentList);
        $this->assertEquals($commentText, $commentItem->getText());
    }

    /**
     * @link https://familysearch.org/developers/docs/api/memories/Create_Memory_Persona_usecase
     */
    public function testCreateMemoryPersona()
    {
        $filename = ArtifactBuilder::makeImage();
        $artifact = new DataSource();
        $artifact->setFile($filename);

        $description = SourceBuilder::newSource();

        $factory = new FamilyTreeStateFactory();
        /** @var FamilySearchMemories $memories */
        $memories = $factory->newMemoriesState();
        $memories = $this->authorize($memories);
        $this->assertNotEmpty($memories->getResponse()->getStatusCode());

        /** @var \Gedcomx\Rs\Client\SourceDescriptionState $upload */
        $upload = $memories->addArtifact($artifact, $description);
        $this->queueForDelete($upload);
        $this->assertEquals(HttpStatus::CREATED, $upload->getResponse()->getStatusCode());
        $upload = $upload->get();
        $this->assertEquals(HttpStatus::OK, $upload->getResponse()->getStatusCode());

        $person = PersonBuilder::buildPerson('male');

        $persona = $upload->addPersonPersona($person);
        $this->queueForDelete($persona);

        $this->assertEquals(
            HttpStatus::CREATED,
            $persona->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__, $persona)
        );
        $persona = $persona->get();
        $this->assertEquals(HttpStatus::OK, $persona->getResponse()->getStatusCode());
    }

    /**
     * @link https://familysearch.org/developers/docs/api/sources/Create_User-Uploaded_Source_usecase
     * @see SourcesTests::testCreateSourceDescription
     */

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Read_Person_Memory_References_usecase
     */
    public function testReadPersonMemoryReference()
    {
        $filename = ArtifactBuilder::makeImage();
        $artifact = new DataSource();
        $artifact->setFile($filename);

        $description = SourceBuilder::newSource();

        $factory = new FamilyTreeStateFactory();
        /** @var FamilySearchMemories $memories */
        $memories = $factory->newMemoriesState();
        $memories = $this->authorize($memories);
        $this->assertNotEmpty($memories->getAccessToken());

        /** @var \Gedcomx\Rs\Client\SourceDescriptionState $upload */
        $upload = $memories->addArtifact($artifact, $description);
        $this->queueForDelete($upload);
        $this->assertEquals(HttpStatus::CREATED, $upload->getResponse()->getStatusCode());
        $upload = $upload->get();
        $this->assertEquals(HttpStatus::OK, $upload->getResponse()->getStatusCode());

        $this->collectionState($factory);
        /** @var FamilyTreePersonState $person */
        $person = $this->createPerson('male');
        $this->assertEquals(HttpStatus::CREATED, $person->getResponse()->getStatusCode());
        $person = $person->get();
        $this->assertEquals(HttpStatus::OK, $person->getResponse()->getStatusCode());

        $persona = $upload->addPersonPersona(PersonBuilder::buildPerson('male'));
        $this->queueForDelete($persona);
        $this->assertEquals(HttpStatus::CREATED, $persona->getResponse()->getStatusCode());
        $persona = $persona->get();
        $this->assertEquals(HttpStatus::OK, $persona->getResponse()->getStatusCode());

        $person->addPersonaPersonState($persona);
        $newState = $person->loadPersonaReferences();
        $this->assertEquals(HttpStatus::OK, $newState->getResponse()->getStatusCode());

        $this->assertEquals(
            HttpStatus::OK,
            $newState->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__, $newState)
        );
        $thePerson = $newState->getPerson();
        $this->assertNotEmpty($thePerson->getEvidence(), "Evidence reference missing.");
    }

    /**
     * @link https://familysearch.org/developers/docs/api/memories/Read_Memories_Comments_usecase
     */
    public function testReadMemoriesComments()
    {
        $factory = new FamilySearchStateFactory();
        /** @var FamilySearchMemories $memories */
        $memories = $factory->newMemoriesState();
        $this->authorize($memories);
        $this->assertNotEmpty($memories->getAccessToken());

        $ds = new DataSource();
        $ds->setTitle("Sample Memory");
        $ds->setFile(ArtifactBuilder::makeTextFile());
        /** @var FamilySearchSourceDescriptionState $artifact */
        $artifact = $memories->addArtifact($ds);
        $this->queueForDelete($artifact);
        $this->assertEquals(HttpStatus::CREATED, $artifact->getResponse()->getStatusCode());
        $artifact = $artifact->get();
        $this->assertEquals(HttpStatus::OK, $artifact->getResponse()->getStatusCode());

        $commentText = "Test comment.";
        /** @var DiscussionState $state */
        $comments = $artifact->readComments();
        $comment = new Comment();
        $comment->setText($commentText);
        $comments->addComment($comment);
        $state = $artifact->readComments();

        $this->assertNotNull($state->ifSuccessful());
        $this->assertEquals(HttpStatus::OK, $state->getResponse()->getStatusCode());
        $this->assertNotNull($state->getDiscussion());
        $commentList = $state->getDiscussion()->getComments();
        $this->assertNotNull($commentList);
        $this->assertGreaterThan(0, count($commentList));
        $commentItem = array_shift($commentList);
        $this->assertEquals($commentText, $commentItem->getText());
    }

    /**
     * @link https://familysearch.org/developers/docs/api/memories/Read_Memories_for_a_User_usecase
     */
    public function testReadMemoriesForAUser()
    {
        $factory = new FamilyTreeStateFactory();
        /** @var FamilySearchMemories $memories */
        $memories = $factory->newMemoriesState();
        $memories = $this->authorize($memories);
        $this->assertNotEmpty($memories->getAccessToken());
        $memories = $memories->get();
        $this->assertEquals(HttpStatus::OK, $memories->getResponse()->getStatusCode());

        $results = $memories->readResourcesOfCurrentUser();

        $this->assertEquals(
            HttpStatus::OK,
            $results->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__, $results)
        );
        $this->assertNotNull($results->getEntity());
        $sds = $results->getEntity()->getSourceDescriptions();
        $this->assertNotEmpty($sds);
        $this->assertGreaterThan(0, count($sds));
    }

    /**
     * @link https://familysearch.org/developers/docs/api/memories/Read_Memory_usecase
     */
    public function testReadMemory()
    {
        $filename = ArtifactBuilder::makeTextFile();
        $artifact = new DataSource();
        $artifact->setFile($filename);

        $description = SourceBuilder::newSource();

        $factory = new FamilySearchStateFactory();
        /** @var FamilySearchMemories $memories */
        $memories = $factory->newMemoriesState();
        $memories = $this->authorize($memories);
        $this->assertNotEmpty($memories->getAccessToken());

        $upload = $memories->addArtifact($artifact, $description);
        $this->queueForDelete($upload);
        $this->assertEquals(HttpStatus::CREATED, $upload->getResponse()->getStatusCode());

        $upload = $upload->get();

        $this->assertEquals(
            HttpStatus::OK,
            $upload->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__, $upload)
        );
    }

    /**
     * @link https://familysearch.org/developers/docs/api/memories/Read_Memory_Persona_usecase
     */
    public function testReadMemoryPersona()
    {
        $filename = ArtifactBuilder::makeImage();
        $artifact = new DataSource();
        $artifact->setFile($filename);

        $description = SourceBuilder::newSource();

        $factory = new FamilyTreeStateFactory();
        /** @var FamilySearchMemories $memories */
        $memories = $factory->newMemoriesState();
        $memories = $this->authorize($memories);
        $this->assertNotEmpty($memories->getAccessToken());

        /** @var \Gedcomx\Rs\Client\SourceDescriptionState $upload */
        $upload = $memories->addArtifact($artifact, $description);
        $this->queueForDelete($upload);
        $this->assertEquals(HttpStatus::CREATED, $upload->getResponse()->getStatusCode());
        $upload = $upload->get();
        $this->assertEquals(HttpStatus::OK, $upload->getResponse()->getStatusCode());

        $person = PersonBuilder::buildPerson('male');

        $persona = $upload->addPersonPersona($person);
        $this->queueForDelete($persona);
        $this->assertEquals(HttpStatus::CREATED, $persona->getResponse()->getStatusCode());

        $persona = $persona->get();
        $this->assertEquals(
            HttpStatus::OK,
            $persona->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__.'(Status:OK)', $persona)
        );
        $persons = $persona->getPersons();
        $this->assertNotNull($persons);
        $this->assertGreaterThan(
            0,
            count($persons),
            $this->buildFailMessage(__METHOD__.'(HasPersons)', $persona)
        );
    }

    /**
     * @link https://familysearch.org/developers/docs/api/memories/Read_Memory_Personas_usecase
     */
    public function testReadMemoriesPersonas()
    {
        $filename = ArtifactBuilder::makeImage();
        $artifact = new DataSource();
        $artifact->setFile($filename);

        $description = SourceBuilder::newSource();

        $factory = new FamilyTreeStateFactory();
        /** @var FamilySearchMemories $memories */
        $memories = $factory->newMemoriesState();
        $memories = $this->authorize($memories);
        $this->assertNotEmpty($memories->getAccessToken());

        /** @var \Gedcomx\Rs\Client\SourceDescriptionState $upload */
        $upload = $memories->addArtifact($artifact, $description);
        $this->queueForDelete($upload);
        $this->assertEquals(HttpStatus::CREATED, $upload->getResponse()->getStatusCode());
        $upload = $upload->get();
        $this->assertEquals(HttpStatus::OK, $upload->getResponse()->getStatusCode());

        $person = PersonBuilder::buildPerson('male');

        $persona = $upload->addPersonPersona($person);
        $this->queueForDelete($persona);
        $this->assertEquals(HttpStatus::CREATED, $persona->getResponse()->getStatusCode());

        $personas = $upload->readPersonas();
        $this->assertEquals(HttpStatus::OK, $personas->getResponse()->getStatusCode());

        $this->assertEquals(
            HttpStatus::OK,
            $personas->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__.'(Status:OK)', $personas)
        );
        $persons = $personas->getPersons();
        $this->assertGreaterThan(
            0,
            count($persons),
            $this->buildFailMessage(__METHOD__.'(HasPersons)', $personas)
        );
    }

    /**
     * @link https://familysearch.org/developers/docs/api/memories/Update_Memories_Comment_usecase
     */
    public function testUpdateMemoriesComment()
    {
        $factory = new FamilySearchStateFactory();
        /** @var FamilySearchMemories $memories */
        $memories = $factory->newMemoriesState();
        $this->authorize($memories);
        $this->assertNotEmpty($memories->getAccessToken());

        $ds = new DataSource();
        $ds->setTitle("Sample Memory");
        $ds->setFile(ArtifactBuilder::makeTextFile());
        /** @var FamilySearchSourceDescriptionState $artifact */
        $artifact = $memories->addArtifact($ds);
        $this->queueForDelete($artifact);
        $this->assertEquals(HttpStatus::CREATED, $artifact->getResponse()->getStatusCode());
        $artifact = $artifact->get();
        $this->assertEquals(HttpStatus::OK, $artifact->getResponse()->getStatusCode());

        $comments = $artifact->readComments();
        $comment = new Comment();
        $comment->setText("Test comment.");
        $comments->addComment($comment);
        $comments = $artifact->readComments();
        $this->assertEquals(HttpStatus::OK, $comments->getResponse()->getStatusCode());
        $this->assertNotNull($comments->getDiscussion());
        $entities = $comments->getDiscussion()->getComments();
        $this->assertNotEmpty($entities);
        $update = array_shift($entities);
        $commentText = "Updated comment";
        $update->setText($commentText);
        $state = $comments->updateComment($update);

        $this->assertNotNull($state->ifSuccessful());
        $this->assertEquals(HttpStatus::NO_CONTENT, $state->getResponse()->getStatusCode());

        $comments = $artifact->readComments();
        $this->assertEquals(HttpStatus::OK, $comments->getResponse()->getStatusCode());
        $this->assertNotNull($comments->getDiscussion());
        $entities = $comments->getDiscussion()->getComments();
        $this->assertNotEmpty($entities);
        $update = array_shift($entities);
        $this->assertEquals($commentText, $update->getText());
    }

    /**
     * @link https://familysearch.org/developers/docs/api/memories/Update_Memory_Description_usecase
     */
    public function testUpdateMemoryDescription()
    {
        $filename = ArtifactBuilder::makeImage();
        $artifact = new DataSource();
        $artifact->setFile($filename);

        $description = SourceBuilder::newSource();

        $factory = new FamilySearchStateFactory();
        /** @var FamilySearchMemories $memories */
        $memories = $factory->newMemoriesState();
        $memories = $this->authorize($memories);
        $this->assertNotEmpty($memories->getAccessToken());

        $upload = $memories->addArtifact($artifact, $description)->get();
        $this->queueForDelete($upload);
        $this->assertEquals(HttpStatus::OK, $upload->getResponse()->getStatusCode());

        /** @var Gedcomx $gedcom */
        $gedcom = $upload->getEntity();
        $this->assertNotNull($gedcom);
        $descriptions = $gedcom->getSourceDescriptions();
        $this->assertNotEmpty($descriptions);
        foreach ($descriptions as $source) {
            foreach ($source->getDescriptions() as $d) {
                $d->setValue($this->faker->sentence(3));
            }
        }

        /** @var FamilySearchMemories $updated */
        $updated = $upload->update($gedcom);

        $this->assertEquals(
            HttpStatus::NO_CONTENT,
            $updated->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__, $updated)
        );
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Delete_Person_Memory_Reference_usecase
     */
    public function testDeletePersonMemoryReference()
    {
        $filename = ArtifactBuilder::makeImage();
        $artifact = new DataSource();
        $artifact->setFile($filename);

        $description = SourceBuilder::newSource();

        $factory = new FamilyTreeStateFactory();
        /** @var FamilySearchMemories $memories */
        $memories = $factory->newMemoriesState();
        $memories = $this->authorize($memories);
        $this->assertNotEmpty($memories->getAccessToken());

        /** @var \Gedcomx\Rs\Client\SourceDescriptionState $upload */
        $upload = $memories->addArtifact($artifact, $description);
        $this->queueForDelete($upload);
        $this->assertEquals(HttpStatus::CREATED, $upload->getResponse()->getStatusCode());
        $upload = $upload->get();
        $this->assertEquals(HttpStatus::OK, $upload->getResponse()->getStatusCode());

        $this->collectionState($factory);
        /** @var FamilyTreePersonState $person */
        $person = $this->createPerson('male');
        $this->assertEquals(HttpStatus::CREATED, $person->getResponse()->getStatusCode());
        $person = $person->get();
        $this->assertEquals(HttpStatus::OK, $person->getResponse()->getStatusCode());

        $persona = $upload->addPersonPersona(PersonBuilder::buildPerson('male'));
        $this->queueForDelete($persona);
        $this->assertEquals(HttpStatus::CREATED, $persona->getResponse()->getStatusCode());
        $persona = $persona->get();
        $this->assertEquals(HttpStatus::OK, $persona->getResponse()->getStatusCode());

        $person->addPersonaPersonState($persona);
        $person = $person->loadPersonaReferences();
        $this->assertEquals(HttpStatus::OK, $person->getResponse()->getStatusCode());
        $this->assertNotNull($person->getPerson());

        $evidence = $person->getPerson()->getEvidence();
        $this->assertNotEmpty($evidence);
        $newState = $person->deleteEvidenceReference($evidence[0]);

        $this->assertEquals(
            HttpStatus::NO_CONTENT,
            $newState->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__, $newState)
        );

        $person = $person->get();
        $this->assertEquals(HttpStatus::OK, $person->getResponse()->getStatusCode());
        $this->assertNotNull($person->getPerson());
        $this->assertEmpty($person->getPerson()->getEvidence());
    }

    /**
     * @link https://familysearch.org/developers/docs/api/memories/Update_Memory_Persona_usecase
     */
    public function testUpdateMemoryPersona()
    {
        $filename = ArtifactBuilder::makeImage();
        $artifact = new DataSource();
        $artifact->setFile($filename);

        $description = SourceBuilder::newSource();

        $factory = new FamilyTreeStateFactory();
        /** @var FamilySearchMemories $memories */
        $memories = $factory->newMemoriesState();
        $memories = $this->authorize($memories);
        $this->assertNotEmpty($memories->getAccessToken());

        /** @var \Gedcomx\Rs\Client\SourceDescriptionState $upload */
        $upload = $memories->addArtifact($artifact, $description);
        $this->assertEquals(HttpStatus::CREATED, $upload->getResponse()->getStatusCode());
        $upload = $upload->get();
        $this->assertEquals(HttpStatus::OK, $upload->getResponse()->getStatusCode());

        $factory = new StateFactory();
        $this->collectionState($factory);
        /** @var FamilyTreePersonState $person1 */
        $person1 = $this->createPerson('male');
        $this->assertEquals(HttpStatus::CREATED, $person1->getResponse()->getStatusCode());
        $person1 = $person1->get();
        $this->assertEquals(HttpStatus::OK, $person1->getResponse()->getStatusCode());
        $person2 = PersonBuilder::buildPerson('male');

        $persona = $upload->addPersonPersona($person2);
        $this->assertEquals(HttpStatus::CREATED, $persona->getResponse()->getStatusCode());
        $person1->addPersonaPersonState($persona);
        $personas = $person1->loadPersonaReferences();
        $this->assertEquals(HttpStatus::OK, $personas->getResponse()->getStatusCode());
        $personas = $personas->get();
        $this->assertEquals(HttpStatus::OK, $personas->getResponse()->getStatusCode());

        /** @var PersonState $updated */
        $updated = $personas->update($personas->getPerson());
        $this->assertEquals(
            HttpStatus::NO_CONTENT,
            $updated->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__, $updated)
        );

        $upload->delete();
        $person1->delete();
    }

    /**
     * @link https://familysearch.org/developers/docs/api/memories/Delete_Memory_usecase
     */
    public function testDeleteMemory()
    {
        $filename = ArtifactBuilder::makeTextFile();
        $artifact = new DataSource();
        $artifact->setFile($filename);

        $description = SourceBuilder::newSource();

        $factory = new FamilySearchStateFactory();
        /** @var FamilySearchMemories $memories */
        $memories = $factory->newMemoriesState();
        $memories = $this->authorize($memories);
        $this->assertNotEmpty($memories->getAccessToken());

        /** @var SourceDescriptionState $upload */
        $upload = $memories->addArtifact($artifact, $description);
        $this->assertEquals(HttpStatus::CREATED, $upload->getResponse()->getStatusCode());
        $upload = $upload->delete();

        $this->assertEquals(
            HttpStatus::NO_CONTENT,
            $upload->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__, $upload)
        );

        $upload = $upload->get();
        $this->assertEquals(HttpStatus::NOT_FOUND, $upload->getResponse()->getStatusCode());
    }

    /**
     * @link https://familysearch.org/developers/docs/api/memories/Delete_Memories_Comment_usecase
     */
    public function testDeleteMemoriesComment()
    {
        $factory = new FamilyTreeStateFactory();
        /** @var FamilySearchMemories $memories */
        $memories = $factory->newMemoriesState();
        $this->authorize($memories);
        $this->assertNotEmpty($memories->getAccessToken());

        $file = ArtifactBuilder::makeTextFile();
        $ds = new DataSource();
        $ds->setFile($file);
        /** @var FamilySearchSourceDescriptionState $artifact */
        $artifact = $memories->addArtifact($ds);
        $this->queueForDelete($artifact);
        $this->assertEquals(HttpStatus::CREATED, $artifact->getResponse()->getStatusCode());
        $artifact = $artifact->get();
        $this->assertEquals(HttpStatus::OK, $artifact->getResponse()->getStatusCode());

        $comments = $artifact->readComments();
        $comment = new Comment();
        $comment->setText("Test comment.");
        $comments->addComment($comment);
        $comments = $artifact->readComments();
        $this->assertEquals(HttpStatus::OK, $comments->getResponse()->getStatusCode());
        $this->assertNotNull($comments->getDiscussion());
        $entities = $comments->getDiscussion()->getComments();
        $this->assertNotEmpty($entities);
        $delete = array_shift($entities);
        $state = $comments->deleteComment($delete);

        $this->assertNotNull($state->ifSuccessful());
        $this->assertEquals(HttpStatus::NO_CONTENT, $state->getResponse()->getStatusCode());

        $comments = $artifact->readComments();
        $this->assertEquals(HttpStatus::OK, $comments->getResponse()->getStatusCode());
        $this->assertNotNull($comments->getDiscussion());
        $entities = $comments->getDiscussion()->getComments();
        $this->assertEmpty($entities);
    }

    /**
     * @link https://familysearch.org/developers/docs/api/memories/Delete_Memory_Persona_usecase
     */
    public function testDeleteMemoryPersona()
    {
        $filename = ArtifactBuilder::makeImage();
        $artifact = new DataSource();
        $artifact->setFile($filename);

        $description = SourceBuilder::newSource();

        $factory = new FamilyTreeStateFactory();
        /** @var FamilySearchMemories $memories */
        $memories = $factory->newMemoriesState();
        $memories = $this->authorize($memories);
        $this->assertNotEmpty($memories->getAccessToken());

        /** @var \Gedcomx\Rs\Client\SourceDescriptionState $upload */
        $upload = $memories->addArtifact($artifact, $description);
        $this->queueForDelete($upload);
        $this->assertEquals(HttpStatus::CREATED, $upload->getResponse()->getStatusCode());
        $upload = $upload->get();
        $this->assertEquals(HttpStatus::OK, $upload->getResponse()->getStatusCode());

        $person = PersonBuilder::buildPerson('male');

        $persona = $upload->addPersonPersona($person);
        $this->assertEquals(HttpStatus::CREATED, $persona->getResponse()->getStatusCode());
        /** @var FamilyTreePersonState $personas */
        $personas = $upload->readPersonas();
        $this->assertEquals(HttpStatus::OK, $personas->getResponse()->getStatusCode());
        $this->assertNotNull($personas->getPersons());
        $this->assertEquals(1, count($personas->getPersons()));
        $persona = $persona->delete();
        $this->assertEquals(
            HttpStatus::NO_CONTENT,
            $persona->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__, $persona)
        );
        $personas = $upload->readPersonas();
        $this->assertEquals(HttpStatus::NO_CONTENT, $personas->getResponse()->getStatusCode());
        $this->assertNull($personas->getPersons());
    }

    /**
     * @link https://familysearch.org/developers/docs/api/memories/Upload_PDF_Document_usecase
     * SDK only supports uploading via multi-part form uploads.
     */
    public function testUploadPdf()
    {
        $filename = __DIR__ . '/../artifact.pdf';
        $artifact = new DataSource();
        $artifact->setFile($filename);

        $description = SourceBuilder::newSource();
        $factory = new FamilySearchStateFactory();
        /** @var FamilySearchMemories $memories */
        $memories = $factory->newMemoriesState();
        $memories = $this->authorize($memories);
        $this->assertNotEmpty($memories->getAccessToken());

        $upload = $memories->addArtifact($artifact, $description);
        $this->queueForDelete($upload);
        $this->assertEquals(
            HttpStatus::CREATED,
            $upload->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__, $upload)
        );
        $upload = $upload->get();
        $this->assertEquals(HttpStatus::OK, $upload->getResponse()->getStatusCode());
    }

    /**
     * @link https://familysearch.org/developers/docs/api/memories/Upload_Photo_usecase
     * Use shows uploading a photo with an image content type. SDK only supports uploading
     *       via multi-part form uploads.
     */
    public function testUploadPhoto()
    {
        $filename = ArtifactBuilder::makeImage();
        $artifact = new DataSource();
        $artifact->setFile($filename);
        $artifact->setTitle($this->faker->sentence(4));

        $factory = new FamilySearchStateFactory();
        /** @var FamilySearchMemories $memories */
        $memories = $factory->newMemoriesState();
        $memories = $this->authorize($memories);
        $this->assertNotEmpty($memories->getAccessToken());

        $upload = $memories->addArtifact($artifact);
        $this->queueForDelete($upload);

        $this->assertEquals(
            HttpStatus::CREATED,
            $upload->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__, $upload)
        );
        $upload = $upload->get();
        $this->assertEquals(HttpStatus::OK, $upload->getResponse()->getStatusCode());
    }

    /**
     * @link https://familysearch.org/developers/docs/api/memories/Upload_Photo_Via_Multipart_Form_Data_usecase
     * SDK only supports uploading via multi-part form uploads.
     */
    public function testUploadPhotoViaMultipartFormData()
    {
        $filename = ArtifactBuilder::makeImage();
        $artifact = new DataSource();
        $artifact->setFile($filename);

        $description = SourceBuilder::newSource();

        $factory = new FamilySearchStateFactory();
        /** @var FamilySearchMemories $memories */
        $memories = $factory->newMemoriesState();
        $memories = $this->authorize($memories);
        $this->assertNotEmpty($memories->getAccessToken());

        $upload = $memories->addArtifact($artifact, $description);
        $this->queueForDelete($upload);

        $this->assertEquals(
            HttpStatus::CREATED,
            $upload->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__, $upload)
        );
        $upload = $upload->get();
        $this->assertEquals(HttpStatus::OK, $upload->getResponse()->getStatusCode());
    }

    /**
     * @link https://familysearch.org/developers/docs/api/memories/Upload_Story_usecase
     * SDK only supports uploading via multi-part form uploads.
     */
    public function testUploadStory()
    {
        $filename = ArtifactBuilder::makeTextFile();
        $artifact = new DataSource();
        $artifact->setFile($filename);

        $description = SourceBuilder::newSource();

        $factory = new FamilySearchStateFactory();
        /** @var FamilySearchMemories $memories */
        $memories = $factory->newMemoriesState();
        $memories = $this->authorize($memories);
        $this->assertNotEmpty($memories->getAccessToken());

        $upload = $memories->addArtifact($artifact, $description);
        $this->queueForDelete($upload);

        $this->assertEquals(
            HttpStatus::CREATED,
            $upload->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__, $upload)
        );
        $upload = $upload->get();
        $this->assertEquals(HttpStatus::OK, $upload->getResponse()->getStatusCode());
    }
}