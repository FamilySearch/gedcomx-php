<?php

namespace Gedcomx\Tests\Unit;

use Gedcomx\Common\TextValue;
use Gedcomx\Extensions\FamilySearch\Platform\Vocab\VocabConcept;
use Gedcomx\Extensions\FamilySearch\Platform\Vocab\VocabConceptAttribute;
use Gedcomx\Extensions\FamilySearch\Platform\Vocab\VocabConcepts;
use Gedcomx\Extensions\FamilySearch\Platform\Vocab\VocabTerm;
use Gedcomx\Extensions\FamilySearch\Platform\Vocab\VocabTranslation;
use Gedcomx\Tests\ApiTestCase;

class VocabTest extends ApiTestCase
{
    public function testVocabConceptAttributeConstruction()
    {
        $attr = new VocabConceptAttribute();
        $attr->setId('attr-1');
        $attr->setName('category');
        $attr->setValue('person');

        $this->assertEquals('attr-1', $attr->getId());
        $this->assertEquals('category', $attr->getName());
        $this->assertEquals('person', $attr->getValue());
    }

    public function testVocabConceptAttributeJsonRoundTrip()
    {
        $attr = new VocabConceptAttribute([
            'id' => 'attr-123',
            'name' => 'type',
            'value' => 'relationship'
        ]);

        $json = $attr->toJson();
        $this->assertStringContainsString('relationship', $json);

        $decoded = json_decode($json, true);
        $attr2 = new VocabConceptAttribute($decoded);

        $this->assertEquals('attr-123', $attr2->getId());
        $this->assertEquals('type', $attr2->getName());
        $this->assertEquals('relationship', $attr2->getValue());
    }

    public function testVocabTranslationConstruction()
    {
        $translation = new VocabTranslation();
        $translation->setLang('en');
        $translation->setText('Birth');

        $this->assertEquals('en', $translation->getLang());
        $this->assertEquals('Birth', $translation->getText());
    }

    public function testVocabTranslationConstructorWithParameters()
    {
        $translation = new VocabTranslation('Birth', 'en');

        $this->assertEquals('en', $translation->getLang());
        $this->assertEquals('Birth', $translation->getText());
    }

    public function testVocabTranslationJsonRoundTrip()
    {
        $translation = new VocabTranslation('Marriage', 'en');

        $json = $translation->toJson();
        $decoded = json_decode($json, true);
        $translation2 = new VocabTranslation($decoded);

        $this->assertEquals('en', $translation2->getLang());
        $this->assertEquals('Marriage', $translation2->getText());
    }

    public function testVocabTermConstruction()
    {
        $term = new VocabTerm();
        $term->setTypeUri('http://gedcomx.org/Type');
        $term->setVocabConceptUri('http://gedcomx.org/Concept');
        $term->setSublistUri('http://gedcomx.org/Sublist');
        $term->setSublistPosition(5);

        $value1 = new TextValue();
        $value1->setLang('en');
        $value1->setValue('Birth');

        $value2 = new TextValue();
        $value2->setLang('es');
        $value2->setValue('Nacimiento');

        $term->setValues([$value1, $value2]);

        $this->assertEquals('http://gedcomx.org/Type', $term->getTypeUri());
        $this->assertEquals('http://gedcomx.org/Concept', $term->getVocabConcept());
        $this->assertEquals('http://gedcomx.org/Sublist', $term->getSublistUri());
        $this->assertEquals(5, $term->getSublistPosition());
        $this->assertCount(2, $term->getValues());
    }

    public function testVocabTermJsonRoundTrip()
    {
        $term = new VocabTerm([
            'typeUri' => 'http://gedcomx.org/Term',
            'conceptUri' => 'http://gedcomx.org/Birth',
            'sublistPosition' => 1
        ]);

        $json = $term->toJson();
        $decoded = json_decode($json, true);
        $term2 = new VocabTerm($decoded);

        $this->assertEquals('http://gedcomx.org/Term', $term2->getTypeUri());
        $this->assertEquals('http://gedcomx.org/Birth', $term2->getVocabConcept());
        $this->assertEquals(1, $term2->getSublistPosition());
    }

