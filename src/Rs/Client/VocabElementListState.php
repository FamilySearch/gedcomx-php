<?php

namespace Gedcomx\Rs\Client;

use Gedcomx\Rs\Client\Util\RdfCollection;
use Gedcomx\Vocab\VocabElement;
use Gedcomx\Vocab\VocabElementList;
use Guzzle\Http\Message\Request;
use Guzzle\Http\Message\Response;
use ML\JsonLD\JsonLD;
use ML\JsonLD\RdfConstants;

class VocabElementListState extends GedcomxApplicationState
{
    /**
     * @var \Gedcomx\Rs\Client\Util\RdfCollection
     */
    private $rdfCollection;

    protected function reconstruct(Request $request, Response $response)
    {
        return new VocabElementListState($this->client, $request, $response, $this->accessToken, $this->stateFactory);
    }

    protected function loadEntity()
    {
        $input = $this->getResponse()->getBody(true);
        $options = array("");
        $this->rdfCollection = new RdfCollection(JsonLD::toRdf($input, $options));

        return $this;
    }

    protected function getScope()
    {
        return null;
    }

    public function getSelfRel()
    {
        return Rel::DESCRIPTION;
    }

    public function getVocabElementList()
    {
        /** @var \Gedcomx\Rs\Client\Util\RdfCollection $rootQuads */
        $rootQuads = $this->rdfCollection->quadsMatchingSubject($this->request->getUrl());

        $vocabElementList = new VocabElementList();
        $idQuad = $rootQuads->getPropertyQuad(VocabConstants::DC_NAMESPACE . "identifier");
        if ($idQuad != null) {
            $vocabElementList->setId($idQuad->getObject()->getValue());
        }
        $vocabElementList->setUri((string)$rootQuads->first()->getSubject());

        $titleQuad = $rootQuads->getPropertyQuad(VocabConstants::DC_NAMESPACE . "identifier");
        $vocabElementList->setTitle($titleQuad->getObject()->getValue());

        $descriptionQuad = $rootQuads->getPropertyQuad(VocabConstants::DC_NAMESPACE . "description");
        $vocabElementList->setTitle($descriptionQuad->getObject()->getValue());

        $firstQuads = $this->rdfCollection->quadsMatchingProperty(RdfConstants::RDF_FIRST);
        foreach ($firstQuads as $element) {
            /** @var \ML\JsonLD\TypedValue $node */
            $node = $element->getObject();
            $quads = $this->rdfCollection->quadsMatchingSubject($node->getValue());

            $vocabElementList[] = $this->mapToVocabElement($quads);
        }

        return $this;
    }

    /**
     * @param \Gedcomx\Rs\Client\Util\RdfCollection $quads
     *
     * @return \Gedcomx\Vocab\VocabElement
     */
    private function mapToVocabElement(RdfCollection $quads)
    {
        $vocabElement = new VocabElement();
        $vocabElement->setId($quads->getPropertyQuad(VocabConstants::DC_NAMESPACE . "identifier")->getObject()->getValue());
        $vocabElement->setUri((string)$quads->first()->getSubject());

        $subclass = $quads->getPropertyQuad(VocabConstants::RDFS_NAMESPACE . "subClassOf");
        if ($subclass != null) {
            $vocabElement->setSubclass($subclass->getObject()->getValue());
        }

        $type = $quads->getPropertyQuad(VocabConstants::DC_NAMESPACE . "type");
        if ($type != null) {
            $vocabElement->setType($type->getObject()->getValue());
        }

        $labels = $quads->quadsMatchingProperty(VocabConstants::RDFS_NAMESPACE . "label");
        if ($labels->count()) {
            foreach ($labels as $label) {
                $node = $label->getObject();
                $vocabElement->addLabel($node->getValue(), strtolower($node->getLanguage()));
            }
        }

        $comments = $quads->quadsMatchingProperty(VocabConstants::RDFS_NAMESPACE . "comment");
        if ($comments->count()) {
            foreach ($comments as $comment) {
                $node = $comment->getObject();
                $vocabElement->addDescription($node->getValue(), strtolower($node->getLanguage()));
            }
        }
    }

}