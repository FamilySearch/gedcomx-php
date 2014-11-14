<?php

namespace Gedcomx\Extensions\FamilySearch\Rt;

use Gedcomx\Agent\Agent;
use Gedcomx\Common\EvidenceReference;
use Gedcomx\Common\ExtensibleData;
use Gedcomx\Common\Note;
use Gedcomx\Common\ResourceReference;
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
use Gedcomx\Extensions\FamilySearch\Platform\Discussions\Comment;
use Gedcomx\Extensions\FamilySearch\Platform\Discussions\Discussion;
use Gedcomx\Extensions\FamilySearch\Platform\Tree\ChildAndParentsRelationship;
use Gedcomx\Extensions\FamilySearch\Platform\Users\User;
use Gedcomx\Gedcomx;
use Gedcomx\Records\Collection;
use Gedcomx\Records\Field;
use Gedcomx\Records\FieldValue;
use Gedcomx\Records\RecordDescriptor;
use Gedcomx\Source\SourceCitation;
use Gedcomx\Source\SourceDescription;
use Gedcomx\Source\SourceReference;

class FamilySearchPlatformLocalReferenceResolver extends FamilySearchPlatformModelVisitorBase
{
    private $resourceId;
    protected $resource;

    public function __construct($resourceId)
    {
        $this->resourceId = $resourceId;
    }

    public static function resolveByRef(ResourceReference $ref, Gedcomx $document)
    {
        if ($ref->getResource() === null) {
            return null;
        }

        return FamilySearchPlatformLocalReferenceResolver::resolveByString($ref->getResource(), $document);
    }

    public static function resolveByString($ref, Gedcomx $document)
    {
        if (substr($ref, 0, 1) != "#") {
            return null;
        }

        $visitor = new FamilySearchPlatformLocalReferenceResolver(substr($ref, 1));
        $document->accept($visitor);
        return $visitor->getResource();
    }

    public function getResource()
    {
        return $this->resource;
    }

    protected function bindIfNeeded(ExtensibleData $candidate)
    {
        if ($this->resource === null && $this->resourceId == $candidate->getId()) {
            $this->resource = $candidate;
        }
    }

    public function visitChildAndParentsRelationship(ChildAndParentsRelationship $pcr)
    {
        $this->bindIfNeeded($pcr);
        parent::visitChildAndParentsRelationship($pcr);
    }

    public function visitDiscussion(Discussion $discussion)
    {
        $this->bindIfNeeded($discussion);
        parent::visitDiscussion($discussion);
    }

    public function visitComment(Comment $comment)
    {
        $this->bindIfNeeded($comment);
        parent::visitComment($comment);
    }

    public function visitGedcomx(Gedcomx $gx)
    {
        $this->bindIfNeeded($gx);
        parent::visitGedcomx($gx);
    }

    public function visitDocument(Document $document)
    {
        $this->bindIfNeeded($document);
        parent::visitDocument($document);
    }

    public function visitPlaceDescription(PlaceDescription $place)
    {
        $this->bindIfNeeded($place);
        parent::visitPlaceDescription($place);
    }

    public function visitEvent(Event $event)
    {
        $this->bindIfNeeded($event);
        parent::visitEvent($event);
    }

    public function visitEventRole(EventRole $role)
    {
        $this->bindIfNeeded($role);
        parent::visitEventRole($role);
    }

    public function visitAgent(Agent $agent)
    {
        $this->bindIfNeeded($agent);
        parent::visitAgent($agent);
    }

    public function visitSourceDescription(SourceDescription $sourceDescription)
    {
        $this->bindIfNeeded($sourceDescription);
        parent::visitSourceDescription($sourceDescription);
    }

    public function visitSourceCitation(SourceCitation $citation)
    {
        $this->bindIfNeeded($citation);
        parent::visitSourceCitation($citation);
    }

    public function visitCollection(Collection $collection)
    {
        $this->bindIfNeeded($collection);
        parent::visitCollection($collection);
    }

    public function visitRecordDescriptor(RecordDescriptor $recordDescriptor)
    {
        $this->bindIfNeeded($recordDescriptor);
        parent::visitRecordDescriptor($recordDescriptor);
    }

    public function visitField(Field $field)
    {
        $this->bindIfNeeded($field);
        parent::visitField($field);
    }

    public function visitFieldValue(FieldValue $fieldValue)
    {
        $this->bindIfNeeded($fieldValue);
        parent::visitFieldValue($fieldValue);
    }

    public function visitRelationship(Relationship $relationship)
    {
        $this->bindIfNeeded($relationship);
        parent::visitRelationship($relationship);
    }

    protected function visitConclusion(Conclusion $conclusion)
    {
        $this->bindIfNeeded($conclusion);
        parent::visitConclusion($conclusion);
    }

    protected function visitSubject(Subject $subject)
    {
        $this->bindIfNeeded($subject);
        parent::visitSubject($subject);
    }

    public function visitPerson(Person $person)
    {
        $this->bindIfNeeded($person);
        parent::visitPerson($person);
    }

    public function visitFact(Fact $fact)
    {
        $this->bindIfNeeded($fact);
        parent::visitFact($fact);
    }

    public function visitPlaceReference(PlaceReference $place)
    {
        $this->bindIfNeeded($place);
        parent::visitPlaceReference($place);
    }

    public function visitDate(DateInfo $date)
    {
        $this->bindIfNeeded($date);
        parent::visitDate($date);
    }

    public function visitName(Name $name)
    {
        $this->bindIfNeeded($name);
        parent::visitName($name);
    }

    public function visitNameForm(NameForm $form)
    {
        $this->bindIfNeeded($form);
        parent::visitNameForm($form);
    }

    public function visitNamePart(NamePart $part)
    {
        $this->bindIfNeeded($part);
        parent::visitNamePart($part);
    }

    /**
     * @param Gender $gender
     */
    public function visitGender(Gender $gender)
    {
        $this->bindIfNeeded($gender);
        parent::visitGender($gender);
    }

    public function visitSourceReference(SourceReference $sourceReference)
    {
        $this->bindIfNeeded($sourceReference);
        parent::visitSourceReference($sourceReference);
    }

    public function visitNote(Note $note)
    {
        $this->bindIfNeeded($note);
        parent::visitNote($note);
    }

    public function visitEvidenceReference(EvidenceReference $evidenceReference)
    {
        $this->bindIfNeeded($evidenceReference);
        parent::visitEvidenceReference($evidenceReference);
    }

    public function visitUser(User $user)
    {
        $this->bindIfNeeded($user);
        parent::visitUser($user);
    }
}