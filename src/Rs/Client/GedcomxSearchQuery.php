<?php


namespace Gedcomx\Rs\Client;

/**
 * This is a helper utility for building syntactically correct person search query strings.
 *
 * Class GedcomxSearchQuery
 *
 * @package Gedcomx\Rs\Client
 */
class GedcomxSearchQuery
{
    /**
     * The name parameter in person search queries
     */
    const NAME = "name";
    /**
     * The given name parameter in person search queries
     */
    const GIVEN_NAME = "givenName";
    /**
     * The surname parameter in person search queries
     */
    const SURNAME = "surname";
    /**
     * The gender parameter in person search queries
     */
    const GENDER = "gender";
    /**
     * The birth date parameter in person search queries
     */
    const BIRTH_DATE = "birthDate";
    /**
     * The birth place parameter in person search queries
     */
    const BIRTH_PLACE = "birthPlace";
    /**
     * The death date parameter in person search queries
     */
    const DEATH_DATE = "deathDate";
    /**
     * The death place parameter in person search queries
     */
    const DEATH_PLACE = "deathPlace";
    /**
     * The marriage date parameter in person search queries
     */
    const MARRIAGE_DATE = "marriageDate";
    /**
     * The marriage place parameter in person search queries
     */
    const MARRIAGE_PLACE = "marriagePlace";
    /**
     * The father name parameter in person search queries
     */
    const FATHER_NAME = "fatherName";
    /**
     * The father given name parameter in person search queries
     */
    const FATHER_GIVEN_NAME = "fatherGivenName";
    /**
     * The father surname parameter in person search queries
     */
    const FATHER_SURNAME = "fatherSurname";
    /**
     * The father gender parameter in person search queries
     */
    const FATHER_GENDER = "fatherGender";
    /**
     * The father birth date parameter in person search queries
     */
    const FATHER_BIRTH_DATE = "fatherBirthDate";
    /**
     * The father birth place parameter in person search queries
     */
    const FATHER_BIRTH_PLACE = "fatherBirthPlace";
    /**
     * The father death date parameter in person search queries
     */
    const FATHER_DEATH_DATE = "fatherDeathDate";
    /**
     * The father death place parameter in person search queries
     */
    const FATHER_DEATH_PLACE = "fatherDeathPlace";
    /**
     * The father marriage date parameter in person search queries
     */
    const FATHER_MARRIAGE_DATE = "fatherMarriageDate";
    /**
     * The father marriage place parameter in person search queries
     */
    const FATHER_MARRIAGE_PLACE = "fatherMarriagePlace";
    /**
     * The mother name parameter in person search queries
     */
    const MOTHER_NAME = "motherName";
    /**
     * The mother given name parameter in person search queries
     */
    const MOTHER_GIVEN_NAME = "motherGivenName";
    /**
     * The mother surname parameter in person search queries
     */
    const MOTHER_SURNAME = "motherSurname";
    /**
     * The mother gender parameter in person search queries
     */
    const MOTHER_GENDER = "motherGender";
    /**
     * The mother birth date parameter in person search queries
     */
    const MOTHER_BIRTH_DATE = "motherBirthDate";
    /**
     * The mother birth place parameter in person search queries
     */
    const MOTHER_BIRTH_PLACE = "motherBirthPlace";
    /**
     * The mother death date parameter in person search queries
     */
    const MOTHER_DEATH_DATE = "motherDeathDate";
    /**
     * The mother death place parameter in person search queries
     */
    const MOTHER_DEATH_PLACE = "motherDeathPlace";
    /**
     * The mother marriage date parameter in person search queries
     */
    const MOTHER_MARRIAGE_DATE = "motherMarriageDate";
    /**
     * The mother marriage place parameter in person search queries
     */
    const MOTHER_MARRIAGE_PLACE = "motherMarriagePlace";
    /**
     * The spouse name parameter in person search queries
     */
    const SPOUSE_NAME = "spouseName";
    /**
     * The spouse given name parameter in person search queries
     */
    const SPOUSE_GIVEN_NAME = "spouseGivenName";
    /**
     * The spouse surname parameter in person search queries
     */
    const SPOUSE_SURNAME = "spouseSurname";
    /**
     * The spouse gender parameter in person search queries
     */
    const SPOUSE_GENDER = "spouseGender";
    /**
     * The spouse birth date parameter in person search queries
     */
    const SPOUSE_BIRTH_DATE = "spouseBirthDate";
    /**
     * The spouse birth place parameter in person search queries
     */
    const SPOUSE_BIRTH_PLACE = "spouseBirthPlace";
    /**
     * The spouse death date parameter in person search queries
     */
    const SPOUSE_DEATH_DATE = "spouseDeathDate";
    /**
     * The spouse death place parameter in person search queries
     */
    const SPOUSE_DEATH_PLACE = "spouseDeathPlace";
    /**
     * The spouse marriage date parameter in person search queries
     */
    const SPOUSE_MARRIAGE_DATE = "spouseMarriageDate";
    /**
     * The spouse marriage place parameter in person search queries
     */
    const SPOUSE_MARRIAGE_PLACE = "spouseMarriagePlace";
    /**
     * The parent name parameter in person search queries
     */
    const PARENT_NAME = "parentName";
    /**
     * The parent given name parameter in person search queries
     */
    const PARENT_GIVEN_NAME = "parentGivenName";
    /**
     * The parent surname parameter in person search queries
     */
    const PARENT_SURNAME = "parentSurname";
    /**
     * The parent gender parameter in person search queries
     */
    const PARENT_GENDER = "parentGender";
    /**
     * The parent birth date parameter in person search queries
     */
    const PARENT_BIRTH_DATE = "parentBirthDate";
    /**
     * The parent birth place parameter in person search queries
     */
    const PARENT_BIRTH_PLACE = "parentBirthPlace";
    /**
     * The parent death date parameter in person search queries
     */
    const PARENT_DEATH_DATE = "parentDeathDate";
    /**
     * The parent death place parameter in person search queries
     */
    const PARENT_DEATH_PLACE = "parentDeathPlace";
    /**
     * The parent marriage date parameter in person search queries
     */
    const PARENT_MARRIAGE_DATE = "parentMarriageDate";
    /**
     * The parent marriage place parameter in person search queries
     */
    const PARENT_MARRIAGE_PLACE = "parentMarriagePlace";

