<?php

namespace Gedcomx\GedcomxFile;

class GedcomxFileException extends \Exception
{
    public function __construct($error_code)
    {
        $errorMessage = "Unknown error.";
        switch($error_code){
            case \ZipArchive::ER_MEMORY:
                $errorMessage = "Error allocating memory.";
                break;

            case \ZipArchive::ER_NOZIP:
                $errorMessage = "File is not a valid archive.";
                break;

            case \ZipArchive::ER_OPEN:
                $errorMessage = "Unable to open file.";
                break;

            case \ZipArchive::ER_READ:
                $errorMessage = "Unable to read file.";
                break;

            case \ZipArchive::ER_SEEK:
                $errorMessage = "Seek error.";
                break;

            case \ZipArchive::ER_NOENT:
                $errorMessage = "File not found.";
                break;
        }

        parent::__construct($errorMessage, $error_code);
    }
}