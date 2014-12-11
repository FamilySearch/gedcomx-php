<?php

namespace Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree;

/**
 * A basic interface declaring the explicit ability to produce a self URI.
 *
 * Interface PreferredRelationshipState
 *
 * @package Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree
 */
interface PreferredRelationshipState
{
    /**
     * Gets the self URI.
     *
     * @return string
     */
    function getSelfUri();
} 