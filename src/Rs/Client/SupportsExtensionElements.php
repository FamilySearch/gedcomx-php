<?php


namespace Gedcomx\Rs\Client;

/**
 * An interface allowing a type to explicitly declare support for extension elements.
 */
interface SupportsExtensionElements {
    /**
     * Custom extension elements for a resource.
     *
     * @return Custom extension elements for a resource.
     */
    public function getExtensionElements();

    /**
     * Finds the first extension of a specified type.
     *
     * @param string clas The type.
     * @return mixed|null The extension, or null if none found.
     */
    public function findExtensionOfType($class);

  /**
   * Find the extensions of a specified type.
   *
   * @param string class The type.
   * @return mixed The extensions, possibly empty but not null.
   */
  public function findExtensionsOfType($class);

  /**
   * Add an extension element.
   *
   * @param mixed element The extension element to add.
   */
  public function addExtensionElement($element);

}