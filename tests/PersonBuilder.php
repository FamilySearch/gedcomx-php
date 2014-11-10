<?php

namespace Gedcomx\Tests;

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

class PersonBuilder extends TestBuilder
{
    public static function buildPerson($gender)
    {
        /*
         * Can't use faker for dates. It doesn't deal well with negative timestamps.
         */
        switch ($gender) {
            case 'male':
                $gender = GenderType::MALE;
                break;
            case 'female':
                $gender = GenderType::FEMALE;
                break;
            default:
                $gender = self::faker()->boolean() ? GenderType::FEMALE : GenderType::MALE;
        }


        $birth = FactBuilder::birth();
        $death = FactBuilder::death($birth);
        $facts = array();
        $facts[] = $birth;

        $living = false;
        if ($death->getDate()->getDateTime()->getTimestamp() > time()) {
            $living = true;
        } else {
            $facts[] = $death;
        }

        $person = new Person();
        $person->setGender(new Gender(array("type" => $gender)));
        $person->setLiving($living);
        $person->setPrincipal(false);

        $name = self::birthName($gender);
        $person->setNames(array($name));


        $person->setFacts($facts);

        $display = new DisplayProperties(array(
            "birthDate"  => $birth->getDate()->getDateTime()->format("d M Y"),
            "birthPlace" => $birth->getPlace()->getOriginal(),
            "gender"     => $gender,
            "lifespan"   => $birth->getDate()->getDateTime()->format("d M Y") . " - " . ($living ? '' : $death->getDate()->getDateTime()->format("d M Y")),
            "name"       => $name->toString()
        ));
        $person->setDisplayExtension($display);

        return $person;
    }

    public static function birthName($gender)
    {
        $firstName = self::faker()->firstName($gender);
        $lastName = self::faker()->lastName();
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

    public static function nickName($gender = 'female' )
    {
        $name = self::faker()->firstName($gender);
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
}