<?php 

namespace Gedcomx\Rs\Client\Util;

use Guzzle\Http\Message\PostFile;

class DataSource
{
    private $filepath;
    private $title;
    private $parameters;
    private $isFile;

    /**
     * @param string $filepath Path to the file to upload
     */
    public function setFile($filepath)
    {
        $this->isFile = true;
        $this->filename = $filepath;
    }

    /**
     * Return the file path of this data source
     *
     * @return string
     */
    public function getPostFile()
    {
        return new PostFile('artifact',$this->filepath);
    }

    /**
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
        return $this->isFile();
    }
}