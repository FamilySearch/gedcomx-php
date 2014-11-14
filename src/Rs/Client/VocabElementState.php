<?php 

namespace Gedcomx\Rs\Client;

use Gedcomx\Vocab\VocabElement;
use Guzzle\Http\Message\Request;
use Guzzle\Http\Message\Response;
use ML\JsonLD\JsonLD;

class VocabElementState extends GedcomxApplicationState
{
    /**
     * @var \Gedcomx\Rs\Client\Util\RdfCollection
     */
    private $rdfCollection;

    /**
     * @param \Guzzle\Http\Message\Request  $request
     * @param \Guzzle\Http\Message\Response $response
     *
     * @return \Gedcomx\Rs\Client\VocabElementState
     */
    protected function reconstruct(Request $request, Response $response)
    {
        return new VocabElementState($this->client, $request, $response, $this->accessToken, $this->stateFactory);
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

    public function getVocabElement(){
        $vocabElement = new VocabElement();

        $idQuad = $this->rdfCollection->getPropertyQuad(VocabConstants::DC_NAMESPACE + "identifier");
        $vocabElement->setId($idQuad->getObject()->getValue());
        $vocabElement->setId((string)$this->rdfCollection->first()->getSubject());

        $subclass = $this->rdfCollection->getPropertyQuad(VocabConstants::RDFS_NAMESPACE . "subClassOf");
        if ($subclass != null) {
            $vocabElement->setSubclass($subclass->getObject()->getValue());
        }

        $type = $this->rdfCollection->getPropertyQuad(VocabConstants::DC_NAMESPACE . "type");
        if ($type != null) {
            $vocabElement->setType($type->getObject()->getValue());
        }

        $labels = $this->rdfCollection->quadsMatchingProperty(VocabConstants::RDFS_NAMESPACE . "label");
        if ($labels->count()) {
            foreach ($labels as $label) {
                $node = $label->getObject();
                $vocabElement->addLabel($node->getValue(), strtolower($node->getLanguage()));
            }
        }

        $comments = $this->rdfCollection->quadsMatchingProperty(VocabConstants::RDFS_NAMESPACE . "comment");
        if ($comments->count()) {
            foreach ($comments as $comment) {
                $node = $comment->getObject();
                $vocabElement->addDescription($node->getValue(), strtolower($node->getLanguage()));
            }
        }

        return $vocabElement;
    }
}