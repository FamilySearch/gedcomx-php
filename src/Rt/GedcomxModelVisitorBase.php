<?php

namespace Gedcomx\Rt;

use Gedcomx\Agent\Agent;
use Gedcomx\Common\EvidenceReference;
use Gedcomx\Common\Note;
use Gedcomx\Conclusion\Conclusion;
use Gedcomx\Conclusion\DateInfo;
use Gedcomx\Conclusion\Document;
use Gedcomx\Conclusion\Event;
use Gedcomx\Conclusion\EventRole;
use Gedcomx\Conclusion\Fact;
use Gedcomx\Conclusion\Gender;
use Gedcomx\Conclusion\Name;
use Gedcomx\Conclusion\NameForm;
use Gedcomx\Conclusion\NamePart;
use Gedcomx\Conclusion\Person;
use Gedcomx\Conclusion\PlaceDescription;
use Gedcomx\Conclusion\PlaceReference;
use Gedcomx\Conclusion\Relationship;
use Gedcomx\Conclusion\Subject;
use Gedcomx\Gedcomx;
use Gedcomx\Records\Collection;
use Gedcomx\Records\Field;
use Gedcomx\Records\FieldValue;
use Gedcomx\Records\RecordDescriptor;
use Gedcomx\Source\SourceCitation;
use Gedcomx\Source\SourceDescription;
use Gedcomx\Source\SourceReference;

class GedcomxModelVisitorBase implements GedcomxModelVisitor
{
    protected $contextStack = array();

    public function visitGedcomx(Gedcomx $gx)
    {
        array_push($this->contextStack, $gx);

        $persons = $gx->getPersons();
        if ($persons !== null) {
            /** @var Person $person */
            foreach ($persons as $person) {
                if ($person !== null) {
                    $person->accept($this);
                }
            }
        }

        $relationships = $gx->getRelationships();
        if ($relationships !== null) {
            /** @var Relationship $relationship */
            foreach ($relationships as $relationship) {
                if ($relationship !== null) {
                    $relationship->accept($this);
                }
            }
        }

        $sourceDescriptions = $gx->getSourceDescriptions();
        if ($sourceDescriptions !== null) {
            /** @var SourceDescription $sourceDescription */
            foreach ($sourceDescriptions as $sourceDescription) {
                if ($sourceDescription !== null) {
                    $sourceDescription->accept($this);
                }
            }
        }

        $agents = $gx->getAgents();
        if ($agents !== null) {
            /** @var Agent $agent */
            foreach ($agents as $agent) {
                if ($agent !== null) {
                    $agent->accept($this);
                }
            }
        }

        $events = $gx->getEvents();
        if ($events !== null) {
            /** @var Event $event */
            foreach ($events as $event) {
                if ($event !== null) {
                    $event->accept($this);
                }
            }
        }

        $places = $gx->getPlaces();
        if ($places !== null) {
            /** @var PlaceDescription $place */
            foreach ($places as $place) {
                if ($place !== null) {
                    $place->accept($this);
                }
            }
        }

        $documents = $gx->getDocuments();
        if ($documents !== null) {
            /** @var Document $document */
            foreach ($documents as $document) {
                if ($document !== null) {
                    $document->accept($this);
                }
            }
        }

        $fields = $gx->getFields();
        if ($fields !== null) {
            /** @var Field $field */
            foreach ($fields as $field) {
                if ($field !== null) {
                    $field->accept($this);
                }
            }
        }

        $recordDescriptors = $gx->getRecordDescriptors();
        if ($recordDescriptors !== null) {
            /** @var RecordDescriptor $rd */
            foreach ($recordDescriptors as $rd) {
                if ($rd !== null) {
                    $rd->accept($this);
                }
            }
        }

        $collections = $gx->getCollections();
        if ($collections !== null) {
            /** @var Collection $collection */
            foreach ($collections as $collection) {
                if ($collection !== null) {
                    $collection->accept($this);
                }
            }
        }

        array_pop($this->contextStack);
    }