    public function testVocabConceptConstruction()
    {
        $concept = new VocabConcept();
        $concept->setDescription('A birth event');
        $concept->setNote('Additional notes');
        $concept->setGedcomxUri('http://gedcomx.org/Birth');

        $term = new VocabTerm();
        $term->setTypeUri('http://gedcomx.org/Label');
        $concept->setVocabTerms([$term]);

        $attr = new VocabConceptAttribute();
        $attr->setName('category');
        $attr->setValue('event');
        $concept->setAttributes([$attr]);

        $def = new TextValue();
        $def->setLang('en');
        $def->setValue('The event of birth');
        $concept->setDefinitions([$def]);

        $this->assertEquals('A birth event', $concept->getDescription());
        $this->assertEquals('Additional notes', $concept->getNote());
        $this->assertEquals('http://gedcomx.org/Birth', $concept->getGedcomxUri());
        $this->assertCount(1, $concept->getVocabTerms());
        $this->assertCount(1, $concept->getAttributes());
        $this->assertCount(1, $concept->getDefinitions());
    }

    public function testVocabConceptAddVocabTerm()
    {
        $concept = new VocabConcept();

        $term1 = new VocabTerm();
        $term1->setTypeUri('http://gedcomx.org/Label1');

        $term2 = new VocabTerm();
        $term2->setTypeUri('http://gedcomx.org/Label2');

        $concept->addVocabTerm($term1);
        $concept->addVocabTerm($term2);

        $this->assertCount(2, $concept->getVocabTerms());
    }

    public function testVocabConceptEmbed()
    {
        $concept1 = new VocabConcept();
        $concept1->setId('concept-1');

        $term1 = new VocabTerm();
        $term1->setId('term-1');
        $term1->setTypeUri('http://gedcomx.org/Original');

        $concept1->setVocabTerms([$term1]);

        $concept2 = new VocabConcept();
        $term2 = new VocabTerm();
        $term2->setId('term-2');
        $term2->setTypeUri('http://gedcomx.org/New');

        $concept2->setVocabTerms([$term2]);

        $concept1->embed($concept2);

        $this->assertCount(2, $concept1->getVocabTerms());
    }

    public function testVocabConceptEmbedWithSameId()
    {
        $concept1 = new VocabConcept();
        $term1 = new VocabTerm();
        $term1->setId('term-1');
        $term1->setTypeUri('http://gedcomx.org/Original');

        $concept1->setVocabTerms([$term1]);

        $concept2 = new VocabConcept();
        $term2 = new VocabTerm();
        $term2->setId('term-1');
        $term2->setTypeUri('http://gedcomx.org/Updated');

        $concept2->setVocabTerms([$term2]);

        $concept1->embed($concept2);

        // Should still be 1 term, but embedded
        $this->assertCount(1, $concept1->getVocabTerms());
    }

    public function testVocabConceptJsonRoundTrip()
    {
        $concept = new VocabConcept([
            'description' => 'Test concept',
            'note' => 'Test note',
            'gedcomxUri' => 'http://gedcomx.org/Test'
        ]);

        $json = $concept->toJson();
        $this->assertStringContainsString('Test concept', $json);

        $decoded = json_decode($json, true);
        $concept2 = new VocabConcept($decoded);

        $this->assertEquals('Test concept', $concept2->getDescription());
        $this->assertEquals('Test note', $concept2->getNote());
        $this->assertEquals('http://gedcomx.org/Test', $concept2->getGedcomxUri());
    }

    public function testVocabConceptsConstruction()
    {
        $concepts = new VocabConcepts();

        $concept1 = new VocabConcept();
        $concept1->setDescription('Concept 1');

        $concept2 = new VocabConcept();
        $concept2->setDescription('Concept 2');

        $concepts->setVocabConcepts([$concept1, $concept2]);

        $this->assertCount(2, $concepts->getVocabConcepts());
        $this->assertEquals('Concept 1', $concepts->getVocabConcepts()[0]->getDescription());
    }

    public function testVocabConceptsJsonRoundTrip()
    {
        $concepts = new VocabConcepts([
            'vocabConcepts' => [
                ['description' => 'First'],
                ['description' => 'Second']
            ]
        ]);

        $json = $concepts->toJson();
        $decoded = json_decode($json, true);
        $concepts2 = new VocabConcepts($decoded);

        $this->assertCount(2, $concepts2->getVocabConcepts());
        $this->assertEquals('First', $concepts2->getVocabConcepts()[0]->getDescription());
    }
}
