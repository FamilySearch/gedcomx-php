<?php 

namespace Gedcomx\Rs\Client\Util;

/**
 * A basic class for using various data sources with the artifacts and memories API.
 *
 * Class DataSource
 *
 * @package Gedcomx\Rs\Client\Util
 */
class DataSource
{
    private $filepath;
    private $title;
    private $parameters;
    private $isFile;

    /**
     * Sets the file that will be uploaded.
     *
     * @param string $filepath Path to the file to upload
     */
    public function setFile($filepath)
    {
        $this->isFile = true;
        $this->filepath = $filepath;
    }

    /**
     * Return the file path of this data source
     *
     * @return string
     */
    public function getFile()
    {
        return $this->filepath;
    }

    /**
     * Sets the title of the file to be uploaded.
     *
     * @param string $title A human readable name to associate with the data file
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Return the title of this data
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Add a key value pair as data
     *
     * @param $key
     * @param $value
     */
    public function addParameter($key, $value)
    {
        $this->parameters[$key] = $value;
    }

    /**
     * Return the array of parameters assigned to this data source
     *
     * @return array
     */
    public function getParameters(){
        return $this->parameters;
    }

    /**
     * Return whether or not this data source has a file associated with it
     *
     * @return boolean
     */
    public function isFile()
    {
        return $this->isFile;
    }
}