<?php

namespace Gedcomx\Tests;

use Gedcomx\Conclusion\DateInfo;
use Gedcomx\Conclusion\Fact;
use Gedcomx\Conclusion\PlaceReference;
use Gedcomx\Extensions\FamilySearch\Types\FactType;

class FactBuilder extends TestBuilder {

    public static function birth(){
        $birthDate = self::dateBetween(200, 50);
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
        $rnd = self::faker()->numberBetween(5, 95);
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
        $date = self::dateBetween(125, 50);
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
        $date = self::dateBetween(125, 50);
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
        $date = self::dateBetween(18);
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
        $date = self::dateBetween(18);
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
        $date = self::dateBetween(40, 18);
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
    
    /**
     * Return a random date between the given range.
     */
    private static function dateBetween($startYear, $endYear = 0)
    {
        $startYear = 2010 - intval($startYear) . '-01-01';
        $endYear = 2010 - intval($endYear) . '-01-01';
        return self::faker()->dateTimeBetween($startYear, $endYear);
    }

}