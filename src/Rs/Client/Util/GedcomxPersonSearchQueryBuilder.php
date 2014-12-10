<?php


namespace Gedcomx\Rs\Client\Util;


/**
 * This is a helper utility for building syntactically correct person search query strings.
 *
 * Class GedcomxPersonSearchQueryBuilder
 *
 * @package Gedcomx\Rs\Client\Util
 */
class GedcomxPersonSearchQueryBuilder extends GedcomxBaseSearchQueryBuilder{
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
	 * Creates a generic search parameter with the specified name and value.
	 * The prefix parameter can take on three forms:
	 *     "+": The parameter search value should be found in the search results
	 *     null: The parameter search filter is optional
	 *     "-": The parameter search value should not found in the search results (i.e., perform a NOT seaarch)
	 *
	 * @param $prefix
	 * @param $name
	 * @param $value
	 * @param $exact
	 *
	 * @return $this
	 */
	private function param( $prefix, $name, $value, $exact) {
		$this->parameters[] = new SearchParameter($prefix, $name, $value, $exact);
		return $this;
	}

	/**
	 * Creates a name search parameter with the search value.
	 *
	 * @param      $value
	 * @param bool $exact
	 * @param bool $required
	 *
	 * @return \Gedcomx\Rs\Client\Util\GedcomxPersonSearchQueryBuilder
	 */
	public function name($value, $exact = false, $required = false){
		return $this->param(($required ? "+" : null), self::NAME, $value, $exact);
	}

	/**
	 * Creates a given name search parameter with the search value.
	 *
	 * @param      $value
	 * @param bool $exact
	 * @param bool $required
	 *
	 * @return \Gedcomx\Rs\Client\Util\GedcomxPersonSearchQueryBuilder
	 */
	public function givenName($value, $exact = false, $required = false){
		return $this->param(($required ? "+" : null), self::GIVEN_NAME, $value, $exact);
	}

	/**
	 * Creates a surname search parameter with the search value.
	 *
	 * @param      $value
	 * @param bool $exact
	 * @param bool $required
	 *
	 * @return \Gedcomx\Rs\Client\Util\GedcomxPersonSearchQueryBuilder
	 */
	public function surname ($value, $exact = false, $required = false){
		return $this->param(($required ? "+" : null), self::SURNAME, $value, $exact);
	}

	/**
	 * Creates a gender search parameter with the search value.
	 *
	 * @param      $value
	 * @param bool $exact
	 * @param bool $required
	 *
	 * @return \Gedcomx\Rs\Client\Util\GedcomxPersonSearchQueryBuilder
	 */
	public function gender($value, $exact = false, $required = false){
		return $this->param(($required ? "+" : null), self::GENDER, $value, $exact);
	}

	/**
	 * Creates a birth date search parameter with the search value.
	 *
	 * @param      $value
	 * @param bool $exact
	 * @param bool $required
	 *
	 * @return \Gedcomx\Rs\Client\Util\GedcomxPersonSearchQueryBuilder
	 */
	public function birthDate($value, $exact = false, $required = false){
		return $this->param(($required ? "+" : null), self::BIRTH_DATE, $value, $exact);
	}

	/**
	 * Creates a birth place search parameter with the search value.
	 *
	 * @param      $value
	 * @param bool $exact
	 * @param bool $required
	 *
	 * @return \Gedcomx\Rs\Client\Util\GedcomxPersonSearchQueryBuilder
	 */
	public function birthPlace ($value, $exact = false, $required = false){
		return $this->param(($required ? "+" : null), self::BIRTH_PLACE, $value, $exact);
	}

	/**
	 * Creates a death date search parameter with the search value.
	 *
	 * @param      $value
	 * @param bool $exact
	 * @param bool $required
	 *
	 * @return \Gedcomx\Rs\Client\Util\GedcomxPersonSearchQueryBuilder
	 */
	public function deathDate($value, $exact = false, $required = false){
		return $this->param(($required ? "+" : null), self::DEATH_DATE, $value, $exact);
	}

