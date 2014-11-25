<?php 

namespace Gedcomx\Tests\Functional;

use Gedcomx\Extensions\FamilySearch\Rs\Client\FamilySearchStateFactory;
use Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree\FamilyTreeStateFactory;
use Gedcomx\Rs\Client\Util\DataSource;
use Gedcomx\Rs\Client\Util\HttpStatus;
use Gedcomx\Rs\Client\Util\ImageSource;
use Gedcomx\Rs\Client\Util\MultiPartManager;
use Gedcomx\Tests\ApiTestCase;
use Gedcomx\Tests\PersonBuilder;
use Gedcomx\Tests\SourceBuilder;
use Intervention\Image\ImageManagerStatic as Image;
use mPDF;

class MemoriesTests extends ApiTestCase
{

    /**
     * Clean up generated test images
     */
    public function tearDown()
    {
        parent::tearDown();
        foreach (glob('*.jpg') as $file) {
            unlink($file);
        }
        foreach (glob('*.pdf') as $file) {
            unlink($file);
        }
        foreach (glob('*.txt') as $file) {
            unlink($file);
        }
    }

    /**
      * @link https://familysearch.org/developers/docs/api/tree/Create_Person_Memory_Reference_usecase
      */
    public function testCreatePersonMemoryReference()
    {
        $this->markTestIncomplete("Not yet implemented");
    }

     /**
      * @link https://familysearch.org/developers/docs/api/memories/Create_Memories_Comment_usecase
      */
    public function testCreateMemoriesComment()
    {
        $this->markTestIncomplete("Not yet implemented");
    }

