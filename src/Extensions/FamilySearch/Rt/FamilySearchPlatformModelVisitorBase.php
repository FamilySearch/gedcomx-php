<?php

namespace Gedcomx\Extensions\FamilySearch\Rt;

use Gedcomx\Conclusion\Fact;
use Gedcomx\Extensions\FamilySearch\FamilySearchPlatform;
use Gedcomx\Extensions\FamilySearch\Platform\Discussions\Comment;
use Gedcomx\Extensions\FamilySearch\Platform\Discussions\Discussion;
use Gedcomx\Extensions\FamilySearch\Platform\Tree\ChildAndParentsRelationship;
use Gedcomx\Extensions\FamilySearch\Platform\Tree\Merge;
use Gedcomx\Extensions\FamilySearch\Platform\Tree\MergeAnalysis;
use Gedcomx\Extensions\FamilySearch\Platform\Users\User;
use Gedcomx\Gedcomx;
use Gedcomx\Rt\GedcomxModelVisitorBase;

class FamilySearchPlatformModelVisitorBase extends GedcomxModelVisitorBase implements FamilySearchPlatformModelVisitor
{
    public function visitFamilySearchPlatform(FamilySearchPlatform $fsp)
    {
        $this->visitGedcomx($fsp);

        array_push($this->contextStack, $fsp);
        $discussions = $fsp->getDiscussions();
        if ($discussions != null) {
            /** @var $discussion Discussion */
            foreach ($discussions as $discussion) {
                $discussion->accept($this);
            }
        }

        $merges = $fsp->getMerges();
        if ($merges != null) {
            /** @var Merge $merge */
            foreach ($merges as $merge) {
                $merge->accept($this);
            }
        }

        $mergeAnalyses = $fsp->getMergeAnalyses();
        if ($mergeAnalyses != null) {
            /** @var MergeAnalysis $merge */
            foreach ($mergeAnalyses as $merge) {
                $merge->accept($this);
            }
        }

        $childAndParentsRelationships = $fsp->getChildAndParentsRelationships();
        if ($childAndParentsRelationships != null) {
            /** @var ChildAndParentsRelationship $pcr */
            foreach ($childAndParentsRelationships as $pcr) {
                $pcr->accept($this);
            }
        }

        $users = $fsp->getUsers();
        if ($users != null) {
            /** @var User $user */
            foreach ($users as $user) {
                $user->accept($this);
            }
        }

        array_pop($this->contextStack);
    }

    public function visitGedcomx(Gedcomx $gx)
    {
        parent::visitGedcomx($gx);
        array_push($this->contextStack, $gx);
        $discussions = $gx->findExtensionsOfType('Discussion');
        if ($discussions != null) {
            /** @var Discussion $discussion */
            foreach ($discussions as $discussion) {
                $discussion->accept($this);
            }
        }

        $merges = $gx->findExtensionsOfType('Merge');
        if ($merges != null) {
            /** @var Merge $merge */
            foreach ($merges as $merge) {
                $merge->accept($this);
            }
        }

        $mergeAnalyses = $gx->findExtensionsOfType('MergeAnalysis');
        if ($mergeAnalyses != null) {
            /** @var MergeAnalysis $merge */
            foreach ($mergeAnalyses as $merge) {
                $merge->accept($this);
            }
        }

        $childAndParentsRelationships = $gx->findExtensionsOfType('ChildAndParentsRelationship');
        if ($childAndParentsRelationships != null) {
            /** @var ChildAndParentsRelationship $pcr */
            foreach ($childAndParentsRelationships as $pcr) {
                $pcr->accept($this);
            }
        }

        array_pop($this->contextStack);
    }

    public function visitChildAndParentsRelationship(ChildAndParentsRelationship $pcr)
    {
        array_push($this->contextStack, $pcr);
        $this->visitConclusion($pcr);

        $facts = $pcr->getFatherFacts();
        if ($facts != null) {
            /** @var Fact $fact */
            foreach ($facts as $fact) {
                $fact->accept($this);
            }
        }

        $facts = $pcr->getMotherFacts();
        if ($facts != null) {
            /** @var Fact $fact */
            foreach ($facts as $fact) {
                $fact->accept($this);
            }
        }

        array_pop($this->contextStack);
    }

    public function visitMergeAnalysis(MergeAnalysis $merge)
    {
        //no-op.
    }

    public function visitMerge(Merge $merge)
    {
        //no-op.
    }

    public function visitDiscussion(Discussion $discussion)
    {
        array_push($this->contextStack, $discussion);
        $comments = $discussion->getComments();
        if ($comments != null) {
            /** @var Comment $comment */
            foreach ($comments as $comment) {
                $comment->accept($this);
            }
        }
        array_pop($this->contextStack);
    }

    public function visitComment(Comment $comment)
    {
        //no-op.
    }

    public function visitUser(User $user)
    {
        //no-op.
    }
}