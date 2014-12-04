<?php

namespace Gedcomx\GedcomxFile;

use Gedcomx\Extensions\FamilySearch\FamilySearchPlatform;
use Gedcomx\Gedcomx;

/**
 * Class GedcomxOutputStream
 * @package Gedcomx\GedcomxFile
 *
 *          Create a .gedx file
 */
class GedcomxOutputStream
{
    /**
     * @var GedcomEntrySerializer
     */
    private $serializer;
    /**
     * @var /ZipArchive
     */
    private $archive;
    /**
     * @var Manifest
     */
    private $manifest;
    /**
     * @var int
     */
    private $entryCount;

    /**
     * Create a new instance of the .gedx file creator
     *
     * @param \Gedcomx\GedcomxFile\GedcomxEntrySerializer $serializer
     */
    public function __construct(GedcomxEntrySerializer $serializer = null )
    {
        $this->serializer = $serializer;
        if ($this->serializer == null) {
            $this->serializer = new DefaultXMLSerialization();
        }
        $this->archive = new \ZipArchive();
        $this->manifest = new Manifest();
        $this->manifest->addAttribute('Manifest-Version','1.0');
        $this->manifest->addAttribute('Created-By','Gedcomx-PHP SDK');
    }

    /**
     * Add an attribute to the manifest file for this archive.
     *
     * @param string $key
     * @param string $value
     *
     * @return void
     */
    public function addAttribute($key, $value)
    {
        $this->manifest->addAttribute($key, $value);
    }

    /**
     * Add a manifest file attribute for an entry
     *
     * @param string $name
     * @param string $key
     * @param string $value
     *
     * @return void
     */
    public function addAttributeToEntry($name, $key, $value)
    {
        $this->manifest->addAttributeToEntry($name, $key, $value);
    }

    /**
     * Update a manifest file attribute for an entry
     *
     * @param string $name
     * @param string $key
     * @param string $value
     *
     * @return void
     */
    public function updateEntryAttribute($name, $key, $value)
    {
        $this->manifest->updateEntryAttribute($name, $key, $value);
    }

    /**
     * Add a Gedcomx object to the archive
     *
     * @param \Gedcomx\Gedcomx $resource
     * @param \DateTime        $lastModified
     *
     * @return void
     */
    public function addGedcomxResource(Gedcomx $resource, \DateTime $lastModified = null)
    {
        $this->addResource(Gedcomx::XML_MEDIA_TYPE, $resource, $lastModified);
    }

    /**
     * Add a FamilySearch Extensions object to the archive
     *
     * @param \Gedcomx\Extensions\FamilySearch\FamilySearchPlatform $resource
     * @param \DateTime                                             $lastModified
     *
     * @return void
     */
    public function addFamilySearchResource(FamilySearchPlatform $resource, \DateTime $lastModified = null)
    {
        $this->addResource(FamilySearchPlatform::XML_MEDIA_TYPE, $resource, $lastModified);
    }

    /**
     * Add a file resource to the archive
     *
     * @param string    $contentType
     * @param string    $filename
     * @param \DateTime $lastModified
     */
    public function addFileResource($contentType, $filename, \DateTime $lastModified = null)
    {
        if($lastModified == null) {
            $lastModified = new \DateTime();
        }
        $attributes['Content-Type'] = $contentType;
        $attributes['X-DC-modified'] = $lastModified->format("c");

        $content = file_get_contents($filename);

        $this->addEntry(basename($filename), $attributes, $content);
    }

    /**
     * Take the given resource, serialize it and add it to the archive.
     *
     * @param string    $contentType
     * @param string    $resource
     * @param \DateTime $lastModified
     */
    protected function addResource($contentType, $resource, \DateTime $lastModified)
    {
        if($lastModified == null) {
            $lastModified = new \DateTime();
        }
        $attributes['Content-Type'] = $contentType;
        $attributes['X-DC-modified'] = $lastModified->format("c");

        $content = $this->serializer->serialze($resource);

        $name = "tree" . ($this->entryCount > 0 ? $this->entryCount : '') . ".xml";
        $this->addEntry($name, $attributes, $content);
    }

    /**
     * Add the given content to the archive.
     *
     * @param string $name       The name of the content in the archive
     * @param array  $attributes An array of key value pairs
     * @param string $content    The content to add to the archive
     */
    protected function addEntry($name, $attributes, $content)
    {
        $this->manifest->addEntry($name);
        foreach ($attributes as $key => $value) {
            $this->manifest->addAttributeToEntry($name, $key, $value);
        }

        $this->archive->addFromString($name, $content);
    }
}