    /**
     * @link https://familysearch.org/developers/docs/api/memories/Create_Memory_Persona_usecase
     */
    public function testCreateMemoryPersona()
    {
        $filename = $this->makeImage();
        $artifact = new DataSource();
        $artifact->setFile($filename);

        $description = SourceBuilder::newSource();

        $factory = new FamilyTreeStateFactory();
        $memories = $factory->newMemoriesState();
        $memories = $this->authorize($memories);

        /** @var \Gedcomx\Rs\Client\SourceDescriptionState $upload */
        $upload = $memories->addArtifact($artifact, $description)->get();

        $person = PersonBuilder::buildPerson('male');

        $persona = $upload->addPersonPersona($person);
        $this->assertEquals(
            HttpStatus::CREATED,
            $persona->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__, $persona)
        );

    }

     /**
      * @link https://familysearch.org/developers/docs/api/sources/Create_User-Uploaded_Source_usecase
      */
    public function testCreateUserUploadedSource()
    {
        $this->markTestIncomplete("Not yet implemented");
    }
     /**
      * @link https://familysearch.org/developers/docs/api/tree/Read_Person_Memory_References_usecase
      */
    public function testReadPersonMemoryReference()
    {
        $this->markTestIncomplete("Not yet implemented");
    }
     /**
      * @link https://familysearch.org/developers/docs/api/memories/Read_Memories_Comments_usecase
      */
    public function testReadMemoriesComments()
    {
        $this->markTestIncomplete("Not yet implemented");
    }
     /**
      * @link https://familysearch.org/developers/docs/api/memories/Read_Memories_for_a_User_usecase
      */
    public function testReadMemoriesForAUser()
    {
        $this->markTestIncomplete("Not yet implemented");
    }

    /**
     * @link https://familysearch.org/developers/docs/api/memories/Read_Memory_usecase
     */
    public function testReadMemory()
    {
        $filename = $this->makeTextFile();
        $artifact = new DataSource();
        $artifact->setFile($filename);

        $description = SourceBuilder::newSource();

        $factory = new FamilySearchStateFactory();
        $memories = $factory->newMemoriesState();
        $memories = $this->authorize($memories);

        $upload = $memories->addArtifact($artifact, $description);
        $upload = $upload->get();

        $this->assertEquals(
            HttpStatus::OK,
            $upload->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__, $upload)
        );

        $upload->delete();
    }

    /**
     * @link https://familysearch.org/developers/docs/api/memories/Read_Memory_Personas_usecase
     */
    public function testReadMemoryPersona()
    {
        $filename = $this->makeImage();
        $artifact = new DataSource();
        $artifact->setFile($filename);

        $description = SourceBuilder::newSource();

        $factory = new FamilyTreeStateFactory();
        $memories = $factory->newMemoriesState();
        $memories = $this->authorize($memories);

        /** @var \Gedcomx\Rs\Client\SourceDescriptionState $upload */
        $upload = $memories->addArtifact($artifact, $description)->get();

        $person = PersonBuilder::buildPerson('male');

        $persona = $upload->addPersonPersona($person);
        $this->assertEquals(
            HttpStatus::CREATED,
            $persona->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__, $persona)
        );

    }

     /**
      * @link https://familysearch.org/developers/docs/api/memories/Read_Memory_Personas_usecase
      */
    public function testReadMemoriesPersonas()
    {
        $this->markTestIncomplete("Not yet implemented");
    }
     /**
      * @link https://familysearch.org/developers/docs/api/memories/Update_Memories_Comment_usecase
      */
    public function testUpdateMemoriesComment()
    {
        $this->markTestIncomplete("Not yet implemented");
    }

    /**
     * @link https://familysearch.org/developers/docs/api/memories/Update_Memory_Description_usecase
     */
    public function testUpdateMemoryDescription()
    {
        $filename = $this->makeImage();
        $artifact = new DataSource();
        $artifact->setFile($filename);

        $description = SourceBuilder::newSource();

        $factory = new FamilySearchStateFactory();
        $memories = $factory->newMemoriesState();
        $memories = $this->authorize($memories);

        $upload = $memories->addArtifact($artifact, $description)->get();

        $gedcom = $upload->getEntity();
        $descriptions = $gedcom->getSourceDescriptions();
        foreach ($descriptions as $source) {
            foreach($source->getDescriptions() as $d) {
                $d->setValue($this->faker->sentence(3));
            }
        }

        $updated = $upload->update($gedcom);

        $this->assertEquals(
            HttpStatus::NO_CONTENT,
            $updated->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__, $updated)
        );
    }

     /**
      * @link https://familysearch.org/developers/docs/api/tree/Delete_Person_Memory_Reference_usecase
      */
    public function testDeletePersonMemoryReference()
    {
        $this->markTestIncomplete("Not yet implemented");
    }

    /**
     * @link https://familysearch.org/developers/docs/api/memories/Delete_Memory_usecase
     */
    public function testDeleteMemory()
    {
        $filename = $this->makeTextFile();
        $artifact = new DataSource();
        $artifact->setFile($filename);

        $description = SourceBuilder::newSource();

        $factory = new FamilySearchStateFactory();
        $memories = $factory->newMemoriesState();
        $memories = $this->authorize($memories);

        $upload = $memories->addArtifact($artifact, $description);
        $upload = $upload->delete();

        $this->assertEquals(
            HttpStatus::NO_CONTENT,
            $upload->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__, $upload)
        );
    }

    /**
      * @link https://familysearch.org/developers/docs/api/memories/Delete_Memories_Comment_usecase
      */
    public function testDeleteMemoriesComment()
    {
        $this->markTestIncomplete("Not yet implemented");
    }
     /**
      * @link https://familysearch.org/developers/docs/api/memories/Delete_Memory_Persona_usecase
      */
    public function testDeleteMemoryPersona()
    {
        $this->markTestIncomplete("Not yet implemented");
    }
     /**
      * @link https://familysearch.org/developers/docs/api/memories/Upload_Multiple_Photo_Memories_usecase
      */
    public function testUploadMultiplePhotoMemories()
    {
        $this->markTestSkipped("Not currently supported by the API.");
    }

    /**
     * @link https://familysearch.org/developers/docs/api/memories/Upload_PDF_Document_usecase
     *
     * SDK only supports uploading via multi-part form uploads.
     */
    public function testUploadPdf()
    {
        $filename = $this->makePDF();
        $artifact = new DataSource();
        $artifact->setFile($filename);

        $description = SourceBuilder::newSource();

        $factory = new FamilySearchStateFactory();
        $memories = $factory->newMemoriesState();
        $memories = $this->authorize($memories);

        $upload = $memories->addArtifact($artifact, $description);
        $this->assertEquals(
            HttpStatus::CREATED,
            $upload->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__, $upload)
        );

        $upload->delete();
    }

    /**
     * @link https://familysearch.org/developers/docs/api/memories/Upload_Photo_usecase
     *
     * Use shows uploading a photo with an image content type. SDK only supports uploading
     *       via multi-part form uploads.
     */
    public function testUploadPhoto()
    {
        $filename = $this->makeImage();
        $artifact = new DataSource();
        $artifact->setFile($filename);
        $artifact->setTitle($this->faker->sentence(4));

        $factory = new FamilySearchStateFactory();
        $memories = $factory->newMemoriesState();
        $memories = $this->authorize($memories);

        $upload = $memories->addArtifact($artifact);
        $this->assertEquals(
            HttpStatus::CREATED,
            $upload->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__, $upload)
        );

        $upload->delete();
    }

    /**
     * @link https://familysearch.org/developers/docs/api/memories/Upload_Photo_Via_Multipart_Form_Data_usecase
     *
     * SDK only supports uploading via multi-part form uploads.
     */
    public function testUploadPhotoViaMultipartFormData()
    {
        $filename = $this->makeImage();
        $artifact = new DataSource();
        $artifact->setFile($filename);

        $description = SourceBuilder::newSource();

        $factory = new FamilySearchStateFactory();
        $memories = $factory->newMemoriesState();
        $memories = $this->authorize($memories);

        $upload = $memories->addArtifact($artifact, $description);
        $upload->getEntity()->getDescriptions();

        $this->assertEquals(
            HttpStatus::CREATED,
            $upload->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__, $upload)
        );

        $upload->delete();
    }

    /**
     * @link https://familysearch.org/developers/docs/api/memories/Upload_Story_usecase
     *
     * SDK only supports uploading via multi-part form uploads.
     */
    public function testUploadStory()
    {
        $filename = $this->makeTextFile();
        $artifact = new DataSource();
        $artifact->setFile($filename);

        $description = SourceBuilder::newSource();

        $factory = new FamilySearchStateFactory();
        $memories = $factory->newMemoriesState();
        $memories = $this->authorize($memories);

        $upload = $memories->addArtifact($artifact, $description);
        $this->assertEquals(
            HttpStatus::CREATED,
            $upload->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__, $upload)
        );

        $upload->delete();
    }

     /**
      * @link https://familysearch.org/developers/docs/api/memories/Upload_Story_Memories_usecase
      */
    public function testUploadStoryMemories()
    {
        $this->markTestSkipped("Not currently supported by the API.");
    }

    /**
     * Generate randomized text files for testing
     *
     * @return string The generated filename
     */
    private function makeTextFile()
    {
        $filename = 'test_'.bin2hex(openssl_random_pseudo_bytes(8)).".txt";

        $text = $this->faker->sentence(4) . "\n" .
            "==========================\n\n" .
            $this->faker->paragraph() ."\n\n" .
            $this->faker->paragraph() ."\n\n" .
            $this->faker->paragraph() ."\n";
        $fileHandle = fopen($filename,'w');
        fwrite($fileHandle, $text);
        fclose($fileHandle);

        return $filename;
    }

    /**
     * Generate randomized PDF files for testing
     *
     * @return string The generated filename
     */
    private function makePDF()
    {
        $filename = 'test_'.bin2hex(openssl_random_pseudo_bytes(8)).".pdf";

        $pdf = new mPDF();
        $pdf->WriteHTML(
            '<h3>'.$this->faker->sentence(4).'</h3>' .
            '<p>'.$this->faker->paragraph().'</p>' .
            '<p>'.$this->faker->paragraph().'</p>' .
            '<p>'.$this->faker->paragraph().'</p>'
        );
        $pdf->Output($filename);
        return $filename;
    }

    /**
     * Generate randomized images for testing
     *
     * @return string The generated filename
     */
    private function makeImage()
    {
        $height = $width = 5;
        $scale = 100;
        $filename = 'test_'.bin2hex(openssl_random_pseudo_bytes(8)).".jpg";

        $img = Image::canvas($width, $height, '#000');
        for ($x = 0; $x < $width; $x++) {
            for ($y = 0; $y < $height; $y++) {
                $color = $this->randomColor();
                $img->pixel($color, $x, $y);
            }
        }
        $img->resize($width*$scale, $width*$scale);
        $png = $img->encode('jpg');
        $png->save($filename);

        return $filename;
    }

    /**
     * Generate random rgba color
     *
     * @return array
     */
    private function randomColor()
    {
        return array(
            mt_rand(0, 255),
            mt_rand(0, 255),
            mt_rand(0, 255),
            1
        );
    }

}