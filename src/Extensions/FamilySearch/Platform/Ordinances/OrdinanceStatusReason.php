<?php
/**
 * Copyright Intellectual Reserve, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Gedcomx\Extensions\FamilySearch\Platform\Ordinances;

/**
 * Enumeration of reasons why an ordinance might have a particular status.
 *
 * These reasons provide detailed explanations for why an ordinance cannot be reserved,
 * is not needed, or has other specific status conditions.
 *
 * @package Gedcomx\Extensions\FamilySearch\Platform\Ordinances
 */
class OrdinanceStatusReason
{
    /**
     * The "sealing to parent" ordinance is not needed when an individual is "born in the covenant".
     */
    const BORN_IN_COVENANT = "http://familysearch.org/v1/BornInCovenant";

    /**
     * The "sealing to spouse" ordinance requires a couple relationship between the couple.
     */
    const COUPLE_RELATIONSHIP_MISSING = "http://familysearch.org/v1/CoupleRelationshipMissing";

    /**
     * The person's death date cannot be before his/her birthdate.
     */
    const DEATH_BEFORE_BIRTH = "http://familysearch.org/v1/DeathBeforeBirth";

    /**
     * Baptism, Confirmation, Initiatory, Endowment, and Sealing to Spouse ordinances are not needed if a person died before the age of eight.
     */
    const DIED_BEFORE_AGE_EIGHT = "http://familysearch.org/v1/DiedBeforeAgeEight";

    /**
     * There's a death or burial date that cannot be standardized. Only applied when a person was born recently enough (110 years) that the death date should be findable and able to be standardized.
     */
    const DEATH_DATE_REFORMAT_NEEDED = "http://familysearch.org/v1/DeathDateReformatNeeded";

    /**
     * A person's given name(s) cannot contain one or more invalid words or be represented by a traditional prefix such as Mr., Miss, Mrs., etc.
     */
    const INVALID_GIVEN_NAME_PIECE = "http://familysearch.org/v1/InvalidGivenNamePiece";

    /**
     * This person's name contains one or more invalid words.
     */
    const INVALID_NAME = "http://familysearch.org/v1/InvalidName";

    /**
     * This person's name contains an invalid name prefix.
     */
    const INVALID_NAME_PREFIX = "http://familysearch.org/v1/InvalidNamePrefix";

    /**
     * This person's name contains an invalid name suffix.
     */
    const INVALID_NAME_SUFFIX = "http://familysearch.org/v1/InvalidNameSuffix";

    /**
     * A person's name cannot include descriptors such as Child, Baby, Son, Daughter, Sister, Brother, Aunt, Uncle, etc.
     */
    const INVALID_SINGLE_NAME_PIECE = "http://familysearch.org/v1/InvalidSingleNamePiece";

    /**
     * A person's name cannot include invalid characters.
     */
    const INVALID_SPECIAL_CHARACTER_NAME = "http://familysearch.org/v1/InvalidSpecialCharacterName";

    /**
     * A person's surname contains invalid words or characters. A person's surname cannot be a descriptor such as nephew, niece, spouse, twin, etc.
     */
    const INVALID_SURNAME = "http://familysearch.org/v1/InvalidSurname";

    /**
     * A person has a title but no given name. A male person with a title and surname name must have a given name.
     */
    const INVALID_TITLE_GIVEN_MISSING = "http://familysearch.org/v1/InvalidTitleGivenMissing";

    /**
     * The person's marriage fact date occurs before the person is eight years old.
     */
    const MARRIED_TOO_YOUNG = "http://familysearch.org/v1/MarriedTooYoung";

    /**
     * A person who lived before A.D. 1500.
     * @deprecated Replaced by RESTRICTED_DATE
     */
    const MEDIEVAL = "http://familysearch.org/v1/Medieval";

    /**
     * A person must have a standardized date reference.
     */
    const MISSING_STANDARDIZED_DATE = "http://familysearch.org/v1/MissingStandardizedDate";

    /**
     * A person must have a standardized place reference.
     */
    const MISSING_STANDARDIZED_PLACE = "http://familysearch.org/v1/MissingStandardizedPlace";

    /**
     * A person's surname cannot contain the word "Mister". If the person's surname is Mister, then it should be the only word in the last name.
     */
    const MISTER_AS_ONLY_SURNAME = "http://familysearch.org/v1/MisterAsOnlySurname";

    /**
     * A person cannot have a name that is only initials.
     */
    const NAME_CONTAINS_ONLY_INITIALS = "http://familysearch.org/v1/NameContainsOnlyInitials";

    /**
     * One or more characters in the name do not match the designated language script of the name.
     */
    const NAME_LANG_SCRIPT_MISMATCH = "http://familysearch.org/v1/NameLangScriptMismatch";

    /**
     * The language script for the name is undefined and the name contains multiple scripts.
     */
    const NAME_LANG_UND_WITH_MULTIPLE_SCRIPTS = "http://familysearch.org/v1/NameLangUndWithMultipleScripts";

