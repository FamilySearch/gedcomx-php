<?php

namespace Gedcomx\Rs\Client\Util;

use Gedcomx\Conclusion\Person;
use Gedcomx\Gedcomx;

class DescendancyTree
{
    private $root = null;

    function __construct(Gedcomx $gx)
    {
        $this->root = $this->buildTree($gx);
    }

    protected function buildTree(Gedcomx $gx)
    {
        $root = null;
        if ($gx->getPersons() != null && count($gx->getPersons()) > 0) {
            $rootArray = array();
            foreach ($gx->getPersons() as $person) {
                if ($person->getDisplayExtension() != null && $person->getDisplayExtension()->getDescendancyNumber() != null) {
                    $number = $person->getDisplayExtension()->getDescendancyNumber();
                    $spouse = substr($number, -2) === "-S" || substr($number, -2) === "-s";
                    if ($spouse) {
                        $number = substr($number, 0, strlen($number) - 2);
                    }
                    $coordinates = $this->parseCoordinates($number);
                    $current =& $rootArray;
                    $i = 0;
                    $node = null;
                    while ($current !== null) {
                        $coordinate = $coordinates[$i];
                        while (count($current) < $coordinate) {
                            array_push($current, null);
                        }

                        $node = $current[$coordinate - 1];
                        if ($node == null) {
                            $node = new DescendancyNode();
                            $current[$coordinate - 1] = $node;
                        }

                        unset($current);
                        if (++$i < count($coordinates)) {
                            //if we still have another generation to descend, make sure the list is initialized.
                            $children =& $node->Children;
                            if ($children == null) {
                                $children = array();
                                $node->Children = $children;
                            }
                            $current =& $children;
                        } else {
                            $current = null;
                        }
                    }

                    if ($spouse) {
                        $node->Spouse = $person;
                    } else {
                        $node->Person = $person;
                    }
                }
            }

            if (count($rootArray) > 0) {
                $root = $rootArray[0];
            }
        }
        return $root;
    }

    function getRoot()
    {
        return $this->root;
    }

    protected function parseCoordinates($number)
    {
        $coords = array();
        $current = "";
        for ($i = 0; $i < strlen($number); $i++) {
            $ch = $number[$i];
            if ($ch == '.') {
                array_push($coords, $current);
                $current = "";
            } else {
                $current = $current . $ch;
            }
        }
        array_push($coords, $current);

        $coordinates = array();
        for ($i = 0; $i < count($coords); $i++) {
            $num = $coords[$i];
            $coordinates[$i] = (int)$num;
        }
        return $coordinates;
    }
}

class DescendancyNode
{
    public $Person;
    public $Spouse;
    public $Children;

    public function getPerson()
    {
        return $this->Person;
    }

    public function setPerson(Person $person)
    {
        $this->Person = $person;
    }

    public function getSpouse()
    {
        return $this->Spouse;
    }

    public function setSpouse(Person $spouse)
    {
        $this->Spouse = $spouse;
    }

    public function getChildren()
    {
        return $this->Children;
    }

    public function setChildren(array $children)
    {
        $this->Children = $children;
    }
}