	/**
	 * Creates a death place search parameter with the search value.
	 *
	 * @param      $value
	 * @param bool $exact
	 * @param bool $required
	 *
	 * @return \Gedcomx\Rs\Client\Util\GedcomxPersonSearchQueryBuilder
	 */
	public function deathPlace($value, $exact = false, $required = false){
		return $this->param(($required ? "+" : null), self::DEATH_PLACE, $value, $exact);
	}

	/**
	 * Creates a marriage date search parameter with the search value.
	 *
	 * @param      $value
	 * @param bool $exact
	 * @param bool $required
	 *
	 * @return \Gedcomx\Rs\Client\Util\GedcomxPersonSearchQueryBuilder
	 */
	public function marriageDate($value, $exact = false, $required = false){
		return $this->param(($required ? "+" : null), self::MARRIAGE_DATE, $value, $exact);
	}

	/**
	 * Creates a marriage place search parameter with the search value.
	 *
	 * @param      $value
	 * @param bool $exact
	 * @param bool $required
	 *
	 * @return \Gedcomx\Rs\Client\Util\GedcomxPersonSearchQueryBuilder
	 */
	public function marriagePlace($value, $exact = false, $required = false){
		return $this->param(($required ? "+" : null), self::MARRIAGE_PLACE, $value, $exact);
	}

	/**
	 * Creates a father name search parameter with the search value.
	 *
	 * @param      $value
	 * @param bool $exact
	 * @param bool $required
	 *
	 * @return \Gedcomx\Rs\Client\Util\GedcomxPersonSearchQueryBuilder
	 */
	public function fatherName($value, $exact = false, $required = false){
		return $this->param(($required ? "+" : null), self::FATHER_NAME, $value, $exact);
	}

	/**
	 * Creates a father given name search parameter with the search value.
	 *
	 * @param      $value
	 * @param bool $exact
	 * @param bool $required
	 *
	 * @return \Gedcomx\Rs\Client\Util\GedcomxPersonSearchQueryBuilder
	 */
	public function fatherGivenName($value, $exact = false, $required = false){
		return $this->param(($required ? "+" : null), self::FATHER_GIVEN_NAME, $value, $exact);
	}

	/**
	 * Creates a father surname search parameter with the search value.
	 *
	 * @param      $value
	 * @param bool $exact
	 * @param bool $required
	 *
	 * @return \Gedcomx\Rs\Client\Util\GedcomxPersonSearchQueryBuilder
	 */
	public function fatherSurname($value, $exact = false, $required = false){
		return $this->param(($required ? "+" : null), self::FATHER_SURNAME, $value, $exact);
	}

	/**
	 * Creates a father gender search parameter with the search value.
	 *
	 * @param      $value
	 * @param bool $exact
	 * @param bool $required
	 *
	 * @return \Gedcomx\Rs\Client\Util\GedcomxPersonSearchQueryBuilder
	 */
	public function fatherGender($value, $exact = false, $required = false){
		return $this->param(($required ? "+" : null), self::FATHER_GENDER, $value, $exact);
	}

	/**
	 * Creates a father birth date search parameter with the search value.
	 *
	 * @param      $value
	 * @param bool $exact
	 * @param bool $required
	 *
	 * @return \Gedcomx\Rs\Client\Util\GedcomxPersonSearchQueryBuilder
	 */
	public function fatherBirthDate($value, $exact = false, $required = false){
		return $this->param(($required ? "+" : null), self::FATHER_BIRTH_DATE, $value, $exact);
	}

	/**
	 * Creates a father birth place search parameter with the search value.
	 *
	 * @param      $value
	 * @param bool $exact
	 * @param bool $required
	 *
	 * @return \Gedcomx\Rs\Client\Util\GedcomxPersonSearchQueryBuilder
	 */
	public function fatherBirthPlace($value, $exact = false, $required = false){
		return $this->param(($required ? "+" : null), self::FATHER_BIRTH_PLACE, $value, $exact);
	}

