<?php

namespace Gedcomx\Rs\Client\Util;

use Gedcomx\Conclusion\Person;
use Gedcomx\Gedcomx;

/**
 * A model representation of descendancy.
 *
 * Class DescendancyTree
 *
 * @package Gedcomx\Rs\Client\Util
 */
class DescendancyTree
{
    private $root = null;

    /**
     * Constructs a new descendancy tree using the specified model.
     *
     * @param \Gedcomx\Gedcomx $gx
     */
    function __construct(Gedcomx $gx)
    {
        $this->root = $this->buildTree($gx);
    }

    /**
     * Builds an array of persons to be placed in the descendancy tree.
     *
     * @param \Gedcomx\Gedcomx $gx
     *
     * @return \Gedcomx\Rs\Client\Util\DescendancyNode|null
     */
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

    /**
     * Gets the root person of the descendancy tree.
     *
     * @return \Gedcomx\Rs\Client\Util\DescendancyNode|null
     */
    function getRoot()
    {
        return $this->root;
    }

    /**
     * Parses the coordinates of the specified d'Aboville number. See remarks.
     * More information on a d'Aboville number can be found here: {@link http://en.wikipedia.org/wiki/Genealogical_numbering_system#d.27Aboville_System}.
     *
     * @param $number
     *
     * @return array
     */
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

/**
 * Represents a person, spouse, and descendancy in a tree.
 *
 * Class DescendancyNode
 *
 * @package Gedcomx\Rs\Client\Util
 */
class DescendancyNode
{
    public $Person;
    public $Spouse;
    public $Children;

    /**
     * Gets the main person of a tree.
     * @return mixed
     */
    public function getPerson()
    {
        return $this->Person;
    }

    /**
     * Sets the main person of a tree.
     *
     * @param \Gedcomx\Conclusion\Person $person
     */
    public function setPerson(Person $person)
    {
        $this->Person = $person;
    }

    /**
     * Gets the spouse of the main person.
     *
     * @return mixed
     */
    public function getSpouse()
    {
        return $this->Spouse;
    }

    /**
     * Sets the spouse of the main person.
     *
     * @param \Gedcomx\Conclusion\Person $spouse
     */
    public function setSpouse(Person $spouse)
    {
        $this->Spouse = $spouse;
    }

    /**
     * Gets the children of the main person
     *
     * @return mixed
     */
    public function getChildren()
    {
        return $this->Children;
    }

    /**
     * Sets the children of the main person
     *
     * @param array $children
     */
    public function setChildren(array $children)
    {
        $this->Children = $children;
    }
}
