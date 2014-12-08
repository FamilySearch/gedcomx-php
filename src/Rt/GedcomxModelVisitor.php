<?php

namespace Gedcomx\Rt;

use Gedcomx\Agent\Agent;
use Gedcomx\Common\EvidenceReference;
use Gedcomx\Common\Note;
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
use Gedcomx\Gedcomx;
use Gedcomx\Records\Collection;
use Gedcomx\Records\Field;
use Gedcomx\Records\FieldValue;
use Gedcomx\Records\RecordDescriptor;
use Gedcomx\Source\SourceCitation;
use Gedcomx\Source\SourceDescription;
use Gedcomx\Source\SourceReference;

/**
 * Visitor interface for the GEDCOM X model.
 */
interface GedcomxModelVisitor
{
    /**
     * Visits the Gedcomx entity.
     *
     * @param \Gedcomx\Gedcomx $gx
     *
     * @return mixed
     */
    function visitGedcomx(Gedcomx $gx);

    /**
     * Visits the document.
     *
     * @param \Gedcomx\Conclusion\Document $document
     *
     * @return mixed
     */
    function visitDocument(Document $document);

    /**
     * Visits the place description.
     *
     * @param \Gedcomx\Conclusion\PlaceDescription $place
     *
     * @return mixed
     */
    function visitPlaceDescription(PlaceDescription $place);

    /**
     * Visits the event.
     *
     * @param \Gedcomx\Conclusion\Event $event
     *
     * @return mixed
     */
    function visitEvent(Event $event);

    /**
     * Visits the event role.
     *
     * @param \Gedcomx\Conclusion\EventRole $role
     *
     * @return mixed
     */
    function visitEventRole(EventRole $role);

    /**
     * Visits the agent.
     *
     * @param \Gedcomx\Agent\Agent $agent
     *
     * @return mixed
     */
    function visitAgent(Agent $agent);

    /**
     * Visits the source description.
     *
     * @param \Gedcomx\Source\SourceDescription $sourceDescription
     *
     * @return mixed
     */
    function visitSourceDescription(SourceDescription $sourceDescription);

    /**
     * Visits the source citation.
     *
     * @param \Gedcomx\Source\SourceCitation $citation
     *
     * @return mixed
     */
    function visitSourceCitation(SourceCitation $citation);

    /**
     * Visits the collection.
     *
     * @param \Gedcomx\Records\Collection $collection
     *
     * @return mixed
     */
    function visitCollection(Collection $collection);

    /**
     * Visits the record descriptor.
     *
     * @param \Gedcomx\Records\RecordDescriptor $recordDescriptor
     *
     * @return mixed
     */
    function visitRecordDescriptor(RecordDescriptor $recordDescriptor);

    /**
     * Visits the field.
     *
     * @param \Gedcomx\Records\Field $field
     *
     * @return mixed
     */
    function visitField(Field $field);

    /**
     * Visits the field value.
     * @param \Gedcomx\Records\FieldValue $fieldValue
     *
     * @return mixed
     */
    function visitFieldValue(FieldValue $fieldValue);

    /**
     * Visits the relationship.
     *
     * @param \Gedcomx\Conclusion\Relationship $relationship
     *
     * @return mixed
     */
    function visitRelationship(Relationship $relationship);

    /**
     * Visits the person.
     *
     * @param \Gedcomx\Conclusion\Person $person
     *
     * @return mixed
     */
    function visitPerson(Person $person);

    /**
     * Visits the fact.
     *
     * @param \Gedcomx\Conclusion\Fact $fact
     *
     * @return mixed
     */
    function visitFact(Fact $fact);

    /**
     * Visits the place reference.
     *
     * @param \Gedcomx\Conclusion\PlaceReference $place
     *
     * @return mixed
     */
    function visitPlaceReference(PlaceReference $place);

    /**
     * Visits the date.
     *
     * @param \Gedcomx\Conclusion\DateInfo $date
     *
     * @return mixed
     */
    function visitDate(DateInfo $date);

    /**
     * Visits the name.
     *
     * @param \Gedcomx\Conclusion\Name $name
     *
     * @return mixed
     */
    function visitName(Name $name);

    /**
     * Visits the name form.
     *
     * @param \Gedcomx\Conclusion\NameForm $form
     *
     * @return mixed
     */
    function visitNameForm(NameForm $form);

    /**
     * Visits the name part.
     *
     * @param \Gedcomx\Conclusion\NamePart $part
     *
     * @return mixed
     */
    function visitNamePart(NamePart $part);

    /**
     * Visits the gender.
     *
     * @param \Gedcomx\Conclusion\Gender $gender
     *
     * @return mixed
     */
    function visitGender(Gender $gender);

    /**
     * Visits the source reference.
     *
     * @param \Gedcomx\Source\SourceReference $sourceReference
     *
     * @return mixed
     */
    function visitSourceReference(SourceReference $sourceReference);

    /**
     * Visits the note.
     *
     * @param \Gedcomx\Common\Note $note
     *
     * @return mixed
     */
    function visitNote(Note $note);

    /**
     * Visits the evidence reference.
     *
     * @param \Gedcomx\Common\EvidenceReference $evidenceReference
     *
     * @return mixed
     */
    function visitEvidenceReference(EvidenceReference $evidenceReference);
}