	/**
	 * Creates a father death date search parameter with the search value.
	 *
	 * @param      $value
	 * @param bool $exact
	 * @param bool $required
	 *
	 * @return \Gedcomx\Rs\Client\Util\GedcomxPersonSearchQueryBuilder
	 */
	public function fatherDeathDate($value, $exact = false, $required = false){
		return $this->param(($required ? "+" : null), self::FATHER_DEATH_DATE, $value, $exact);
	}

	/**
	 * Creates a father death place search parameter with the search value.
	 *
	 * @param      $value
	 * @param bool $exact
	 * @param bool $required
	 *
	 * @return \Gedcomx\Rs\Client\Util\GedcomxPersonSearchQueryBuilder
	 */
	public function fatherDeathPlace($value, $exact = false, $required = false){
		return $this->param(($required ? "+" : null), self::FATHER_DEATH_PLACE, $value, $exact);
	}

	/**
	 * Creates a father marriage date search parameter with the search value.
	 *
	 * @param      $value
	 * @param bool $exact
	 * @param bool $required
	 *
	 * @return \Gedcomx\Rs\Client\Util\GedcomxPersonSearchQueryBuilder
	 */
	public function fatherMarriageDate($value, $exact = false, $required = false){
		return $this->param(($required ? "+" : null), self::FATHER_MARRIAGE_DATE, $value, $exact);
	}

	/**
	 * Creates a father marriage place search parameter with the search value.
	 *
	 * @param      $value
	 * @param bool $exact
	 * @param bool $required
	 *
	 * @return \Gedcomx\Rs\Client\Util\GedcomxPersonSearchQueryBuilder
	 */
	public function fatherMarriagePlace($value, $exact = false, $required = false){
		return $this->param(($required ? "+" : null), self::FATHER_MARRIAGE_PLACE, $value, $exact);
	}

	/**
	 * Creates a mother name search parameter with the search value.
	 *
	 * @param      $value
	 * @param bool $exact
	 * @param bool $required
	 *
	 * @return \Gedcomx\Rs\Client\Util\GedcomxPersonSearchQueryBuilder
	 */
	public function motherName($value, $exact = false, $required = false){
		return $this->param(($required ? "+" : null), self::MOTHER_NAME, $value, $exact);
	}

	/**
	 * Creates a mother given name search parameter with the search value.
	 *
	 * @param      $value
	 * @param bool $exact
	 * @param bool $required
	 *
	 * @return \Gedcomx\Rs\Client\Util\GedcomxPersonSearchQueryBuilder
	 */
	public function motherGivenName($value, $exact = false, $required = false){
		return $this->param(($required ? "+" : null), self::MOTHER_GIVEN_NAME, $value, $exact);
	}

	/**
	 * Creates a mother surname search parameter with the search value.
	 *
	 * @param      $value
	 * @param bool $exact
	 * @param bool $required
	 *
	 * @return \Gedcomx\Rs\Client\Util\GedcomxPersonSearchQueryBuilder
	 */
	public function motherSurname($value, $exact = false, $required = false){
		return $this->param(($required ? "+" : null), self::MOTHER_SURNAME, $value, $exact);
	}

	/**
	 * Creates a mother gender search parameter with the search value.
	 *
	 * @param      $value
	 * @param bool $exact
	 * @param bool $required
	 *
	 * @return \Gedcomx\Rs\Client\Util\GedcomxPersonSearchQueryBuilder
	 */
	public function motherGender($value, $exact = false, $required = false){
		return $this->param(($required ? "+" : null), self::MOTHER_GENDER, $value, $exact);
	}

