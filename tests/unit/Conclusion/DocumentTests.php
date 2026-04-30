<?php

namespace Gedcomx\Tests\Unit\Conclusion;

use Gedcomx\Tests\ApiTestCase;
use Gedcomx\Conclusion\Document;

class DocumentTests extends ApiTestCase
{
    public function testDocumentDeserialization()
    {
        $document = new Document($this->loadJson('document.json'));

        $this->assertEquals('D-1', $document->getId());
        $this->assertEquals('http://gedcomx.org/Abstract', $document->getType());
        $this->assertEquals('This is a transcribed document containing genealogical information.', $document->getText());
        $this->assertTrue($document->getExtracted());
    }

    public function testDocumentGettersAndSetters()
    {
        $document = new Document();
        $document->setId('D-2');
        $document->setType('http://gedcomx.org/Transcription');
        $document->setText('Transcribed text');
        $document->setExtracted(false);

        $this->assertEquals('D-2', $document->getId());
        $this->assertEquals('http://gedcomx.org/Transcription', $document->getType());
        $this->assertEquals('Transcribed text', $document->getText());
        $this->assertFalse($document->getExtracted());
    }

    public function testDocumentWithoutText()
    {
        $document = new Document();
        $document->setType('http://gedcomx.org/Analysis');

        $this->assertNull($document->getText());
    }
}