    public function visitDocument(Document $document)
    {
        array_push($this->contextStack, $document);
        $this->visitConclusion($document);
        array_pop($this->contextStack);
    }

    public function visitPlaceDescription(PlaceDescription $place)
    {
        array_push($this->contextStack, $place);
        $this->visitSubject($place);
        array_pop($this->contextStack);
    }

    public function visitEvent(Event $event)
    {
        array_push($this->contextStack, $event);
        $this->visitSubject($event);
        $date = $event->getDate();
        if ($date !== null) {
            $date->accept($this);
        }

        /** @var PlaceReference $place */
        $place = $event->getPlace();
        if ($place !== null) {
            $place->accept($this);
        }

        $roles = $event->getRoles();
        if ($roles !== null) {
            /** @var EventRole $role */
            foreach ($roles as $role) {
                $role->accept($this);
            }
        }
        array_pop($this->contextStack);
    }

    public function visitEventRole(EventRole $role)
    {
        array_push($this->contextStack, $role);
        $this->visitConclusion($role);
        array_pop($this->contextStack);
    }

    public function visitAgent(Agent $agent)
    {
        //no-op.
    }

    public function visitSourceDescription(SourceDescription $sourceDescription)
    {
        array_push($this->contextStack, $sourceDescription);
        $sources = $sourceDescription->getSources();
        if ($sources !== null) {
            /** @var SourceReference $source */
            foreach ($sources as $source) {
                $source->accept($this);
            }
        }

        $notes = $sourceDescription->getNotes();
        if ($notes !== null) {
            /** @var Note $note */
            foreach ($notes as $note) {
                $note->accept($this);
            }
        }

        $citations = $sourceDescription->getCitations();
        if ($citations !== null) {
            /** @var SourceCitation $citation */
            foreach ($citations as $citation) {
                $citation->accept($this);
            }
        }
        array_pop($this->contextStack);
    }

    public function visitSourceCitation(SourceCitation $citation)
    {
        //no-op.
    }

    public function visitCollection(Collection $collection)
    {
    }

    public function visitRecordDescriptor(RecordDescriptor $recordDescriptor)
    {
        //no-op.
    }

    public function visitField(Field $field)
    {
        array_push($this->contextStack, $field);

        $values = $field->getValues();
        if ($values !== null) {
            /** @var FieldValue $value */
            foreach ($values as $value) {
                $value->accept($this);
            }
        }

        array_pop($this->contextStack);
    }

    public function visitFieldValue(FieldValue $fieldValue)
    {
        array_push($this->contextStack, $fieldValue);
        $this->visitConclusion($fieldValue);
        array_pop($this->contextStack);
    }

    public function visitRelationship(Relationship $relationship)
    {
        array_push($this->contextStack, $relationship);
        $this->visitSubject($relationship);

        $facts = $relationship->getFacts();
        if ($facts !== null) {
            /** @var Fact $fact */
            foreach ($facts as $fact) {
                $fact->accept($this);
            }
        }

        $fields = $relationship->getFields();
        if ($fields !== null) {
            /** @var Field $field */
            foreach ($fields as $field) {
                $field->accept($this);
            }
        }

        array_pop($this->contextStack);
    }

    protected function visitConclusion(Conclusion $conclusion)
    {
        $sourceReferences = $conclusion->getSources();
        if ($sourceReferences !== null) {
            /** @var SourceReference $sourceReference */
            foreach ($sourceReferences as $sourceReference) {
                $sourceReference->accept($this);
            }
        }

        $notes = $conclusion->getNotes();
        if ($notes !== null) {
            /** @var Note $note */
            foreach ($notes as $note) {
                $note->accept($this);
            }
        }
    }

