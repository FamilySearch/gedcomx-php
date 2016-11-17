<?php

namespace GedcomX\Exensions\FamilySearch;

/**
 * Representation of an OAuth2 token response.
 */
class OAuth2
{
    
    /**
     * @var string
     */
    private $accessToken;
    
    /**
     * @var string
     */
    private $tokenType;
    
    /**
     * @var string
     */
    private $error; 
    
    /**
     * @var string
     */
    private $errorDescription;
    
    /**
     * Constructs an OAuth2 object from an associative array
     * 
     * @param array $0 Array of OAuth2 data
     */
    public function __construct($o = array())
    {
        if(is_array($o)){
            $this->initFromArray($o);
        }
    }
    
    /**
     * @return string
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * @param string $accessToken
     */
    public function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;
    }
    
    /**
     * @return string
     */
    public function getTokenType()
    {
        return $this->tokenType;
    }

    /**
     * @param string $tokenType
     */
    public function setTokenType($tokenType)
    {
        $this->tokenType = $tokenType;
    }
    
    /**
     * @return string
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @param string $error
     */
    public function setError($error)
    {
        $this->error = $error;
    }
    
    /**
     * @return string
     */
    public function getErrorDescription()
    {
        return $this->errorDescription;
    }

    /**
     * @param string $errorDescription
     */
    public function setErrorDescription($errorDescription)
    {
        $this->errorDescription = $errorDescription;
    }
    
    /**
     * Returns the associative array for this OAuth2
     *
     * @return array
     */
    public function toArray()
    {
        $a = array();
        if ($this->accessToken) {
            $a["accessToken"] = $this->accessToken;
        }
        if ($this->tokenType) {
            $a["tokenType"] = $this->tokenType;
        }
        if ($this->error) {
            $a["error"] = $this->error;
        }
        if ($this->errorDescription) {
            $a["errorDescription"] = $this->errorDescription;
        }
        return $a;
    }

    /**
     * Returns the JSON string for this OAuth2
     *
     * @return string
     */
    public function toJson()
    {
        return json_encode($this->toArray());
    }

    /**
     * Initializes this OAuth2 from an associative array
     *
     * @param array $o
     */
    public function initFromArray($o)
    {
        if (isset($o['accessToken'])) {
            $this->accessToken = $o["accessToken"];
        }
        if (isset($o['tokenType'])) {
            $this->tokenType = $o["tokenType"];
        }
        if (isset($o['error'])) {
            $this->error = $o["error"];
        }
        if (isset($o['errorDescription'])) {
            $this->errorDescription = $o["errorDescription"];
        }
    }
}