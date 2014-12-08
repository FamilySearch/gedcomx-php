<?php

namespace Gedcomx\Rs\Client;

use Gedcomx\Rs\Client\Util\RdfCollection;
use Gedcomx\Rs\Client\Util\RdfNode;
use Gedcomx\Vocab\VocabElement;
use Gedcomx\Vocab\VocabElementList;
use Guzzle\Http\Message\Request;
use Guzzle\Http\Message\Response;
use ML\JsonLD\JsonLD;
use ML\JsonLD\RdfConstants;

/**
 * The VocabElementListState exposes management functions for a vocab element list.
 */
class VocabElementListState extends GedcomxApplicationState
{
    /**
     * @var \Gedcomx\Rs\Client\Util\RdfCollection
     */
    private $rdfCollection;

    /**
     * Clones the current state instance.
     *
     * @param \Guzzle\Http\Message\Request  $request
     * @param \Guzzle\Http\Message\Response $response
     *
     * @return \Gedcomx\Rs\Client\VocabElementListState
     */
    protected function reconstruct(Request $request, Response $response)
    {
        return new VocabElementListState($this->client, $request, $response, $this->accessToken, $this->stateFactory);
    }

    /**
     * Parses an RDF collection from the response data.
     *
     * @return null
     */
    protected function loadEntity()
    {
        $input = $this->getResponse()->getBody(true);
        $options = array("");
        $this->rdfCollection = new RdfCollection(JsonLD::toRdf($input, $options));

        return null;
    }

    /**
     * Gets the main data element represented by this state instance.
     *
     * @return null
     */
    protected function getScope()
    {
        return null;
    }

    /**
     * Gets the rel name for the current state instance.
     *
     * @return string
     */
    public function getSelfRel()
    {
        return Rel::DESCRIPTION;
    }

    /**
     * Gets the vocab element list associated with this state instance.
     *
     * @return \Gedcomx\Vocab\VocabElementList
     */
    public function getVocabElementList()
    {
        /** @var \Gedcomx\Rs\Client\Util\RdfCollection $rootQuads */
        $rootQuads = $this->rdfCollection->quadsMatchingSubject($this->request->getUrl());

        $vocabElementList = new VocabElementList();
        $idQuad = $rootQuads->getPropertyQuad(VocabConstants::DC_NAMESPACE . "identifier");
        if ($idQuad != null) {
            $vocabElementList->setId(RdfNode::getValue($idQuad->getObject()));
        }
        $vocabElementList->setUri(RdfNode::getValue($rootQuads->first()->getSubject()));

        $titleQuad = $rootQuads->getPropertyQuad(VocabConstants::DC_NAMESPACE . "title");
        $vocabElementList->setTitle(RdfNode::getValue($titleQuad->getObject()));

        $descriptionQuad = $rootQuads->getPropertyQuad(VocabConstants::DC_NAMESPACE . "description");
        $vocabElementList->setDescription(RdfNode::getValue($descriptionQuad->getObject()));

        $firstQuads = $this->rdfCollection->quadsMatchingProperty(RdfConstants::RDF_FIRST);
        foreach ($firstQuads as $element) {
            /** @var \ML\JsonLD\TypedValue $node */
            $node = $element->getObject();
            $quads = $this->rdfCollection->quadsMatchingSubject(RdfNode::getValue($node));

            $vocabElementList->addElement($this->mapToVocabElement($quads));
        }

        return $vocabElementList;
    }

    /**
     * Map a RDF resource that represents a vocabulary element to a GedcomX vocabulary element.
     *
     * @param \Gedcomx\Rs\Client\Util\RdfCollection $quads
     *
     * @return \Gedcomx\Vocab\VocabElement
     */
    private function mapToVocabElement(RdfCollection $quads)
    {
        $vocabElement = new VocabElement();
        $idQuad = $quads->getPropertyQuad(VocabConstants::DC_NAMESPACE . "identifier");
        $vocabElement->setId(RdfNode::getValue($idQuad->getObject()));
        $vocabElement->setUri(RdfNode::getValue($quads->first()->getSubject()));

        $subclass = $quads->getPropertyQuad(VocabConstants::RDFS_NAMESPACE . "subClassOf");
        if ($subclass != null) {
            $vocabElement->setSubclass(RdfNode::getValue($subclass->getObject()));
        }

        $type = $quads->getPropertyQuad(VocabConstants::DC_NAMESPACE . "type");
        if ($type != null) {
            $vocabElement->setType(RdfNode::getValue($type->getObject()));
        }

        $labels = $quads->quadsMatchingProperty(VocabConstants::RDFS_NAMESPACE . "label");
        if ($labels->count()) {
            foreach ($labels as $label) {
                $node = $label->getObject();
                $vocabElement->addLabel(RdfNode::getValue($node), RdfNode::getLanguage($node));
            }
        }

        $comments = $quads->quadsMatchingProperty(VocabConstants::RDFS_NAMESPACE . "comment");
        if ($comments->count()) {
            foreach ($comments as $comment) {
                $node = $comment->getObject();
                $vocabElement->addDescription(RdfNode::getValue($node), RdfNode::getLanguage($node));
            }
        }

        return $vocabElement;
    }
}