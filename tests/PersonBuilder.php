<?php

namespace Gedcomx\Tests;

use Faker\Generator;
use Gedcomx\Conclusion\DateInfo;
use Gedcomx\Conclusion\DisplayProperties;
use Gedcomx\Conclusion\Fact;
use Gedcomx\Conclusion\Gender;
use Gedcomx\Conclusion\Name;
use Gedcomx\Conclusion\NameForm;
use Gedcomx\Conclusion\NamePart;
use Gedcomx\Conclusion\Person;
use Gedcomx\Conclusion\PlaceReference;
use Gedcomx\Types\FactType;
use Gedcomx\Types\GenderType;
use Gedcomx\Types\NamePartType;
use Gedcomx\Types\NameType;

class PersonBuilder
{

    public static function buildPerson( Generator $faker )
    {
        /*
         * Can't use faker for dates. It doesn't deal well with negative timestamps.
         */
        $gender = $faker->boolean() ? GenderType::FEMALE : GenderType::MALE;
        $rnd = rand(50,200);
        $birthDate = new \DateTime("-{$rnd} years");
        $birthPlace = $faker->city() . ", " . $faker->state() . ", United States";
        $rnd = rand(5,95);
        $deathDate = new \DateTime($birthDate->format("F d, Y") . "+{$rnd}years");
        $living = false;
        if ($deathDate->getTimestamp() > time()) {
            $living = true;
        }

        $person = new Person();
        $person->setGender(new Gender(array("type" => $gender)));
        $person->setLiving($living);
        $person->setPrincipal(false);

        $name = self::birthName($faker, $gender);
        $person->setNames(array($name));

        $facts = array();
        $birth = new Fact(
            array(
                "type"  => FactType::BIRTH,
                "date"  => new DateInfo(array(
                        "original" => $birthDate->format("F d, Y")
                    )),
                "place" => new PlaceReference(array(
                        "description" => "possibly, maybe, don't know",
                        "original"    => $birthPlace
                    ))
            ));
        $facts[] = $birth;

        if (!$living) {
            $death = new Fact(
                array(
                    "type"  => FactType::DEATH,
                    "date"  => new DateInfo(array(
                            "original" => $deathDate->format("F d, Y")
                        )),
                    "place" => new PlaceReference(array(
                            "description" => "possibly, maybe, don't know",
                            "original"    => $faker->city() . ", " . $faker->state() . ", United States"
                        ))
                ));
            $facts[] = $death;
        }

        $person->setFacts($facts);

        $display = new DisplayProperties(array(
            "birthDate"  => $birthDate->format("d M Y"),
            "birthPlace" => $birthPlace,
            "gender"     => $gender,
            "lifespan"   => $birthDate->format("d M Y") . " - " . ($living ? '' : $deathDate->format("d M Y")),
            "name"       => $name->toString()
        ));
        $person->setDisplayExtension($display);

        return $person;
    }

    public static function birthName(Generator $faker, $gender )
    {
        $firstName = $faker->firstName($gender);
        $lastName = $faker->lastName();
        return new Name(array(
            "type"      => NameType::BIRTHNAME,
            "preferred" => true,
            "nameForms" => array(
                new NameForm(array(
                    "lang"     => 'en',
                    "fullText" => $firstName . ' ' . $lastName,
                    "parts"    => array(
                        new NamePart(array(
                            "type"  => NamePartType::GIVEN,
                            "value" => $firstName
                        )),
                        new NamePart(array(
                            "type"  => NamePartType::SURNAME,
                            "value" => $lastName
                        ))
                    )
                ))
            )
        ));

    }

    public static function nickName(Faker $faker, $gender = 'female' )
    {
        $name = $faker->firstName($gender);
        return new Name(array(
            "type"      => NameType::ALSOKNOWNAS,
            "nameForms" => array(
                new NameForm(array(
                    "lang"     => 'en',
                    "fullText" => $name,
                    "parts"    => array(
                        new NamePart(array(
                            "type"  => NamePartType::GIVEN,
                            "value" => $name
                        ))
                    )
                ))
            )
        ));
    }

    public static function militaryService()
    {
        return new Fact(array(
            'primary' => true,
            'type' => FactType::MILITARYSERVICE,
            'date' => new DateInfo(array(
                    "original" => "March 12, 1968"
                )),
            'value' => 'Corporal'
        ));
    }
}