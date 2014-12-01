<?php

namespace Gedcomx\Tests;

use Gedcomx\Conclusion\DateInfo;
use Gedcomx\Conclusion\Fact;
use Gedcomx\Conclusion\PlaceReference;
use Gedcomx\Extensions\FamilySearch\Types\FactType;

class FactBuilder extends TestBuilder {

    public static function birth(){
        $rnd = rand(50,200);
        $birthDate = new \DateTime("-{$rnd} years");
        $birthPlace = self::faker()->city() . ", " . self::faker()->state() . ", United States";

        return new Fact(
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
    }

    public static function death(Fact $birth){
        $rnd = rand(5,95);
        $deathDate = new \DateTime($birth->getDate()->getDateTime()->format("F d, Y") . "+{$rnd}years");

        return new Fact(
            array(
                "type"  => FactType::DEATH,
                "date"  => new DateInfo(array(
                    "original" => $deathDate->format("F d, Y")
                )),
                "place" => new PlaceReference(array(
                    "description" => "possibly, maybe, don't know",
                    "original"    => self::faker()->city() . ", " . self::faker()->state() . ", United States"
                ))
            )
        );
    }


    public static function militaryService()
    {
        $rnd = rand(50,125);

        $date = new \DateTime("-{$rnd} years");
        return new Fact(array(
            'primary' => true,
            'type' => FactType::MILITARYSERVICE,
            'date' => new DateInfo(array(
                "original" => $date->format("F d, Y")
            )),
            'value' => self::faker()->sentence(6)
        ));
    }

    public static function eagleScout()
    {
        $rnd = rand(50,125);

        $date = new \DateTime("-{$rnd} years");
        return new Fact(array(
            'primary' => true,
            'type' => "data:,Eagle%20Scout",
            'date' => new DateInfo(array(
                "original" => $date->format("F d, Y")
            )),
            'value' => self::faker()->sentence(6)
        ));
    }

    /**
     * @param \DateTime $birthdate
     *
     * @return Fact
     */
    public static function adoption(\DateTime $birthdate)
    {
        $rnd = rand(0,18);
        $date = new \DateTime($birthdate->format('Y-m-d') . " +{$rnd} years");

        return new Fact(array(
            'primary' => true,
            'type' => FactType::ADOPTION,
            'date' => new DateInfo(array(
                "original" => $date->format("F d, Y")
            )),
            'value' => self::faker()->sentence(6)
        ));
    }

    public static function adoptiveParent()
    {
        $date = new \DateTime();

        return new Fact(array(
            'primary' => true,
            'type' => FactType::ADOPTIVEPARENT,
            'date' => new DateInfo(array(
                "original" => $date->format("F d, Y")
            )),
            'value' => self::faker()->sentence(6)
        ));
    }

    /**
     * @param \DateTime $birthdate
     *
     * @return Fact
     */
    public static function marriage(\DateTime $birthdate)
    {
        $rnd = rand(18,40);
        $date = new \DateTime($birthdate->format('Y-m-d') . " +{$rnd} years");

        return new Fact(array(
            'primary' => true,
            'type' => FactType::MARRIAGE,
            'date' => new DateInfo(array(
                    "original" => $date->format("F d, Y")
                ))
        ));
    }

    public static function lifeSketch(){
        return new Fact(array(
            'primary' => true,
            'type' => FactType::LIFE_SKETCH,
            'value' => self::faker()->paragraph(2)
        ));
    }

}