	/**
	 * Creates a mother birth date search parameter with the search value.
	 *
	 * @param      $value
	 * @param bool $exact
	 * @param bool $required
	 *
	 * @return \Gedcomx\Rs\Client\Util\GedcomxPersonSearchQueryBuilder
	 */
	public function motherBirthDate($value, $exact = false, $required = false){
		return $this->param(($required ? "+" : null), self::MOTHER_BIRTH_DATE, $value, $exact);
	}

	/**
	 * Creates a mother birth place search parameter with the search value.
	 *
	 * @param      $value
	 * @param bool $exact
	 * @param bool $required
	 *
	 * @return \Gedcomx\Rs\Client\Util\GedcomxPersonSearchQueryBuilder
	 */
	public function motherBirthPlace($value, $exact = false, $required = false){
		return $this->param(($required ? "+" : null), self::MOTHER_BIRTH_PLACE, $value, $exact);
	}

	/**
	 * Creates a mother death date search parameter with the search value.
	 *
	 * @param      $value
	 * @param bool $exact
	 * @param bool $required
	 *
	 * @return \Gedcomx\Rs\Client\Util\GedcomxPersonSearchQueryBuilder
	 */
	public function motherDeathDate($value, $exact = false, $required = false){
		return $this->param(($required ? "+" : null), self::MOTHER_DEATH_DATE, $value, $exact);
	}

	/**
	 * Creates a mother death place search parameter with the search value.
	 *
	 * @param      $value
	 * @param bool $exact
	 * @param bool $required
	 *
	 * @return \Gedcomx\Rs\Client\Util\GedcomxPersonSearchQueryBuilder
	 */
	public function motherDeathPlace($value, $exact = false, $required = false){
		return $this->param(($required ? "+" : null), self::MOTHER_DEATH_PLACE, $value, $exact);
	}

	/**
	 * Creates a mother marriage date search parameter with the search value.
	 *
	 * @param      $value
	 * @param bool $exact
	 * @param bool $required
	 *
	 * @return \Gedcomx\Rs\Client\Util\GedcomxPersonSearchQueryBuilder
	 */
	public function motherMarriageDate($value, $exact = false, $required = false){
		return $this->param(($required ? "+" : null), self::MOTHER_MARRIAGE_DATE, $value, $exact);
	}

	/**
	 * Creates a mother marriage place search parameter with the search value.
	 *
	 * @param      $value
	 * @param bool $exact
	 * @param bool $required
	 *
	 * @return \Gedcomx\Rs\Client\Util\GedcomxPersonSearchQueryBuilder
	 */
	public function motherMarriagePlace($value, $exact = false, $required = false){
		return $this->param(($required ? "+" : null), self::MOTHER_MARRIAGE_PLACE, $value, $exact);
	}

	/**
	 * Creates a spouse name search parameter with the search value.
	 *
	 * @param      $value
	 * @param bool $exact
	 * @param bool $required
	 *
	 * @return \Gedcomx\Rs\Client\Util\GedcomxPersonSearchQueryBuilder
	 */
	public function spouseName($value, $exact = false, $required = false){
		return $this->param(($required ? "+" : null), self::SPOUSE_NAME, $value, $exact);
	}

	/**
	 * Creates a spouse given name search parameter with the search value.
	 *
	 * @param      $value
	 * @param bool $exact
	 * @param bool $required
	 *
	 * @return \Gedcomx\Rs\Client\Util\GedcomxPersonSearchQueryBuilder
	 */
	public function spouseGivenName($value, $exact = false, $required = false){
		return $this->param(($required ? "+" : null), self::SPOUSE_GIVEN_NAME, $value, $exact);
	}

	/**
	 * Creates a spouse surname search parameter with the search value.
	 *
	 * @param      $value
	 * @param bool $exact
	 * @param bool $required
	 *
	 * @return \Gedcomx\Rs\Client\Util\GedcomxPersonSearchQueryBuilder
	 */
	public function spouseSurname($value, $exact = false, $required = false){
		return $this->param(($required ? "+" : null), self::SPOUSE_SURNAME, $value, $exact);
	}

