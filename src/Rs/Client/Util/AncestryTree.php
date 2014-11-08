<?php

namespace Gedcomx\Rs\Client\Util;

use Gedcomx\Gedcomx;

class AncestryTree{

    private $ancestry = array();

    function __construct(Gedcomx $gx){
        $this->buildArray($gx);
    }

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

    public function getRoot(){
        return $this->getAncestor(1);
    }

    public function getAncestor($number){
        if (count($this->ancestry) < $number) {
            return null;
        }
        return new AncestryNode($this, $this->ancestry, $number);
    }
}

class AncestryNode{

    private $tree = null;
    private $ancestry = null;
    private $number = null;

    function __construct($tree, $ancestry, $number){
        $this->tree = $tree;
        $this->ancestry = $ancestry;
        $this->number = $number;
    }

    public function getPerson(){
        return $this->ancestry[$this->number - 1];
    }

    public function getFather(){
        return $this->tree->getAncestor($this->number * 2);
    }

    public function getMother(){
        return $this->tree->getAncestor(($this->number * 2) + 1);
    }
}
