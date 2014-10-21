<?php


namespace Gedcomx\Tests;


use Gedcomx\Source\SourceDescription;

class SourceBuilder extends TestBuilder{

    public static function buildSource(){
        return new SourceDescription(array(
            "citations" => array(
                array("value" => "ISBN: 9780681403222")
            ),
            "titles" => array(
                array("value" => "The More Than Complete Hitchhiker's Guide: Complete & Unabridged")
            ),
            "notes" => array(
                array("text" => "Beware Vogons")
            ),
            "attribution" => array(
                "contributor" => array(
                    "resource" =>"https://familysearch.org/platform/users/agents/MM6M-8QJ",
                    "resourceId" => "MM6M-8QJ"
                ),
                "changeMessage" => "Twas brillig and the slithey toves did gire and gimble in the wabe."
            )
        ));
    }
} 