	/**
	 * Creates a spouse gender search parameter with the search value.
	 *
	 * @param      $value
	 * @param bool $exact
	 * @param bool $required
	 *
	 * @return \Gedcomx\Rs\Client\Util\GedcomxPersonSearchQueryBuilder
	 */
	public function spouseGender($value, $exact = false, $required = false){
		return $this->param(($required ? "+" : null), self::SPOUSE_GENDER, $value, $exact);
	}

	/**
	 * Creates a spouse birth date search parameter with the search value.
	 *
	 * @param      $value
	 * @param bool $exact
	 * @param bool $required
	 *
	 * @return \Gedcomx\Rs\Client\Util\GedcomxPersonSearchQueryBuilder
	 */
	public function spouseBirthDate($value, $exact = false, $required = false){
		return $this->param(($required ? "+" : null), self::SPOUSE_BIRTH_DATE, $value, $exact);
	}

	/**
	 * Creates a spouse birth place search parameter with the search value.
	 *
	 * @param      $value
	 * @param bool $exact
	 * @param bool $required
	 *
	 * @return \Gedcomx\Rs\Client\Util\GedcomxPersonSearchQueryBuilder
	 */
	public function spouseBirthPlace($value, $exact = false, $required = false){
		return $this->param(($required ? "+" : null), self::SPOUSE_BIRTH_PLACE, $value, $exact);
	}

	/**
	 * Creates a spouse death date search parameter with the search value.
	 *
	 * @param      $value
	 * @param bool $exact
	 * @param bool $required
	 *
	 * @return \Gedcomx\Rs\Client\Util\GedcomxPersonSearchQueryBuilder
	 */
	public function spouseDeathDate($value, $exact = false, $required = false){
		return $this->param(($required ? "+" : null), self::SPOUSE_DEATH_DATE, $value, $exact);
	}

	/**
	 * Creates a spouse death place search parameter with the search value.
	 *
	 * @param      $value
	 * @param bool $exact
	 * @param bool $required
	 *
	 * @return \Gedcomx\Rs\Client\Util\GedcomxPersonSearchQueryBuilder
	 */
	public function spouseDeathPlace($value, $exact = false, $required = false){
		return $this->param(($required ? "+" : null), self::SPOUSE_DEATH_PLACE, $value, $exact);
	}

	/**
	 * Creates a spouse marriage date search parameter with the search value.
	 *
	 * @param      $value
	 * @param bool $exact
	 * @param bool $required
	 *
	 * @return \Gedcomx\Rs\Client\Util\GedcomxPersonSearchQueryBuilder
	 */
	public function spouseMarriageDate($value, $exact = false, $required = false){
		return $this->param(($required ? "+" : null), self::SPOUSE_MARRIAGE_DATE, $value, $exact);
	}

	/**
	 * Creates a spouse marriage place search parameter with the search value.
	 *
	 * @param      $value
	 * @param bool $exact
	 * @param bool $required
	 *
	 * @return \Gedcomx\Rs\Client\Util\GedcomxPersonSearchQueryBuilder
	 */
	public function spouseMarriagePlace($value, $exact = false, $required = false){
		return $this->param(($required ? "+" : null), self::SPOUSE_MARRIAGE_PLACE, $value, $exact);
	}

	/**
	 * Creates a parent name search parameter with the search value.
	 *
	 * @param      $value
	 * @param bool $exact
	 * @param bool $required
	 *
	 * @return \Gedcomx\Rs\Client\Util\GedcomxPersonSearchQueryBuilder
	 */
	public function parentName($value, $exact = false, $required = false){
		return $this->param(($required ? "+" : null), self::PARENT_NAME, $value, $exact);
	}

