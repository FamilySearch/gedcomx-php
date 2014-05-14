<?php


namespace Gedcomx\Tests;

use XMLReader;

class XMLSerializationTest extends \PHPUnit_Framework_TestCase {

    public function testNothing()
    {
        //so build doesn't fail...
    }

//    public function testWriter()
//    {
//        $writer = new \XMLWriter();
//        $writer->openUri('php://output');
//        $writer->startDocument();
//        $writer->setIndent(4);
//        $writer->startElementNs(null, "howdy", "urn:hi");
//        $writer->writeAttribute("dink:doo", "dah");
//        $writer->startElementNs(null, "yeehaw", "urn:see");
//        $writer->text("hola!&");
//        $writer->endElement();
//        $writer->endElement();
//        $writer->startElementNs("h", "howdy", null);
//        $writer->writeAttributeNs("xmlns", "h", null, "urn:hi");
//        $writer->writeAttributeNs("xmlns", "s", null, "urn:see");
//        $writer->startElementNs("s", "yeehaw", null);
//        $writer->endElement();
//        $writer->endElement();
//        $writer->flush();
//
//        $xml = new \SimpleXMLElement('<eh></eh>');
//        $xml->addAttribute("go", "girl");
//        $childXml = $xml->addChild("howdy", null, "urn:hi");
//        $childXml->addChild("yeehaw", "myvalue", "urn:see");
//        $childXml->addChild("greetings", "partner", "urn:hi");
//        echo $xml->asXML();
//    }
//
//    public function testReader()
//    {
//        $xml = new XMLReader();
//        if (!$xml->open(__DIR__ . '/sample-gx.xml')) {
//            throw new \Exception('Unable to open ' . __DIR__ . '/sample-gx.xml');
//        }
//
//        $success = $xml->read();
//        while ($success && $xml->nodeType != \XMLReader::ELEMENT) {
//            $success = $xml->read();
//        }
//        if ($xml->nodeType != \XMLReader::ELEMENT) {
//            throw new \Exception("Unable to read XML: no start element found.");
//        }
//
//        while ($xml->read() && $xml->hasValue) {
//            printf("%s:%s = %s (empty=%s,hasValue=%s,nodeType=%s)\n", $xml->namespaceURI, $xml->localName, $xml->value, $xml->isEmptyElement, $xml->hasValue, $xml->nodeType);
//            if ($xml->hasAttributes) {
//                $moreAttributes = $xml->moveToFirstAttribute();
//                while ($moreAttributes) {
//                    printf("    @%s:%s = %s (empty=%s,hasValue=%s,nodeType=%s)\n", $xml->namespaceURI, $xml->localName, $xml->value, $xml->isEmptyElement, $xml->hasValue, $xml->nodeType);
//                    $moreAttributes = $xml->moveToNextAttribute();
//                }
//            }
//            print("\n");
//        }
//
//        $xml = new XMLReader();
//        if (!$xml->open(__DIR__ . '/sample-gx.xml')) {
//            throw new \Exception('Unable to open ' . __DIR__ . '/sample-gx.xml');
//        }
//
//        $dom = new \DOMDocument();
//        $nodeFactory = $dom;
//        $dom->formatOutput = true;
//        while ($xml->read()) {
//            if ($xml->nodeType == XMLReader::ELEMENT) {
//                $e = $nodeFactory->createElementNS($xml->namespaceURI, $xml->localName);
//                $dom->appendChild($e);
//                if ($xml->hasAttributes) {
//                    $moreAttributes = $xml->moveToFirstAttribute();
//                    while ($moreAttributes) {
//                        $e->setAttributeNS($xml->namespaceURI, $xml->localName, $xml->value);
//                        $moreAttributes = $xml->moveToNextAttribute();
//                    }
//                }
//                $dom = $e;
//            }
//            else if ($xml->nodeType == XMLReader::TEXT) {
//                $dom->textContent = $xml->value;
//            }
//            else if ($xml->nodeType == XMLReader::END_ELEMENT) {
//                $dom = $dom->parentNode;
//            }
//        }
//
//        echo $nodeFactory->saveXML();
//        echo simplexml_import_dom($nodeFactory)->asXML();
//    }

}
