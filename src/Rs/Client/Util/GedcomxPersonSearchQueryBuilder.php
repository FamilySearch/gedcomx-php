<?php


namespace Gedcomx\Rs\Client\Util;


class GedcomxPersonSearchQueryBuilder extends GedcomxBaseSearchQueryBuilder{

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

	private function param( $prefix, $name, $value, $exact) {
		$this->parameters[] = new SearchParameter($prefix, $name, $value, $exact);
		return $this;
	}

	public function name($value, $exact = false, $required = false){
		return $this->param(($required ? "+" : null), self::NAME, $value, $exact);
	}

	public function givenName($value, $exact = false, $required = false){
		return $this->param(($required ? "+" : null), self::GIVEN_NAME, $value, $exact);
	}

	public function surname ($value, $exact = false, $required = false){
		return $this->param(($required ? "+" : null), self::SURNAME, $value, $exact);
	}

	public function gender($value, $exact = false, $required = false){
		return $this->param(($required ? "+" : null), self::GENDER, $value, $exact);
	}

	public function birthDate($value, $exact = false, $required = false){
		return $this->param(($required ? "+" : null), self::BIRTH_DATE, $value, $exact);
	}

	public function birthPlace ($value, $exact = false, $required = false){
		return $this->param(($required ? "+" : null), self::BIRTH_PLACE, $value, $exact);
	}

	public function deathDate($value, $exact = false, $required = false){
		return $this->param(($required ? "+" : null), self::DEATH_DATE, $value, $exact);
	}

	public function deathPlace($value, $exact = false, $required = false){
		return $this->param(($required ? "+" : null), self::DEATH_PLACE, $value, $exact);
	}

	public function marriageDate($value, $exact = false, $required = false){
		return $this->param(($required ? "+" : null), self::MARRIAGE_DATE, $value, $exact);
	}

	public function marriagePlace($value, $exact = false, $required = false){
		return $this->param(($required ? "+" : null), self::MARRIAGE_PLACE, $value, $exact);
	}

	public function fatherName($value, $exact = false, $required = false){
		return $this->param(($required ? "+" : null), self::FATHER_NAME, $value, $exact);
	}

	public function fatherGivenName($value, $exact = false, $required = false){
		return $this->param(($required ? "+" : null), self::FATHER_GIVEN_NAME, $value, $exact);
	}

	public function fatherSurname($value, $exact = false, $required = false){
		return $this->param(($required ? "+" : null), self::FATHER_SURNAME, $value, $exact);
	}

	public function fatherGender($value, $exact = false, $required = false){
		return $this->param(($required ? "+" : null), self::FATHER_GENDER, $value, $exact);
	}

	public function fatherBirthDate($value, $exact = false, $required = false){
		return $this->param(($required ? "+" : null), self::FATHER_BIRTH_DATE, $value, $exact);
	}

	public function fatherBirthPlace($value, $exact = false, $required = false){
		return $this->param(($required ? "+" : null), self::FATHER_BIRTH_PLACE, $value, $exact);
	}

	public function fatherDeathDate($value, $exact = false, $required = false){
		return $this->param(($required ? "+" : null), self::FATHER_DEATH_DATE, $value, $exact);
	}

	public function fatherDeathPlace($value, $exact = false, $required = false){
		return $this->param(($required ? "+" : null), self::FATHER_DEATH_PLACE, $value, $exact);
	}

	public function fatherMarriageDate($value, $exact = false, $required = false){
		return $this->param(($required ? "+" : null), self::FATHER_MARRIAGE_DATE, $value, $exact);
	}

	public function fatherMarriagePlace($value, $exact = false, $required = false){
		return $this->param(($required ? "+" : null), self::FATHER_MARRIAGE_PLACE, $value, $exact);
	}

	public function motherName($value, $exact = false, $required = false){
		return $this->param(($required ? "+" : null), self::MOTHER_NAME, $value, $exact);
	}

	public function motherGivenName($value, $exact = false, $required = false){
		return $this->param(($required ? "+" : null), self::MOTHER_GIVEN_NAME, $value, $exact);
	}

	public function motherSurname($value, $exact = false, $required = false){
		return $this->param(($required ? "+" : null), self::MOTHER_SURNAME, $value, $exact);
	}

	public function motherGender($value, $exact = false, $required = false){
		return $this->param(($required ? "+" : null), self::MOTHER_GENDER, $value, $exact);
	}

	public function motherBirthDate($value, $exact = false, $required = false){
		return $this->param(($required ? "+" : null), self::MOTHER_BIRTH_DATE, $value, $exact);
	}