	/**
	 * Creates a parent given name search parameter with the search value.
	 *
	 * @param      $value
	 * @param bool $exact
	 * @param bool $required
	 *
	 * @return \Gedcomx\Rs\Client\Util\GedcomxPersonSearchQueryBuilder
	 */
	public function parentGivenName($value, $exact = false, $required = false){
		return $this->param(($required ? "+" : null), self::PARENT_GIVEN_NAME, $value, $exact);
	}

	/**
	 * Creates a parent surname search parameter with the search value.
	 *
	 * @param      $value
	 * @param bool $exact
	 * @param bool $required
	 *
	 * @return \Gedcomx\Rs\Client\Util\GedcomxPersonSearchQueryBuilder
	 */
	public function parentSurname($value, $exact = false, $required = false){
		return $this->param(($required ? "+" : null), self::PARENT_SURNAME, $value, $exact);
	}

	/**
	 * Creates a parent gender search parameter with the search value.
	 *
	 * @param      $value
	 * @param bool $exact
	 * @param bool $required
	 *
	 * @return \Gedcomx\Rs\Client\Util\GedcomxPersonSearchQueryBuilder
	 */
	public function parentGender($value, $exact = false, $required = false){
		return $this->param(($required ? "+" : null), self::PARENT_GENDER, $value, $exact);
	}

	/**
	 * Creates a parent birth date search parameter with the search value.
	 *
	 * @param      $value
	 * @param bool $exact
	 * @param bool $required
	 *
	 * @return \Gedcomx\Rs\Client\Util\GedcomxPersonSearchQueryBuilder
	 */
	public function parentBirthDate($value, $exact = false, $required = false){
		return $this->param(($required ? "+" : null), self::PARENT_BIRTH_DATE, $value, $exact);
	}

	/**
	 * Creates a parent birth place search parameter with the search value.
	 *
	 * @param      $value
	 * @param bool $exact
	 * @param bool $required
	 *
	 * @return \Gedcomx\Rs\Client\Util\GedcomxPersonSearchQueryBuilder
	 */
	public function parentBirthPlace($value, $exact = false, $required = false){
		return $this->param(($required ? "+" : null), self::PARENT_BIRTH_PLACE, $value, $exact);
	}

	/**
	 * Creates a parent death date search parameter with the search value.
	 *
	 * @param      $value
	 * @param bool $exact
	 * @param bool $required
	 *
	 * @return \Gedcomx\Rs\Client\Util\GedcomxPersonSearchQueryBuilder
	 */
	public function parentDeathDate($value, $exact = false, $required = false){
		return $this->param(($required ? "+" : null), self::PARENT_DEATH_DATE, $value, $exact);
	}

	/**
	 * Creates a parent death place search parameter with the search value.
	 *
	 * @param      $value
	 * @param bool $exact
	 * @param bool $required
	 *
	 * @return \Gedcomx\Rs\Client\Util\GedcomxPersonSearchQueryBuilder
	 */
	public function parentDeathPlace($value, $exact = false, $required = false){
		return $this->param(($required ? "+" : null), self::PARENT_DEATH_PLACE, $value, $exact);
	}

	/**
	 * Creates a parent marriage date search parameter with the search value.
	 *
	 * @param      $value
	 * @param bool $exact
	 * @param bool $required
	 *
	 * @return \Gedcomx\Rs\Client\Util\GedcomxPersonSearchQueryBuilder
	 */
	public function parentMarriageDate($value, $exact = false, $required = false){
		return $this->param(($required ? "+" : null), self::PARENT_MARRIAGE_DATE, $value, $exact);
	}

	/**
	 * Creates a parent marriage place search parameter with the search value.
	 *
	 * @param      $value
	 * @param bool $exact
	 * @param bool $required
	 *
	 * @return \Gedcomx\Rs\Client\Util\GedcomxPersonSearchQueryBuilder
	 */
	public function parentMarriagePlace($value, $exact = false, $required = false){
		return $this->param(($required ? "+" : null), self::PARENT_MARRIAGE_PLACE, $value, $exact);
	}
}