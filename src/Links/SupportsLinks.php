<?php


namespace Gedcomx\Links;

/**
 * An interface declaring support for managing hypermedia links. Links are not specified by GEDCOM X core, but as
 * extension elements by GEDCOM X RS.
 * Interface SupportsLinks
 *
 * @package Gedcomx\Links
 */
interface SupportsLinks
{
    /**
     * Gets the array of hypermedia links.
     *
     * @return array
     */
    public function getLinks();

    /**
     * Sets the array of hypermedia links.
     *
     * @param array $links
     */
    public function setLinks(array $links);

    /**
     * Adds a hypermedia link to the current array of links.
     *
     * @param \Gedcomx\Links\Link $link
     */
    public function addLink(Link $link);

    /**
     * Add a hypermedia link relationship
     *
     * @param string $rel  see Gedcom\Rs\Client\Rel
     * @param string $href The target URI.
     */
    public function addLinkRelation($rel, $href);

    /**
     * Add a templated link.
     *
     * @param string $rel      see Gedcom\Rs\Client\Rel
     * @param string $template The link template.
     */
    public function addTemplatedLink($rel, $template);

    /**
     * Get a link by its rel.
     *
     * @param string $rel see Gedcom\Rs\Client\Rel
     *
     * @return \Gedcomx\Links\Link
     */
    public function getLink($rel);

    /**
     * Get a list of links by rel.
     *
     * @param string $rel see Gedcom\Rs\Client\Rel
     *
     * @return \Gedcomx\Links\Link[]
     */
    public function getLinksByRel($rel);
}
