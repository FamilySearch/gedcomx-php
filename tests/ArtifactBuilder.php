<?php

namespace Gedcomx\Tests;

class ArtifactBuilder extends TestBuilder
{
    private static $tempDir;

    public static function setTempDir($temp){
        self::$tempDir = $temp;
    }

    /**
     * Generate randomized text files for testing
     * @return string The generated filename
     */
    public static function makeTextFile()
    {
        $filename = self::$tempDir . 'test_' . bin2hex(openssl_random_pseudo_bytes(8)) . ".txt";

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
     * Generate randomized images for testing using GD
     * @return string The generated filename
     */
    public static function makeImage()
    {
        $height = $width = 5;
        $scale = 100;
        $filename = self::$tempDir . 'test_' . bin2hex(openssl_random_pseudo_bytes(8)) . ".jpg";

        // Create image using GD
        $img = imagecreatetruecolor($width, $height);

        // Fill with random pixels
        for ($x = 0; $x < $width; $x++) {
            for ($y = 0; $y < $height; $y++) {
                $color = self::randomColor();
                $gdColor = imagecolorallocate($img, $color[0], $color[1], $color[2]);
                imagesetpixel($img, $x, $y, $gdColor);
            }
        }

        // Scale up the image
        $scaledImg = imagescale($img, $width * $scale, $height * $scale, IMG_NEAREST_NEIGHBOUR);

        // Save as JPEG
        imagejpeg($scaledImg, $filename, 85);

        // No need to destroy in PHP 8.0+ (automatic cleanup)

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