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

interface GedcomxModelVisitor
{
    function visitGedcomx(Gedcomx $gx);

    function visitDocument(Document $document);

    function visitPlaceDescription(PlaceDescription $place);

    function visitEvent(Event $event);

    function visitEventRole(EventRole $role);

    function visitAgent(Agent $agent);

    function visitSourceDescription(SourceDescription $sourceDescription);

    function visitSourceCitation(SourceCitation $citation);

    function visitCollection(Collection $collection);

    function visitRecordDescriptor(RecordDescriptor $recordDescriptor);

    function visitField(Field $field);

    function visitFieldValue(FieldValue $fieldValue);

    function visitRelationship(Relationship $relationship);

    function visitPerson(Person $person);

    function visitFact(Fact $fact);

    function visitPlaceReference(PlaceReference $place);

    function visitDate(DateInfo $date);

    function visitName(Name $name);

    function visitNameForm(NameForm $form);

    function visitNamePart(NamePart $part);

    function visitGender(Gender $gender);

    function visitSourceReference(SourceReference $sourceReference);

    function visitNote(Note $note);

    function visitEvidenceReference(EvidenceReference $evidenceReference);
}