    /**
     * A person's full name (any name form) cannot be more than 255 characters.
     */
    const NAME_TOO_LONG = "http://familysearch.org/v1/NameTooLong";

    /**
     * This ordinance is for a person born too recently and the current user is not an immediate relative.
     */
    const NEED_PERMISSION = "http://familysearch.org/v1/NeedPermission";

    /**
     * A person's record needs a given name or surname.
     */
    const NO_NAME = "http://familysearch.org/v1/NoName";

    /**
     * A person has been declared not accountable.
     */
    const NOT_ACCOUNTABLE = "http://familysearch.org/v1/NotAccountable";

    /**
     * A person's ordinance status is not available. Please contact FamilySearch Support if you are a direct descendant and need more information.
     */
    const NOT_AVAILABLE = "http://familysearch.org/v1/NotAvailable";

    /**
     * A person has not been deceased for one year.
     * @deprecated Replaced by TOO_RECENTLY_DECEASED
     */
    const NOT_DEAD_AT_LEAST_ONE_YEAR = "http://familysearch.org/v1/NotDeadAtLeastOneYear";

    /**
     * A person must have enough event or relationship information, such as a birthdate and place, a death date and place, etc. A person's record must have enough date or place information for the system to be able to determine whether the ordinance is already done.
     */
    const NOT_MATCHABLE_USING_EVENTS = "http://familysearch.org/v1/NotMatchableUsingEvents";

    /**
     * A person must have has enough event or relationship information. A person's record must have enough relationship information for the system to be able to determine whether the ordinance is already done.
     */
    const NOT_MATCHABLE_USING_RELATIONSHIPS = "http://familysearch.org/v1/NotMatchableUsingRelationships";

    /**
     * A member must be related to a person to reserve ordinances for that person.
     */
    const NOT_RELATED_TO_MEMBER = "http://familysearch.org/v1/NotRelatedToMember";

    /**
     * The ordinance must be a valid temple ordinance. The Family Tree cannot be used to reserve ordinances of this type.
     */
    const NOT_TEMPLE_ORDINANCE = "http://familysearch.org/v1/NotTempleOrdinance";

    /**
     * This person is still listed as living.
     */
    const OFFICIAL_COMPLETED_ORDINANCE_FOR_LIVING = "http://familysearch.org/v1/OfficialCompletedOrdinanceForLiving";

    /**
     * This is an official completed ordinance.
     */
    const OFFICIAL_COMPLETED_ORDINANCE = "http://familysearch.org/v1/OfficialCompletedOrdinance";

    /**
     * Latin surnames cannot consist of only one letter.
     */
    const ONE_LATIN_LETTER_SURNAME = "http://familysearch.org/v1/OneLatinLetterSurname";

    /**
     * One name per script type.
     */
    const ONE_NAME_PER_SCRIPT_TYPE = "http://familysearch.org/v1/OneNamePerScriptType";

    /**
     * A "sealing to parent" ordinance requires the person to have both child-to-father and child-to-mother relationships.
     */
    const PARENT_RELATIONSHIP_MISSING = "http://familysearch.org/v1/ParentRelationshipMissing";

    /**
     * A "sealing to parent" ordinance requires the father and mother to have a couple relationship.
     */
    const PARENTS_COUPLE_RELATIONSHIP_MISSING = "http://familysearch.org/v1/ParentsCoupleRelationshipMissing";

    /**
     * A person's name cannot contain repeated punctuation characters, such as .., --, etc.
     */
    const REPEATING_SPECIAL_CHARACTER_NAME = "http://familysearch.org/v1/RepeatingSpecialCharacterName";

    /**
     * The ordinance is reserved.
     */
    const RESERVED = "http://familysearch.org/v1/Reserved";

    /**
     * A person who lived before 200 A.D.
     */
    const RESTRICTED_DATE = "http://familysearch.org/v1/RestrictedDate";

    /**
     * A sealing to spouse must involve a husband and a wife. A sealing to parents must involve a father and a mother.
     */
    const SAME_SEX = "http://familysearch.org/v1/SameSex";

    /**
     * A person cannot be sealed to themselves.
     */
    const SEALING_TO_SELF = "http://familysearch.org/v1/SealingToSelf";

    /**
     * A stillborn person does not need ordinances.
     */
    const STILLBORN = "http://familysearch.org/v1/Stillborn";

    /**
     * The person has not been deceased long enough to qualify for temple ordinances.
     */
    const TOO_RECENTLY_DECEASED = "http://familysearch.org/v1/TooRecentlyDeceased";

    /**
     * A person must have a known gender, male or female.
     */
    const UNKNOWN_GENDER = "http://familysearch.org/v1/UnknownGender";

    /**
     * The person is in a tree that does not allow ordinances to be performed.
     */
    const UNSUPPORTED_TREE = "http://familysearch.org/v1/UnsupportedTree";

    /**
     * Unknown or unrecognized status reason.
     */
    const OTHER = "http://familysearch.org/v1/OTHER";
}
