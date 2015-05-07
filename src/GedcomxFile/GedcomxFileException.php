<?php

namespace Gedcomx\GedcomxFile;

/**
 * Represents an exception that could occur while working with a GEDCOM X file.
 *
 * Class GedcomxFileException
 *
 * @package Gedcomx\GedcomxFile
 */
class GedcomxFileException extends \Exception
{
    /**
     * Return a meaningful message if ZipArchive returns an error number
     *
     * @param string $filepath
     * @param int    $error_code
     */
    public function __construct($filepath, $error_code = 0)
    {
        $errorMessage = $filepath . ": ";
        switch($error_code){
            case \ZipArchive::ER_MEMORY:
                $errorMessage .= "Error allocating memory.";
                break;

            case \ZipArchive::ER_NOZIP:
                $errorMessage .= "File is not a valid archive.";
                break;

            case \ZipArchive::ER_OPEN:
                $errorMessage .= "Unable to open file.";
                break;

            case \ZipArchive::ER_READ:
                $errorMessage .= "Unable to read file.";
                break;

            case \ZipArchive::ER_SEEK:
                $errorMessage .= "Seek error.";
                break;

            case \ZipArchive::ER_NOENT:
                $errorMessage .= "File not found.";
                break;

            default:
                $errorMessage .= "Uknown error.";
        }

        parent::__construct($errorMessage, $error_code);
    }
}
