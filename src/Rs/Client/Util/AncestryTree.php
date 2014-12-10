<?php

namespace Gedcomx\Rs\Client\Util;

use Gedcomx\Gedcomx;

/**
 * A model representation of ancestry.
 *
 * Class AncestryTree
 *
 * @package Gedcomx\Rs\Client\Util
 */
class AncestryTree{

    private $ancestry = array();

    /**
     * Constructs a new ancestry tree using the specified model.
     *
     * @param \Gedcomx\Gedcomx $gx
     */
    function __construct(Gedcomx $gx){
        $this->buildArray($gx);
    }

    /**
     * Builds an array of persons to be placed in the ancestry tree.
     *
     * @param \Gedcomx\Gedcomx $gx
     *
     * @return array
     */
    protected function buildArray(Gedcomx $gx){
        $this->ancestry = array();
        if ($gx->getPersons() != null) {
            foreach ($gx->getPersons() as $person) {
                $display = $person->getDisplayExtension();
                if ($display && $display->getAscendancyNumber()) {
                    try {
                        $number = (int)$display->getAscendancyNumber();
                        while (count($this->ancestry) < $number) {
                            array_push($this->ancestry, null);
                        }
                        $this->ancestry[$number - 1] = $person;
                    } catch (NumberFormatException $e) {
                        //fall through...
                    }
                }
            }
        }
        return $this->ancestry;
    }

    /**
     * Gets the root person of the ancestry tree.
     *
     * @return \Gedcomx\Rs\Client\Util\AncestryNode|null
     */
    public function getRoot(){
        return $this->getAncestor(1);
    }

    /**
     * Gets an ancestor from the ancestry tree at the specified index.
     *
     * @param $number
     *
     * @return \Gedcomx\Rs\Client\Util\AncestryNode|null
     */
    public function getAncestor($number){
        if (count($this->ancestry) < $number) {
            return null;
        }
        return new AncestryNode($this, $this->ancestry, $number);
    }
}

/**
 * Represents an ancestor within an ancestry tree.
 *
 * Class AncestryNode
 *
 * @package Gedcomx\Rs\Client\Util
 */
class AncestryNode{

    private $tree = null;
    private $ancestry = null;
    private $number = null;

    /**
     * Constructs a new ancestry node using the specified values.
     *
     * @param $tree
     * @param $ancestry
     * @param $number
     */
    function __construct($tree, $ancestry, $number){
        $this->tree = $tree;
        $this->ancestry = $ancestry;
        $this->number = $number;
    }

    /**
     * Gets the current person this node represents.
     *
     * @return mixed
     */
    public function getPerson(){
        return $this->ancestry[$this->number - 1];
    }

    /**
     * Gets the father of the person represented by this node.
     *
     * @return mixed
     */
    public function getFather(){
        return $this->tree->getAncestor($this->number * 2);
    }

    /**
     * Gets the mother of the person represented by this node.
     *
     * @return mixed
     */
    public function getMother(){
        return $this->tree->getAncestor(($this->number * 2) + 1);
    }
}
