<?php


namespace Gedcomx\Rs\Api;


class GedcomxSearchQuery
{

    const NAME = "name";
    const GIVEN_NAME = "givenName";
    const SURNAME = "surname";
    const GENDER = "gender";
    const BIRTH_DATE = "birthDate";
    const BIRTH_PLACE = "birthPlace";
    const DEATH_DATE = "deathDate";
    const DEATH_PLACE = "deathPlace";
    const MARRIAGE_DATE = "marriageDate";
    const MARRIAGE_PLACE = "marriagePlace";
    const FATHER_NAME = "fatherName";
    const FATHER_GIVEN_NAME = "fatherGivenName";
    const FATHER_SURNAME = "fatherSurname";
    const FATHER_GENDER = "fatherGender";
    const FATHER_BIRTH_DATE = "fatherBirthDate";
    const FATHER_BIRTH_PLACE = "fatherBirthPlace";
    const FATHER_DEATH_DATE = "fatherDeathDate";
    const FATHER_DEATH_PLACE = "fatherDeathPlace";
    const FATHER_MARRIAGE_DATE = "fatherMarriageDate";
    const FATHER_MARRIAGE_PLACE = "fatherMarriagePlace";
    const MOTHER_NAME = "motherName";
    const MOTHER_GIVEN_NAME = "motherGivenName";
    const MOTHER_SURNAME = "motherSurname";
    const MOTHER_GENDER = "motherGender";
    const MOTHER_BIRTH_DATE = "motherBirthDate";
    const MOTHER_BIRTH_PLACE = "motherBirthPlace";
    const MOTHER_DEATH_DATE = "motherDeathDate";
    const MOTHER_DEATH_PLACE = "motherDeathPlace";
    const MOTHER_MARRIAGE_DATE = "motherMarriageDate";
    const MOTHER_MARRIAGE_PLACE = "motherMarriagePlace";
    const SPOUSE_NAME = "spouseName";
    const SPOUSE_GIVEN_NAME = "spouseGivenName";
    const SPOUSE_SURNAME = "spouseSurname";
    const SPOUSE_GENDER = "spouseGender";
    const SPOUSE_BIRTH_DATE = "spouseBirthDate";
    const SPOUSE_BIRTH_PLACE = "spouseBirthPlace";
    const SPOUSE_DEATH_DATE = "spouseDeathDate";
    const SPOUSE_DEATH_PLACE = "spouseDeathPlace";
    const SPOUSE_MARRIAGE_DATE = "spouseMarriageDate";
    const SPOUSE_MARRIAGE_PLACE = "spouseMarriagePlace";
    const PARENT_NAME = "parentName";
    const PARENT_GIVEN_NAME = "parentGivenName";
    const PARENT_SURNAME = "parentSurname";
    const PARENT_GENDER = "parentGender";
    const PARENT_BIRTH_DATE = "parentBirthDate";
    const PARENT_BIRTH_PLACE = "parentBirthPlace";
    const PARENT_DEATH_DATE = "parentDeathDate";
    const PARENT_DEATH_PLACE = "parentDeathPlace";
    const PARENT_MARRIAGE_DATE = "parentMarriageDate";
    const PARENT_MARRIAGE_PLACE = "parentMarriagePlace";

    /**
     * @var SearchParameter[]
     */
    private $parameters = array();

    public function __toString()
    {
        return implode(' ', $this->parameters);
    }

    /**
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
     * @return boolean
     */
    public function getExact()
    {
        return $this->exact;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

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