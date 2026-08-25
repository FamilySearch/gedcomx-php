<?php

namespace Gedcomx\Extensions\FamilySearch\Rt;

use Gedcomx\Extensions\FamilySearch\FamilySearchPlatform;
use Gedcomx\Extensions\FamilySearch\Platform\Discussions\Comment;
use Gedcomx\Extensions\FamilySearch\Platform\Discussions\Discussion;
use Gedcomx\Extensions\FamilySearch\Platform\Tree\ChildAndParentsRelationship;
use Gedcomx\Extensions\FamilySearch\Platform\Tree\Merge;
use Gedcomx\Extensions\FamilySearch\Platform\Tree\MergeAnalysis;
use Gedcomx\Extensions\FamilySearch\Platform\Users\User;
use Gedcomx\Extensions\FamilySearch\Platform\Vocab\VocabConcept;
use Gedcomx\Extensions\FamilySearch\Platform\Vocab\VocabTerm;
use Gedcomx\Extensions\FamilySearch\Platform\Vocab\VocabTranslation;
use Gedcomx\Rt\GedcomxModelVisitor;

interface FamilySearchPlatformModelVisitor extends GedcomxModelVisitor
{
    function visitFamilySearchPlatform(FamilySearchPlatform $fsp);

    function visitChildAndParentsRelationship(ChildAndParentsRelationship $pcr);

    function visitMergeAnalysis(MergeAnalysis $merge);

    function visitMerge(Merge $merge);

    function visitDiscussion(Discussion $discussion);

    function visitComment(Comment $comment);

    function visitUser(User $user);

    function visitVocabConcept(VocabConcept $vocabConcept);

    function visitVocabTerm(VocabTerm $vocabTerm);

    function visitVocabTranslation(VocabTranslation $vocabTranslation);
}