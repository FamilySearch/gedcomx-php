<?php

namespace Gedcomx\Extensions\FamilySearch\Platform;

/**
 * Class Error
 *
 * @package Gedcomx\Extensions\FamilySearch\Platform
 *
 * Manages the Error object.
 */
class Error
{
    private $code;
    private $label;
    private $message;
    private $stacktrace;
    
    /**
     * Constructs an Error from a (parsed) JSON hash
     *
     * @param mixed $o Either an array (JSON) or an XMLReader.
     *
     * @throws \Exception
     */
    public function __construct($o = null)
    {
        if (is_array($o)) {
            $this->initFromArray($o);
        }
        else if ($o instanceof \XMLReader) {
            $success = true;
            while ($success && $o->nodeType != \XMLReader::ELEMENT) {
                $success = $o->read();
            }
            if ($o->nodeType != \XMLReader::ELEMENT) {
                throw new \Exception("Unable to read XML: no start element found.");
            }
    
            $this->initFromReader($o);
        }
    }
    
    /**
     * @return integer
     */
    public function getCode()
    {
        return $this->code;
    }
    
    /**
     * @param integer $code
     */
    public function setCode($code){
        $this->code = $code;
    }
    
    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }
    
    /**
     * @param string $code
     */
    public function setLabel($label){
        $this->label = $label;
    }
    
    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }
    
    /**
     * @param string $message
     */
    public function setMessage($message){
        $this->message = $message;
    }
    
    /**
     * @return string
     */
    public function getStacktrace()
    {
        return $this->stacktrace;
    }
    
    /**
     * @param string $stacktrace
     */
    public function setStacktrace($stacktrace){
        $this->stacktrace = $stacktrace;
    }
    
    /**
     * Returns the associative array for this User
     *
     * @return array
     */
    public function toArray()
    {
        $a = [];
        if ($this->code) {
            $a["code"] = $this->code;
        }
        if ($this->label) {
            $a["label"] = $this->label;
        }
        if ($this->message) {
            $a["message"] = $this->message;
        }
        if ($this->stacktrace) {
            $a["stacktrace"] = $this->stacktrace;
        }
        return $a;
    }
    
    /**
     * Initializes this User from an associative array
     *
     * @param array $o
     */
    public function initFromArray(array $o)
    {
        if (isset($o['code'])) {
            $this->code = $o["code"];
            unset($o['code']);
        }
        if (isset($o['label'])) {
            $this->label = $o["label"];
            unset($o['label']);
        }
        if (isset($o['message'])) {
            $this->message = $o["message"];
            unset($o['message']);
        }
        if (isset($o['stacktrace'])) {
            $this->stacktrace = $o["stacktrace"];
            unset($o['stacktrace']);
        }
    }
    
    /**
     * Sets a known child element of Error from an XML reader.
     *
     * @param \XMLReader $xml The reader.
     *
     * @return bool Whether a child element was set.
     */
    protected function setKnownChildElement(\XMLReader $xml) 
    {
        $happened = parent::setKnownChildElement($xml);
        if ($happened) {
          return true;
        }
        else if (($xml->localName == 'code') && ($xml->namespaceURI == 'http://familysearch.org/v1/')) {
            $child = '';
            while ($xml->read() && $xml->hasValue) {
                $child = $child . $xml->value;
            }
            $this->code = $child;
            $happened = true;
        }
        else if (($xml->localName == 'label') && ($xml->namespaceURI == 'http://familysearch.org/v1/')) {
            $child = '';
            while ($xml->read() && $xml->hasValue) {
                $child = $child . $xml->value;
            }
            $this->label = $child;
            $happened = true;
        }
        else if (($xml->localName == 'message') && ($xml->namespaceURI == 'http://familysearch.org/v1/')) {
            $child = '';
            while ($xml->read() && $xml->hasValue) {
                $child = $child . $xml->value;
            }
            $this->message = $child;
            $happened = true;
        }
        else if (($xml->localName == 'stacktrace') && ($xml->namespaceURI == 'http://familysearch.org/v1/')) {
            $child = '';
            while ($xml->read() && $xml->hasValue) {
                $child = $child . $xml->value;
            }
            $this->stacktrace = $child;
            $happened = true;
        }
        return $happened;
    }

    /**
     * Sets a known attribute of Error from an XML reader.
     *
     * @param \XMLReader $xml The reader.
     *
     * @return bool Whether an attribute was set.
     */
    protected function setKnownAttribute(\XMLReader $xml)
    {
        if (parent::setKnownAttribute($xml)) {
            return true;
        }

        return false;
    }

    /**
     * Writes this User to an XML writer.
     *
     * @param \XMLWriter $writer The XML writer.
     * @param bool $includeNamespaces Whether to write out the namespaces in the element.
     */
    public function toXml(\XMLWriter $writer, $includeNamespaces = true)
    {
        $writer->startElementNS('fs', 'error', null);
        if ($includeNamespaces) {
            $writer->writeAttributeNs('xmlns', 'gx', null, 'http://gedcomx.org/v1/');
            $writer->writeAttributeNs('xmlns', 'fs', null, 'http://familysearch.org/v1/');
        }
        $this->writeXmlContents($writer);
        $writer->endElement();
    }

    /**
     * Writes the contents of this User to an XML writer. The startElement is expected to be already provided.
     *
     * @param \XMLWriter $writer The XML writer.
     */
    public function writeXmlContents(\XMLWriter $writer)
    {
        if ($this->code) {
            $writer->startElementNs('fs', 'code', null);
            $writer->text($this->code);
            $writer->endElement();
        }
        if ($this->label) {
            $writer->startElementNs('fs', 'label', null);
            $writer->text($this->label);
            $writer->endElement();
        }
        if ($this->message) {
            $writer->startElementNs('fs', 'message', null);
            $writer->text($this->message);
            $writer->endElement();
        }
        if ($this->stacktrace) {
            $writer->startElementNs('fs', 'stacktrace', null);
            $writer->text($this->stacktrace);
            $writer->endElement();
        }
    }
}