    /**
     * @var SearchParameter[]
     */
    private $parameters = array();

    /**
     * Builds the query string to use for searching.
     *
     * @return string
     */
    public function __toString()
    {
        return implode(' ', $this->parameters);
    }

    /**
     * Creates a generic search parameter with the specified name and value. The parameter is added to the array of
     * current parameters and $this is returned.
     * @param string $name
     * @param string $value
     * @param bool $exact
     * @return GedcomxSearchQuery $this
     */
    public function param($name, $value = null, $exact = false)
    {
        array_push($this->parameters, new SearchParameter($name, $value, $exact));
        return $this;
    }
}

/**
 * Represents a generic search parameter.
 */
class SearchParameter
{
    /**
     * @var string
     */
    private $name;
    /**
     * @var string
     */
    private $value;
    /**
     * @var bool
     */
    private $exact;

    private static $whitespace = array("\n", "\t", "\f", "\013");


    /**
     * Constructs a new generic search parameter using the specified values.
     *
     * @param string $name
     * @param string $value
     * @param bool $exact
     */
    function __construct($name, $value, $exact = false)
    {
        $this->name = $name;
        $this->value = $value;
        $this->exact = $exact;
    }

    /**
     * Gets a value indicating whether the current search parameter requires exact value match results. See remarks.
     *
     * @return boolean
     */
    public function getExact()
    {
        return $this->exact;
    }

    /**
     * Gets the name of the current search parameter..
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Gets the value of the current search parameter.
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Returns a string that is a syntactically conformant search query that can be used in REST API search requests.
     *
     * @return string
     */
    public function __toString()
    {
        $str = $this->name;
        if ($this->value) {
            $str = $str . ':';
            $escaped = str_replace(SearchParameter::$whitespace, ' ', $this->value);
            $escaped = str_replace('"', '\\"', $escaped);
            $needsQuote = strpos($escaped, ' ');
            if ($needsQuote) {
                $str = $str . '"';
            }
            $str = $str . $escaped;
            if ($needsQuote) {
                $str = $str . '"';
            }
            if ($this->exact) {
                $str = $str . '~';
            }
        }
        return $str;
    }

}