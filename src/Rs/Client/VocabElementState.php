<?php 

namespace Gedcomx\Rs\Client;

use Gedcomx\Rs\Client\Util\RdfCollection;
use Gedcomx\Rs\Client\Util\RdfNode;
use Gedcomx\Vocab\VocabElement;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use ML\JsonLD\JsonLD;

/**
 * The VocabElementState exposes management functions for a vocab element.
 */
class VocabElementState extends GedcomxApplicationState
{
    /**
     * @var \Gedcomx\Rs\Client\Util\RdfCollection
     */
    private $rdfCollection;

    /**
     * Clones the current state instance.
     *
     * @param \GuzzleHttp\Psr7\Request  $request
     * @param \GuzzleHttp\Psr7\Response $response
     *
     * @return \Gedcomx\Rs\Client\VocabElementState
     */
    protected function reconstruct(Request $request, Response $response)
    {
        return new VocabElementState($this->client, $request, $response, $this->accessToken, $this->stateFactory);
    }

    /**
     * Parses the entity from the response.
     *
     * @return null
     */
    protected function loadEntity()
    {
        $input = (string) $this->getResponse()->getBody(true);
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
     * Gets the vocab element represented by this state instance.
     *
     * @return \Gedcomx\Vocab\VocabElement
     */
    public function getVocabElement(){
        $vocabElement = new VocabElement();

        $idQuad = $this->rdfCollection->getPropertyQuad(VocabConstants::DC_NAMESPACE . "identifier");
        $vocabElement->setId(RdfNode::getValue($idQuad->getObject()));
        $vocabElement->setUri(RdfNode::getValue($this->rdfCollection->first()->getSubject()));

        $subclass = $this->rdfCollection->getPropertyQuad(VocabConstants::RDFS_NAMESPACE . "subClassOf");
        if ($subclass != null) {
            $vocabElement->setSubclass(RdfNode::getValue($subclass->getObject()));
        }

        $type = $this->rdfCollection->getPropertyQuad(VocabConstants::DC_NAMESPACE . "type");
        if ($type != null) {
            $vocabElement->setType(RdfNode::getValue($type->getObject()));
        }

        $labels = $this->rdfCollection->quadsMatchingProperty(VocabConstants::RDFS_NAMESPACE . "label");
        if ($labels->count()) {
            foreach ($labels as $label) {
                $node = $label->getObject();
                $vocabElement->addLabel(RdfNode::getValue($node), RdfNode::getLanguage($node));
            }
        }

        $comments = $this->rdfCollection->quadsMatchingProperty(VocabConstants::RDFS_NAMESPACE . "comment");
        if ($comments->count()) {
            foreach ($comments as $comment) {
                $node = $comment->getObject();
                $vocabElement->addDescription(RdfNode::getValue($node), RdfNode::getLanguage($node));
            }
        }

        return $vocabElement;
    }
}