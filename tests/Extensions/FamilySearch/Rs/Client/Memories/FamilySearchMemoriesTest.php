<?php

namespace Gedcomx\Tests\Extensions\Rs\Client\Memories;

use Gedcomx\Extensions\FamilySearch\Rs\Client\FamilySearchStateFactory;
use Gedcomx\Rs\Client\Util\DataSource;
use Gedcomx\Rs\Client\Util\ImageSource;
use Gedcomx\Rs\Client\Util\MultiPartManager;
use Gedcomx\Tests\ApiTestCase;
use Intervention\Image\ImageManagerStatic as Image;

class FamilySearchMemoriesTest extends ApiTestCase
{
    private $image;

    public function testUploadMultiplePhotoMemories()
    {
        $filename = $this->makeImage();
        $artifact = new DataSource();
        $artifact->setFile($filename);
        $artifact->setTitle($this->faker->sentence(4));

        $factory = new FamilySearchStateFactory();
        $memories = $factory->newMemoriesState();

    }

    /**
     * Clean up generated test images
     */
    public function tearDown()
    {
        foreach (glob('*.png') as $pic) {
            unlink($pic);
        }
    }
    private function makeImage()
    {
        $height = $width = 5;
        $scale = 100;
        $filename = 'test_'.bin2hex(openssl_random_pseudo_bytes(8)).".png";

        $img = Image::canvas($width, $height, '#000');
        for ($x = 0; $x < $width; $x++) {
            for ($y = 0; $y < $height; $y++) {
                $color = $this->randomColor();
                $img->pixel($color, $x, $y);
            }
        }
        $img->resize($width*$scale, $width*$scale);
        $png = $img->encode('png');
        $png->save($filename);

        return $filename;
    }

    private function randomColor()
    {
        return array(
            mt_rand(0, 255),
            mt_rand(0, 255),
            mt_rand(0, 255),
            mt_rand(0, 100) / 100
        );
    }
}