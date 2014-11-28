<?php

namespace Gedcomx\Tests;

use Intervention\Image\ImageManagerStatic as Image;
use mPDF;

class ArtifactBuilder extends TestBuilder
{
    /**
     * Generate randomized text files for testing
     * @return string The generated filename
     */
    public static function makeTextFile()
    {
        $filename = 'test_' . bin2hex(openssl_random_pseudo_bytes(8)) . ".txt";

        $text = self::faker()->sentence(4) . "\n" .
            "==========================\n\n" .
            self::faker()->paragraph() . "\n\n" .
            self::faker()->paragraph() . "\n\n" .
            self::faker()->paragraph() . "\n";
        $fileHandle = fopen($filename, 'w');
        fwrite($fileHandle, $text);
        fclose($fileHandle);

        return $filename;
    }

    /**
     * Generate randomized PDF files for testing
     * @return string The generated filename
     */
    public static function makePDF()
    {
        $filename = 'test_' . bin2hex(openssl_random_pseudo_bytes(8)) . ".pdf";

        $pdf = new mPDF();
        $pdf->WriteHTML(
            '<h3>' . self::faker()->sentence(4) . '</h3>' .
            '<p>' . self::faker()->paragraph() . '</p>' .
            '<p>' . self::faker()->paragraph() . '</p>' .
            '<p>' . self::faker()->paragraph() . '</p>'
        );
        $pdf->Output($filename);
        return $filename;
    }

    /**
     * Generate randomized images for testing
     * @return string The generated filename
     */
    public static function makeImage()
    {
        $height = $width = 5;
        $scale = 100;
        $filename = 'test_' . bin2hex(openssl_random_pseudo_bytes(8)) . ".jpg";

        $img = Image::canvas($width, $height, '#000');
        for ($x = 0; $x < $width; $x++) {
            for ($y = 0; $y < $height; $y++) {
                $color = self::randomColor();
                $img->pixel($color, $x, $y);
            }
        }
        $img->resize($width * $scale, $width * $scale);
        $png = $img->encode('jpg');
        $png->save($filename);

        return $filename;
    }

    /**
     * Generate random rgba color
     * @return array
     */
    private static function randomColor()
    {
        return array(
            mt_rand(0, 255),
            mt_rand(0, 255),
            mt_rand(0, 255),
            1
        );
    }
}