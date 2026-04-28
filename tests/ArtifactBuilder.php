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
     * Generate test images by copying a fixture
     * @return string The generated filename
     */
    public static function makeImage()
    {
        $filename = self::$tempDir . 'test_' . bin2hex(openssl_random_pseudo_bytes(8)) . ".jpg";
        $fixtureImage = __DIR__ . '/files/test-image.jpg';

        if (!file_exists($fixtureImage)) {
            throw new \RuntimeException('Test image fixture not found: ' . $fixtureImage);
        }

        copy($fixtureImage, $filename);

        return $filename;
    }
}