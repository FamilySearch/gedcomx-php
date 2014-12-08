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

/**
 * Basic, no-op implementation of the GEDCOM X model visitor. Intended to be extended and appropriate methods overridden as needed.
 */
class GedcomxModelVisitorBase implements GedcomxModelVisitor
{
    protected $contextStack = array();

    /**
     * Visits the Gedcomx entity.
     *
     * @param \Gedcomx\Gedcomx $gx
     */
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

    /**
     * Visits the document.
     *
     * @param \Gedcomx\Conclusion\Document $document
     */
    public function visitDocument(Document $document)
    {
        array_push($this->contextStack, $document);
        $this->visitConclusion($document);
        array_pop($this->contextStack);
    }

    /**
     * Visits the place description.
     *
     * @param \Gedcomx\Conclusion\PlaceDescription $place
     */
    public function visitPlaceDescription(PlaceDescription $place)
    {
        array_push($this->contextStack, $place);
        $this->visitSubject($place);
        array_pop($this->contextStack);
    }

    /**
     * Visits the event.
     *
     * @param \Gedcomx\Conclusion\Event $event
     */
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

    /**
     * Visits the event role.
     *
     * @param \Gedcomx\Conclusion\EventRole $role
     */
    public function visitEventRole(EventRole $role)
    {
        array_push($this->contextStack, $role);
        $this->visitConclusion($role);
        array_pop($this->contextStack);
    }

    /**
     * Visits the agent.
     *
     * @param \Gedcomx\Agent\Agent $agent
     */
    public function visitAgent(Agent $agent)
    {
        //no-op.
    }

    /**
     * Visits the source description.
     *
     * @param \Gedcomx\Source\SourceDescription $sourceDescription
     */
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

    /**
     * Visits the source citation.
     * @param \Gedcomx\Source\SourceCitation $citation
     */
    public function visitSourceCitation(SourceCitation $citation)
    {
        //no-op.
    }

    /**
     * Visits the collection.
     *
     * @param \Gedcomx\Records\Collection $collection
     */
    public function visitCollection(Collection $collection)
    {
    }

    /**
     * Visits the record descriptor.
     *
     * @param \Gedcomx\Records\RecordDescriptor $recordDescriptor
     */
    public function visitRecordDescriptor(RecordDescriptor $recordDescriptor)
    {
        //no-op.
    }

    /**
     * Visits the field.
     *
     * @param \Gedcomx\Records\Field $field
     */
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

    /**
     * Visits the field value.
     *
     * @param \Gedcomx\Records\FieldValue $fieldValue
     */
    public function visitFieldValue(FieldValue $fieldValue)
    {
        array_push($this->contextStack, $fieldValue);
        $this->visitConclusion($fieldValue);
        array_pop($this->contextStack);
    }

    /**
     * Visits the relationship.
     *
     * @param \Gedcomx\Conclusion\Relationship $relationship
     */
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

    /**
     * Visits the conclusion.
     *
     * @param \Gedcomx\Conclusion\Conclusion $conclusion
     */
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

    /**
     * Visists the subject.
     *
     * @param \Gedcomx\Conclusion\Subject $subject
     */
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

    /**
     * Visists the person.
     *
     * @param \Gedcomx\Conclusion\Person $person
     */
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

    /**
     * Visits the fact.
     * @param \Gedcomx\Conclusion\Fact $fact
     */
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

    /**
     * Visits the place reference.
     * @param \Gedcomx\Conclusion\PlaceReference $place
     */
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

    /**
     * Visits the date.
     * @param \Gedcomx\Conclusion\DateInfo $date
     */
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

    /**
     * Visits the name.
     *
     * @param \Gedcomx\Conclusion\Name $name
     */
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

    /**
     * Visits the name form.
     * @param \Gedcomx\Conclusion\NameForm $form
     */
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

    /**
     * Visits the name part.
     *
     * @param \Gedcomx\Conclusion\NamePart $part
     */
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

    /**
     * Visits the gender.
     *
     * @param \Gedcomx\Conclusion\Gender $gender
     */
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

    /**
     * Visits the source reference.
     *
     * @param \Gedcomx\Source\SourceReference $sourceReference
     */
    public function visitSourceReference(SourceReference $sourceReference)
    {
        //no-op
    }

    /**
     * Visits the note.
     *
     * @param \Gedcomx\Common\Note $note
     */
    public function visitNote(Note $note)
    {
        //no-op.
    }

    /**
     * Visits the evidence reference.
     *
     * @param \Gedcomx\Common\EvidenceReference $evidenceReference
     */
    public function visitEvidenceReference(EvidenceReference $evidenceReference)
    {
        //no-op
    }

    /**
     * Gets the context stack of objects that are currently being visited.
     * @return array
     */
    public function getContextStack()
    {
        return $this->contextStack;
    }
}