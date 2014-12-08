<?php 

namespace Gedcomx\Rs\Client;

/**
 * This is a collection of constants used with RDF processing.
 * At this time, only namespaces are defined here.
 */
class VocabConstants 
{
    /**
     * The RDF namespace, {@link http://www.w3.org/1999/02/22-rdf-syntax-ns#}.
     */
    const RDF_NAMESPACE = "http://www.w3.org/1999/02/22-rdf-syntax-ns#";
    /**
     * The RDF Sequence namespace, {@link http://www.w3.org/1999/02/22-rdf-syntax-ns#Seq}.
     */
    const RDF_SEQUENCE_TYPE  = "http://www.w3.org/1999/02/22-rdf-syntax-ns#Seq";
    /**
     * The RDFS namespace, {@link http://www.w3.org/2000/01/rdf-schema#}.
     */
    const RDFS_NAMESPACE = "http://www.w3.org/2000/01/rdf-schema#";
    /**
     * The DC namespace, {@link http://purl.org/dc/terms/}.
     */
    const DC_NAMESPACE = "http://purl.org/dc/terms/";
    /**
     * The XML namespace, {@link http://www.w3.org/XML/1998/namespace}.
     */
    const XML_NAMESPACE = "http://www.w3.org/XML/1998/namespace";
}