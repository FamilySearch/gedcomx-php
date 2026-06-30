<?php

namespace Gedcomx\Conclusion;

/**
 * Interface for model classes that have both a date and a place.
 *
 * This interface provides a consistent contract for GEDCOM X conclusion classes
 * that include temporal and geographic context, such as facts and events.
 */
interface HasDateAndPlace {

    /**
     * The date associated with this conclusion.
     *
     * @return DateInfo The date.
     */
    public function getDate();

    /**
     * The date associated with this conclusion.
     *
     * @param DateInfo $date The date.
     */
    public function setDate($date);

    /**
     * The place associated with this conclusion.
     *
     * @return PlaceReference The place.
     */
    public function getPlace();

    /**
     * The place associated with this conclusion.
     *
     * @param PlaceReference $place The place.
     */
    public function setPlace($place);
}
