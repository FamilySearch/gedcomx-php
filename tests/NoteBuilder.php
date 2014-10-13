<?php


namespace Gedcomx\Tests;


use Faker\Factory;
use Gedcomx\Common\Attribution;
use Gedcomx\Common\Note;

class NoteBuilder {

    private static $faker;

    private static function faker(){
        if( self::$faker == null ){
            self::$faker = Factory::create();
        }

        return self::$faker;
    }

    /**
     * @return \Gedcomx\Common\Note
     */
    public static function createNote(){
        return new Note(
            array(
                "subject" => self::faker()->sentence(6),
                "text" => self::faker()->text(),
                "attribution" => new Attribution(
                    array(
                        "changeMessage" => self::faker()->sentence(10)
                    )
                )
            )
        );
    }
} 