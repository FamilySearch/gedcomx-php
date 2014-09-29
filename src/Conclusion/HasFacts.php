<?php

namespace Gedcomx\Conclusion;

interface HasFacts {

    /**
     * @return Fact[]
     */
    public function getFacts();

    /**
     * @param Fact[] $facts an array of Fact objects
     */
    public function setFacts( $facts );
} 