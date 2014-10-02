<?php


namespace Gedcomx\Tests;


use Gedcomx\Source\SourceDescription;

class SourceBuilder {

    public static function buildSource(){
        return new SourceDescription(array(
            "citation" => array(
                "value" => "ISBN: 9780681403222"
            ),
            "about" => "https://familysearch.org/pal:/MM9.1.1/M9PJ-2JJ",
            "title" => "The More Than Complete Hitchhiker's Guide: Complete & Unabridged",
            "note" => array(
                "text" => "Beware Vogons"
            ),
            "attribution" => array(
                "contributor" => array(
                    "resource" =>"https://familysearch.org/platform/users/agents/ABC123",
                    "resourceId" => "ABC123"
                ),
                "changeMessage" => "Twas brillig and the slithey toves did gire and gimble in the wabe."
            )
        ));
    }
} 