<?php

namespace Gedcomx\Tests;

use Gedcomx\Conclusion\DateInfo;
use Gedcomx\Conclusion\DisplayProperties;
use Gedcomx\Conclusion\Fact;
use Gedcomx\Conclusion\Gender;
use Gedcomx\Conclusion\Person;
use Gedcomx\Conclusion\PlaceReference;
use Gedcomx\Types\FactType;
use Gedcomx\Types\GenderType;

class XMLBuilder extends TestBuilder
{
    public static function XMLRelationshipData()
    {
        //  MOTHER
        $birthDate = new \DateTime('January 21, 1923');
        $birth = new Fact(
            array(
                "type"  => FactType::BIRTH,
                "date"  => new DateInfo(array(
                    "original" => $birthDate->format("F d, Y")
                )),
                "place" => new PlaceReference(array(
                    "description" => "possibly, maybe, don't know",
                    "original"    => 'Flagstaff, Arizona, United States'
                ))
            ));

        $deathDate = new \DateTime('October 12, 1987');
        $death = new Fact(
            array(
                "type"  => FactType::DEATH,
                "date"  => new DateInfo(array(
                    "original" => $deathDate->format("F d, Y")
                )),
                "place" => new PlaceReference(array(
                    "description" => "One. Two. Buckle my shoe.",
                    "original"    => 'San Antonio, Texas, United States'
                ))
            ));
        $facts = array();
        $facts[] = $birth;
        $facts[] = $death;

        $mother = new Person();
        $mother->setGender(new Gender(array("type" => GenderType::FEMALE)));
        $mother->setLiving(false);
        $mother->setPrincipal(false);

        $name = PersonBuilder::makeName('Hildegard','Schmidlab');
        $mother->setNames(array($name));


        $mother->setFacts($facts);

        $display = new DisplayProperties(array(
            "birthDate"  => $birth->getDate()->getDateTime()->format("d M Y"),
            "birthPlace" => $birth->getPlace()->getOriginal(),
            "gender"     => GenderType::FEMALE,
            "lifespan"   => $birth->getDate()->getDateTime()->format("d M Y") . " - " . $death->getDate()->getDateTime()->format("d M Y"),
            "name"       => $name->toString()
        ));
        $mother->setDisplayExtension($display);

        //  FATHER
        $birthDate = new \DateTime('February 2, 1915');
        $birth = new Fact(
            array(
                "type"  => FactType::BIRTH,
                "date"  => new DateInfo(array(
                    "original" => $birthDate->format("F d, Y")
                )),
                "place" => new PlaceReference(array(
                    "description" => "possibly, maybe, don't know",
                    "original"    => 'Flagstaff, Arizona, United States'
                ))
            ));

        $deathDate = new \DateTime('January 12, 1977');
        $death = new Fact(
            array(
                "type"  => FactType::DEATH,
                "date"  => new DateInfo(array(
                    "original" => $deathDate->format("F d, Y")
                )),
                "place" => new PlaceReference(array(
                    "description" => "One. Two. Buckle my shoe.",
                    "original"    => 'San Antonio, Texas, United States'
                ))
            ));
        $facts = array();
        $facts[] = $birth;
        $facts[] = $death;

        $father = new Person();
        $father->setGender(new Gender(array("type" => GenderType::MALE)));
        $father->setLiving(false);
        $father->setPrincipal(false);

        $name = PersonBuilder::makeName('Marvin','Schmidlab');
        $father->setNames(array($name));
        $father->setFacts($facts);

        $display = new DisplayProperties(array(
            "birthDate"  => $birth->getDate()->getDateTime()->format("d M Y"),
            "birthPlace" => $birth->getPlace()->getOriginal(),
            "gender"     => GenderType::MALE,
            "lifespan"   => $birth->getDate()->getDateTime()->format("d M Y") . " - " . $death->getDate()->getDateTime()->format("d M Y"),
            "name"       => $name->toString()
        ));
        $father->setDisplayExtension($display);

        //  CHILD
        $birthDate = new \DateTime('March 12, 1935');
        $birth = new Fact(
            array(
                "type"  => FactType::BIRTH,
                "date"  => new DateInfo(array(
                    "original" => $birthDate->format("F d, Y")
                )),
                "place" => new PlaceReference(array(
                    "description" => "possibly, maybe, don't know",
                    "original"    => 'Santa Fe, New Mexico, United States'
                ))
            ));

        $deathDate = new \DateTime('January 2, 2007');
        $death = new Fact(
            array(
                "type"  => FactType::DEATH,
                "date"  => new DateInfo(array(
                    "original" => $deathDate->format("F d, Y")
                )),
                "place" => new PlaceReference(array(
                    "description" => "One. Two. Buckle my shoe.",
                    "original"    => 'San Antonio, Texas, United States'
                ))
            ));
        $facts = array();
        $facts[] = $birth;
        $facts[] = $death;

        $child = new Person();
        $child->setGender(new Gender(array("type" => GenderType::MALE)));
        $child->setLiving(false);
        $child->setPrincipal(false);

        $name = PersonBuilder::makeName('Englebert','Schmidlab');
        $child->setNames(array($name));
        $child->setFacts($facts);

        $display = new DisplayProperties(array(
            "birthDate"  => $birth->getDate()->getDateTime()->format("d M Y"),
            "birthPlace" => $birth->getPlace()->getOriginal(),
            "gender"     => GenderType::MALE,
            "lifespan"   => $birth->getDate()->getDateTime()->format("d M Y") . " - " . $death->getDate()->getDateTime()->format("d M Y"),
            "name"       => $name->toString()
        ));
        $child->setDisplayExtension($display);

        return array(
            'father' => $father,
            'mother' => $mother,
            'child' => $child
        );
    }
}