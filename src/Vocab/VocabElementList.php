<?php 

namespace Gedcomx\Vocab;

class VocabElementList 
{
    /**
     * @var string
     */
    private $title;
    /**
     * @var string
     */
    private $description;
    /**
     * @var string
     */
    private $uri;
    /**
     * @var string
     */
    private $id;
    /**
     * @var array
     */
    private $elements = array();

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * @param string $uri
     */
    public function setUri($uri)
    {
        $this->uri = $uri;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return \Gedcomx\Vocab\VocabElement[]
     */
    public function getElements()
    {
        return $this->elements;
    }

    /**
     * @param \Gedcomx\Vocab\VocabElement[]
     */
    public function setElements($elements)
    {
        $this->elements = $elements;
    }

    /**
     * @param \Gedcomx\Vocab\VocabElement $element
     */
    public function addElement($element)
    {
        $this->elements[] = $element;
    }
}