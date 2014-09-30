<?php


namespace Gedcomx\Common;


interface HasNotes {
    /**
     * The notes of a conclusion resource.
     *
     * @return array The notes of a conclusion resource.
     */
    public function getNotes();

    /**
     * The notes of a conclusion resource.
     *
     * @param Note[] $notes The notes of a conclusion resource.
     */
    public function setNotes($notes);

} 