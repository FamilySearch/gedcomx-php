<?php


namespace Gedcomx\Tests;

use Faker\Factory;

class TestBuilder
{
    protected static $faker;

    protected static function faker(){
        if( self::$faker == null ){
            self::$faker = Factory::create();
        }

        return self::$faker;
    }
}