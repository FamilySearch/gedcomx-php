<?php

namespace Gedcomx\Source;

interface ReferencesSources {
    /**
     * The references to the sources of a conclusion resource.
     *
     * @return array The references to the sources of a conclusion resource.
     */
    public function getSources();

  /**
   * The references to the sources of a conclusion resource.
   *
   * @param SourceReference[] $notes The references to the sources of a conclusion resource.
   */
  public function setSources( $notes);

} 