<?php

namespace Gedcomx\Tests;

use Gedcomx\Conclusion\DateInfo;
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

class PersonBuilder {

    public static function createPerson()
    {
        $person = new Person();
        $person->setGender(new Gender(array("type" => GenderType::MALE)));
        $person->setLiving(false);
        $person->setPrincipal(false);

        $name = new Name( array(
            "type" => NameType::BIRTHNAME,
            "preferred" => true,
            "nameForms" => array(
                new NameForm( array(
                    "lang" => 'en',
                    "fullText" => 'Bob Friendly',
                    "parts" => array(
                        new NamePart( array(
                            "type" => NamePartType::GIVEN,
                            "value" => "Bob"
                        )),
                        new NamePart( array(
                            "type" => NamePartType::SURNAME,
                            "value" => "Friendly"
                        ))
                    )
                ))
            )
        ));
        $facts = array(
            new Fact(array(
                "type" => FactType::BIRTH,
                "date" => new DateInfo(array(
                        "original" => "January 27, 1949"
                    )),
                "place" => new PlaceReference(array(
                        "description" => "possibly, maybe, don't know",
                        "original" => "Lakes District Hospital, Queenstown, Otago, New Zealand"
                    ))
            )),
            new Fact(array(
                "type" => FactType::DEATH,
                "date" => new DateInfo(array(
                        "original" => "June 13, 1970"
                    )),
                "place" => new PlaceReference(array(
                        "description" => "possibly, maybe, don't know",
                        "original" => "Da Nang, Vientnam"
                    ))
            ))
        );

        $person->setNames(array($name));
        $person->setFacts($facts);

        return $person;
    }

} 