	public function motherBirthPlace($value, $exact = false, $required = false){
		return $this->param(($required ? "+" : null), self::MOTHER_BIRTH_PLACE, $value, $exact);
	}

	public function motherDeathDate($value, $exact = false, $required = false){
		return $this->param(($required ? "+" : null), self::MOTHER_DEATH_DATE, $value, $exact);
	}

	public function motherDeathPlace($value, $exact = false, $required = false){
		return $this->param(($required ? "+" : null), self::MOTHER_DEATH_PLACE, $value, $exact);
	}

	public function motherMarriageDate($value, $exact = false, $required = false){
		return $this->param(($required ? "+" : null), self::MOTHER_MARRIAGE_DATE, $value, $exact);
	}

	public function motherMarriagePlace($value, $exact = false, $required = false){
		return $this->param(($required ? "+" : null), self::MOTHER_MARRIAGE_PLACE, $value, $exact);
	}

	public function spouseName($value, $exact = false, $required = false){
		return $this->param(($required ? "+" : null), self::SPOUSE_NAME, $value, $exact);
	}

	public function spouseGivenName($value, $exact = false, $required = false){
		return $this->param(($required ? "+" : null), self::SPOUSE_GIVEN_NAME, $value, $exact);
	}

	public function spouseSurname($value, $exact = false, $required = false){
		return $this->param(($required ? "+" : null), self::SPOUSE_SURNAME, $value, $exact);
	}

	public function spouseGender($value, $exact = false, $required = false){
		return $this->param(($required ? "+" : null), self::SPOUSE_GENDER, $value, $exact);
	}

	public function spouseBirthDate($value, $exact = false, $required = false){
		return $this->param(($required ? "+" : null), self::SPOUSE_BIRTH_DATE, $value, $exact);
	}

	public function spouseBirthPlace($value, $exact = false, $required = false){
		return $this->param(($required ? "+" : null), self::SPOUSE_BIRTH_PLACE, $value, $exact);
	}

	public function spouseDeathDate($value, $exact = false, $required = false){
		return $this->param(($required ? "+" : null), self::SPOUSE_DEATH_DATE, $value, $exact);
	}

	public function spouseDeathPlace($value, $exact = false, $required = false){
		return $this->param(($required ? "+" : null), self::SPOUSE_DEATH_PLACE, $value, $exact);
	}

	public function spouseMarriageDate($value, $exact = false, $required = false){
		return $this->param(($required ? "+" : null), self::SPOUSE_MARRIAGE_DATE, $value, $exact);
	}

	public function spouseMarriagePlace($value, $exact = false, $required = false){
		return $this->param(($required ? "+" : null), self::SPOUSE_MARRIAGE_PLACE, $value, $exact);
	}

	public function parentName($value, $exact = false, $required = false){
		return $this->param(($required ? "+" : null), self::PARENT_NAME, $value, $exact);
	}

	public function parentGivenName($value, $exact = false, $required = false){
		return $this->param(($required ? "+" : null), self::PARENT_GIVEN_NAME, $value, $exact);
	}

	public function parentSurname($value, $exact = false, $required = false){
		return $this->param(($required ? "+" : null), self::PARENT_SURNAME, $value, $exact);
	}

	public function parentGender($value, $exact = false, $required = false){
		return $this->param(($required ? "+" : null), self::PARENT_GENDER, $value, $exact);
	}

	public function parentBirthDate($value, $exact = false, $required = false){
		return $this->param(($required ? "+" : null), self::PARENT_BIRTH_DATE, $value, $exact);
	}

	public function parentBirthPlace($value, $exact = false, $required = false){
		return $this->param(($required ? "+" : null), self::PARENT_BIRTH_PLACE, $value, $exact);
	}

	public function parentDeathDate($value, $exact = false, $required = false){
		return $this->param(($required ? "+" : null), self::PARENT_DEATH_DATE, $value, $exact);
	}

	public function parentDeathPlace($value, $exact = false, $required = false){
		return $this->param(($required ? "+" : null), self::PARENT_DEATH_PLACE, $value, $exact);
	}

	public function parentMarriageDate($value, $exact = false, $required = false){
		return $this->param(($required ? "+" : null), self::PARENT_MARRIAGE_DATE, $value, $exact);
	}

	public function parentMarriagePlace($value, $exact = false, $required = false){
		return $this->param(($required ? "+" : null), self::PARENT_MARRIAGE_PLACE, $value, $exact);
	}

}