    protected function visitSubject(Subject $subject)
    {
        $this->visitConclusion($subject);

        $media = $subject->getMedia();
        if ($media !== null) {
            /** @var SourceReference $reference */
            foreach ($media as $reference) {
                $reference->accept($this);
            }
        }

        $evidence = $subject->getEvidence();
        if ($evidence !== null) {
            /** @var EvidenceReference $evidenceReference */
            foreach ($evidence as $evidenceReference) {
                $evidenceReference->accept($this);
            }
        }
    }

    public function visitPerson(Person $person)
    {
        array_push($this->contextStack, $person);
        $this->visitSubject($person);

        if ($person->getGender() !== null) {
            $person->getGender()->accept($this);
        }

        $names = $person->getNames();
        if ($names !== null) {
            /** @var Name $name */
            foreach ($names as $name) {
                $name->accept($this);
            }
        }

        $facts = $person->getFacts();
        if ($facts !== null) {
            /** @var Fact $fact */
            foreach ($facts as $fact) {
                $fact->accept($this);
            }
        }

        $fields = $person->getFields();
        if ($fields !== null) {
            /** @var Field $field */
            foreach ($fields as $field) {
                $field->accept($this);
            }
        }
        array_pop($this->contextStack);
    }

    public function visitFact(Fact $fact)
    {
        array_push($this->contextStack, $fact);
        $this->visitConclusion($fact);
        $date = $fact->getDate();
        if ($date !== null) {
            $date->accept($this);
        }

        $place = $fact->getPlace();
        if ($place !== null) {
            $place->accept($this);
        }

        $fields = $fact->getFields();
        if ($fields !== null) {
            /** @var Field $field */
            foreach ($fields as $field) {
                $field->accept($this);
            }
        }

        array_pop($this->contextStack);
    }

    public function visitPlaceReference(PlaceReference $place)
    {
        array_push($this->contextStack, $place);
        $fields = $place->getFields();
        if ($fields !== null) {
            /** @var Field $field */
            foreach ($fields as $field) {
                $field->accept($this);
            }
        }
        array_pop($this->contextStack);
    }

    public function visitDate(DateInfo $date)
    {
        array_push($this->contextStack, $date);
        $fields = $date->getFields();
        if ($fields !== null) {
            /** @var Field $field */
            foreach ($fields as $field) {
                $field->accept($this);
            }
        }
        array_pop($this->contextStack);
    }

    public function visitName(Name $name)
    {
        array_push($this->contextStack, $name);
        $this->visitConclusion($name);

        $forms = $name->getNameForms();
        if ($forms !== null) {
            /** @var NameForm $form */
            foreach ($forms as $form) {
                $form->accept($this);
            }
        }
        array_pop($this->contextStack);
    }

    public function visitNameForm(NameForm $form)
    {
        array_push($this->contextStack, $form);
        $parts = $form->getParts();
        if ($parts !== null) {
            /** @var NamePart $part */
            foreach ($parts as $part) {
                $part->accept($this);
            }
        }

        $fields = $form->getFields();
        if ($fields !== null) {
            /** @var Field $field */
            foreach ($fields as $field) {
                $field->accept($this);
            }
        }
        array_pop($this->contextStack);
    }

    public function visitNamePart(NamePart $part)
    {
        array_push($this->contextStack, $part);
        $fields = $part->getFields();
        if ($fields !== null) {
            /** @var Field $field */
            foreach ($fields as $field) {
                $field->accept($this);
            }
        }
        array_pop($this->contextStack);
    }

    public function visitGender(Gender $gender)
    {
        array_push($this->contextStack, $gender);
        $this->visitConclusion($gender);

        $fields = $gender->getFields();
        if ($fields !== null) {
            /** @var Field $field */
            foreach ($fields as $field) {
                $field->accept($this);
            }
        }

        array_pop($this->contextStack);
    }

    public function visitSourceReference(SourceReference $sourceReference)
    {
        //no-op
    }

    public function visitNote(Note $note)
    {
        //no-op.
    }

    public function visitEvidenceReference(EvidenceReference $evidenceReference)
    {
        //no-op
    }

    public function getContextStack()
    {
        return $this->contextStack;
    }
}