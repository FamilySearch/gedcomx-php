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

namespace Gedcomx\Extensions\FamilySearch\Platform\Tree;

/**
 * Enumeration of change types in the FamilySearch Family Tree.
 *
 * These constants represent the various types of changes that can occur in the
 * FamilySearch Family Tree, including person operations, relationship operations,
 * fact changes, name changes, source attachments, and more.
 *
 * Each change type combines an operation (Create, Update, Delete, Merge, etc.)
 * with an object type (Person, Name, Fact, etc.) and optionally a modifier
 * indicating the context (Person, Couple, ChildAndParentsRelationship).
 *
 * @package Gedcomx\Extensions\FamilySearch\Platform\Tree
 */
class ChangeType
{
    // Person Operations
    const CREATE_PERSON = "http://familysearch.org/v1/CreatePerson";
    const DELETE_PERSON = "http://familysearch.org/v1/DeletePerson";
    const MERGE_PERSON = "http://familysearch.org/v1/MergePerson";
    const UNMERGE_PERSON = "http://familysearch.org/v1/UnmergePerson";
    const UNTOMBSTONE_PERSON = "http://familysearch.org/v1/UntombstonePerson";
    const ADD_PERSON_NOT_A_MATCH = "http://familysearch.org/v1/AddPersonNotAMatch";
    const REMOVE_PERSON_NOT_A_MATCH = "http://familysearch.org/v1/RemovePersonNotAMatch";

    // Couple Relationship Operations
    const CREATE_COUPLE_RELATIONSHIP = "http://familysearch.org/v1/CreateCoupleRelationship";
    const UPDATE_COUPLE_RELATIONSHIP = "http://familysearch.org/v1/UpdateCoupleRelationship";
    const DELETE_COUPLE_RELATIONSHIP = "http://familysearch.org/v1/DeleteCoupleRelationship";
    const ADD_SPOUSE1 = "http://familysearch.org/v1/AddSpouse1";
    const EDIT_SPOUSE1 = "http://familysearch.org/v1/EditSpouse1";
    const ADD_SPOUSE2 = "http://familysearch.org/v1/AddSpouse2";
    const EDIT_SPOUSE2 = "http://familysearch.org/v1/EditSpouse2";
    const MERGE_COUPLE_RELATIONSHIP = "http://familysearch.org/v1/MergeCoupleRelationship";
    const UNMERGE_COUPLE_RELATIONSHIP = "http://familysearch.org/v1/UnmergeCoupleRelationship";
    const UNTOMBSTONE_COUPLE_RELATIONSHIP = "http://familysearch.org/v1/UntombstoneCoupleRelationship";

    // Child and Parents Relationship Operations
    const CREATE_CHILD_AND_PARENTS_RELATIONSHIP = "http://familysearch.org/v1/CreateChildAndParentsRelationship";
    const UPDATE_CHILD_AND_PARENTS_RELATIONSHIP = "http://familysearch.org/v1/UpdateChildAndParentsRelationship";
    const DELETE_CHILD_AND_PARENTS_RELATIONSHIP = "http://familysearch.org/v1/DeleteChildAndParentsRelationship";
    const ADD_PARENT1 = "http://familysearch.org/v1/AddParent1";
    const EDIT_PARENT1 = "http://familysearch.org/v1/EditParent1";
    const REMOVE_PARENT1 = "http://familysearch.org/v1/RemoveParent1";
    const ADD_PARENT2 = "http://familysearch.org/v1/AddParent2";
    const EDIT_PARENT2 = "http://familysearch.org/v1/EditParent2";
    const REMOVE_PARENT2 = "http://familysearch.org/v1/RemoveParent2";
    const ADD_CHILD = "http://familysearch.org/v1/AddChild";
    const EDIT_CHILD = "http://familysearch.org/v1/EditChild";
    const MERGE_CHILD_AND_PARENTS_RELATIONSHIP = "http://familysearch.org/v1/MergeChildAndParentsRelationship";
    const UNMERGE_CHILD_AND_PARENTS_RELATIONSHIP = "http://familysearch.org/v1/UnmergeChildAndParentsRelationship";
    const UNTOMBSTONE_CHILD_AND_PARENTS_RELATIONSHIP = "http://familysearch.org/v1/UntombstoneChildAndParentsRelationship";

    // Source Reference Changes
    const ADD_PERSON_SOURCE_REFERENCE = "http://familysearch.org/v1/AddPersonSourceReference";
    const EDIT_PERSON_SOURCE_REFERENCE = "http://familysearch.org/v1/EditPersonSourceReference";
    const DELETE_PERSON_SOURCE_REFERENCE = "http://familysearch.org/v1/DeletePersonSourceReference";
    const ADD_COUPLE_SOURCE_REFERENCE = "http://familysearch.org/v1/AddCoupleSourceReference";
    const EDIT_COUPLE_SOURCE_REFERENCE = "http://familysearch.org/v1/EditCoupleSourceReference";
    const DELETE_COUPLE_SOURCE_REFERENCE = "http://familysearch.org/v1/DeleteCoupleSourceReference";
    const ADD_CHILD_PARENTS_SOURCE_REFERENCE = "http://familysearch.org/v1/AddChildParentsSourceReference";
    const EDIT_CHILD_PARENTS_SOURCE_REFERENCE = "http://familysearch.org/v1/EditChildParentsSourceReference";
    const DELETE_CHILD_PARENTS_SOURCE_REFERENCE = "http://familysearch.org/v1/DeleteChildParentsSourceReference";

    // Discussion Reference Changes
    const ADD_PERSON_DISCUSSION_REFERENCE = "http://familysearch.org/v1/AddPersonDiscussionReference";
    const EDIT_PERSON_DISCUSSION_REFERENCE = "http://familysearch.org/v1/EditPersonDiscussionReference";
    const DELETE_PERSON_DISCUSSION_REFERENCE = "http://familysearch.org/v1/DeletePersonDiscussionReference";
    const ADD_COUPLE_DISCUSSION_REFERENCE = "http://familysearch.org/v1/AddCoupleDiscussionReference";
    const EDIT_COUPLE_DISCUSSION_REFERENCE = "http://familysearch.org/v1/EditCoupleDiscussionReference";
    const DELETE_COUPLE_DISCUSSION_REFERENCE = "http://familysearch.org/v1/DeleteCoupleDiscussionReference";
    const ADD_CHILD_PARENTS_DISCUSSION_REFERENCE = "http://familysearch.org/v1/AddChildParentsDiscussionReference";
    const EDIT_CHILD_PARENTS_DISCUSSION_REFERENCE = "http://familysearch.org/v1/EditChildParentsDiscussionReference";
    const DELETE_CHILD_PARENTS_DISCUSSION_REFERENCE = "http://familysearch.org/v1/DeleteChildParentsDiscussionReference";

    // Tree Person Reference Changes
    const ADD_TREE_PERSON_REFERENCE = "http://familysearch.org/v1/AddTreePersonReference";
    const EDIT_TREE_PERSON_REFERENCE = "http://familysearch.org/v1/EditTreePersonReference";
    const DELETE_TREE_PERSON_REFERENCE = "http://familysearch.org/v1/DeleteTreePersonReference";

    // Evidence Reference Changes
    const ADD_PERSON_EVIDENCE_REFERENCE = "http://familysearch.org/v1/AddPersonEvidenceReference";
    const EDIT_PERSON_EVIDENCE_REFERENCE = "http://familysearch.org/v1/EditPersonEvidenceReference";
    const DELETE_PERSON_EVIDENCE_REFERENCE = "http://familysearch.org/v1/DeletePersonEvidenceReference";
    const ADD_COUPLE_EVIDENCE_REFERENCE = "http://familysearch.org/v1/AddCoupleEvidenceReference";
    const EDIT_COUPLE_EVIDENCE_REFERENCE = "http://familysearch.org/v1/EditCoupleEvidenceReference";
    const DELETE_COUPLE_EVIDENCE_REFERENCE = "http://familysearch.org/v1/DeleteCoupleEvidenceReference";
    const ADD_CHILD_PARENTS_EVIDENCE_REFERENCE = "http://familysearch.org/v1/AddChildParentsEvidenceReference";
    const EDIT_CHILD_PARENTS_EVIDENCE_REFERENCE = "http://familysearch.org/v1/EditChildParentsEvidenceReference";
    const DELETE_CHILD_PARENTS_EVIDENCE_REFERENCE = "http://familysearch.org/v1/DeleteChildParentsEvidenceReference";

    // Person Event/Fact Changes
    const ADD_AFFILIATION = "http://familysearch.org/v1/AddAffiliation";
    const EDIT_AFFILIATION = "http://familysearch.org/v1/EditAffiliation";
    const DELETE_AFFILIATION = "http://familysearch.org/v1/DeleteAffiliation";
    const ADD_BAR_MITZVAH = "http://familysearch.org/v1/AddBarMitzvah";
    const EDIT_BAR_MITZVAH = "http://familysearch.org/v1/EditBarMitzvah";
    const DELETE_BAR_MITZVAH = "http://familysearch.org/v1/DeleteBarMitzvah";
    const ADD_BAS_MITZVAH = "http://familysearch.org/v1/AddBasMitzvah";
    const EDIT_BAS_MITZVAH = "http://familysearch.org/v1/EditBasMitzvah";
    const DELETE_BAS_MITZVAH = "http://familysearch.org/v1/DeleteBasMitzvah";
    const ADD_BIRTH = "http://familysearch.org/v1/AddBirth";
    const EDIT_BIRTH = "http://familysearch.org/v1/EditBirth";
    const DELETE_BIRTH = "http://familysearch.org/v1/DeleteBirth";
    const ADD_BURIAL = "http://familysearch.org/v1/AddBurial";
    const EDIT_BURIAL = "http://familysearch.org/v1/EditBurial";
    const DELETE_BURIAL = "http://familysearch.org/v1/DeleteBurial";
    const ADD_CHRISTENING = "http://familysearch.org/v1/AddChristening";
    const EDIT_CHRISTENING = "http://familysearch.org/v1/EditChristening";
    const DELETE_CHRISTENING = "http://familysearch.org/v1/DeleteChristening";
    const ADD_CREMATION = "http://familysearch.org/v1/AddCremation";
    const EDIT_CREMATION = "http://familysearch.org/v1/EditCremation";
    const DELETE_CREMATION = "http://familysearch.org/v1/DeleteCremation";
    const ADD_DEATH = "http://familysearch.org/v1/AddDeath";
    const EDIT_DEATH = "http://familysearch.org/v1/EditDeath";
    const DELETE_DEATH = "http://familysearch.org/v1/DeleteDeath";
    const ADD_LIVING_STATUS = "http://familysearch.org/v1/AddLivingStatus";
    const EDIT_LIVING_STATUS = "http://familysearch.org/v1/EditLivingStatus";
    const DELETE_LIVING_STATUS = "http://familysearch.org/v1/DeleteLivingStatus";
    const ADD_MILITARY_SERVICE = "http://familysearch.org/v1/AddMilitaryService";
    const EDIT_MILITARY_SERVICE = "http://familysearch.org/v1/EditMilitaryService";
    const DELETE_MILITARY_SERVICE = "http://familysearch.org/v1/DeleteMilitaryService";
    const ADD_NATURALIZATION = "http://familysearch.org/v1/AddNaturalization";
    const EDIT_NATURALIZATION = "http://familysearch.org/v1/EditNaturalization";
    const DELETE_NATURALIZATION = "http://familysearch.org/v1/DeleteNaturalization";
    const ADD_NOBILITY_TYPE = "http://familysearch.org/v1/AddNobilityType";
    const EDIT_NOBILITY_TYPE = "http://familysearch.org/v1/EditNobilityType";
    const DELETE_NOBILITY_TYPE = "http://familysearch.org/v1/DeleteNobilityType";
    const ADD_OCCUPATION = "http://familysearch.org/v1/AddOccupation";
    const EDIT_OCCUPATION = "http://familysearch.org/v1/EditOccupation";
    const DELETE_OCCUPATION = "http://familysearch.org/v1/DeleteOccupation";
    const ADD_RELIGIOUS_AFFILIATION = "http://familysearch.org/v1/AddReligiousAffiliation";
    const EDIT_RELIGIOUS_AFFILIATION = "http://familysearch.org/v1/EditReligiousAffiliation";
    const DELETE_RELIGIOUS_AFFILIATION = "http://familysearch.org/v1/DeleteReligiousAffiliation";
    const ADD_RESIDENCE = "http://familysearch.org/v1/AddResidence";
    const EDIT_RESIDENCE = "http://familysearch.org/v1/EditResidence";
    const DELETE_RESIDENCE = "http://familysearch.org/v1/DeleteResidence";
    const ADD_STILLBORN = "http://familysearch.org/v1/AddStillborn";
    const EDIT_STILLBORN = "http://familysearch.org/v1/EditStillborn";
    const DELETE_STILLBORN = "http://familysearch.org/v1/DeleteStillborn";

    // Couple Event/Fact Changes
    const ADD_ANNULMENT = "http://familysearch.org/v1/AddAnnulment";
    const EDIT_ANNULMENT = "http://familysearch.org/v1/EditAnnulment";
    const DELETE_ANNULMENT = "http://familysearch.org/v1/DeleteAnnulment";
    const ADD_COMMON_LAW_MARRIAGE = "http://familysearch.org/v1/AddCommonLawMarriage";
    const EDIT_COMMON_LAW_MARRIAGE = "http://familysearch.org/v1/EditCommonLawMarriage";
    const DELETE_COMMON_LAW_MARRIAGE = "http://familysearch.org/v1/DeleteCommonLawMarriage";
    const ADD_DIVORCE = "http://familysearch.org/v1/AddDivorce";
    const EDIT_DIVORCE = "http://familysearch.org/v1/EditDivorce";
    const DELETE_DIVORCE = "http://familysearch.org/v1/DeleteDivorce";
    const ADD_MARRIAGE = "http://familysearch.org/v1/AddMarriage";
    const EDIT_MARRIAGE = "http://familysearch.org/v1/EditMarriage";
    const DELETE_MARRIAGE = "http://familysearch.org/v1/DeleteMarriage";
    const ADD_COUPLE_EVENT = "http://familysearch.org/v1/AddCoupleEvent";
    const EDIT_COUPLE_EVENT = "http://familysearch.org/v1/EditCoupleEvent";
    const DELETE_COUPLE_EVENT = "http://familysearch.org/v1/DeleteCoupleEvent";

    // Parent-Child Fact Changes
    const ADD_ADOPTIVE_PARENT = "http://familysearch.org/v1/AddAdoptiveParent";
    const EDIT_ADOPTIVE_PARENT = "http://familysearch.org/v1/EditAdoptiveParent";
    const DELETE_ADOPTIVE_PARENT = "http://familysearch.org/v1/DeleteAdoptiveParent";
    const ADD_BIOLOGICAL_PARENT = "http://familysearch.org/v1/AddBiologicalParent";
    const EDIT_BIOLOGICAL_PARENT = "http://familysearch.org/v1/EditBiologicalParent";
    const DELETE_BIOLOGICAL_PARENT = "http://familysearch.org/v1/DeleteBiologicalParent";
    const ADD_FOSTER_PARENT = "http://familysearch.org/v1/AddFosterParent";
    const EDIT_FOSTER_PARENT = "http://familysearch.org/v1/EditFosterParent";
    const DELETE_FOSTER_PARENT = "http://familysearch.org/v1/DeleteFosterParent";
    const ADD_GUARDIAN_PARENT = "http://familysearch.org/v1/AddGuardianParent";
    const EDIT_GUARDIAN_PARENT = "http://familysearch.org/v1/EditGuardianParent";
    const DELETE_GUARDIAN_PARENT = "http://familysearch.org/v1/DeleteGuardianParent";
    const ADD_STEP_PARENT = "http://familysearch.org/v1/AddStepParent";
    const EDIT_STEP_PARENT = "http://familysearch.org/v1/EditStepParent";
    const DELETE_STEP_PARENT = "http://familysearch.org/v1/DeleteStepParent";

    // Other Person Event Changes
    const ADD_OTHER_EVENT = "http://familysearch.org/v1/AddOtherEvent";
    const EDIT_OTHER_EVENT = "http://familysearch.org/v1/EditOtherEvent";
    const DELETE_OTHER_EVENT = "http://familysearch.org/v1/DeleteOtherEvent";

    // Person Fact Changes
    const ADD_CASTE_NAME = "http://familysearch.org/v1/AddCasteName";
    const EDIT_CASTE_NAME = "http://familysearch.org/v1/EditCasteName";
    const DELETE_CASTE_NAME = "http://familysearch.org/v1/DeleteCasteName";
    const ADD_CLAN_NAME = "http://familysearch.org/v1/AddClanName";
    const EDIT_CLAN_NAME = "http://familysearch.org/v1/EditClanName";
    const DELETE_CLAN_NAME = "http://familysearch.org/v1/DeleteClanName";
    const ADD_DIED_BEFORE_EIGHT = "http://familysearch.org/v1/AddDiedBeforeEight";
    const EDIT_DIED_BEFORE_EIGHT = "http://familysearch.org/v1/EditDiedBeforeEight";
    const DELETE_DIED_BEFORE_EIGHT = "http://familysearch.org/v1/DeleteDiedBeforeEight";
    const ADD_LIFE_SKETCH = "http://familysearch.org/v1/AddLifeSketch";
    const EDIT_LIFE_SKETCH = "http://familysearch.org/v1/EditLifeSketch";
    const DELETE_LIFE_SKETCH = "http://familysearch.org/v1/DeleteLifeSketch";
    const ADD_NATIONAL_ID = "http://familysearch.org/v1/AddNationalId";
    const EDIT_NATIONAL_ID = "http://familysearch.org/v1/EditNationalId";
    const DELETE_NATIONAL_ID = "http://familysearch.org/v1/DeleteNationalId";
    const ADD_NATIONAL_ORIGIN = "http://familysearch.org/v1/AddNationalOrigin";
    const EDIT_NATIONAL_ORIGIN = "http://familysearch.org/v1/EditNationalOrigin";
    const DELETE_NATIONAL_ORIGIN = "http://familysearch.org/v1/DeleteNationalOrigin";
    const ADD_PHYSICAL_DESCRIPTION = "http://familysearch.org/v1/AddPhysicalDescription";
    const EDIT_PHYSICAL_DESCRIPTION = "http://familysearch.org/v1/EditPhysicalDescription";
    const DELETE_PHYSICAL_DESCRIPTION = "http://familysearch.org/v1/DeletePhysicalDescription";
    const ADD_RACE = "http://familysearch.org/v1/AddRace";
    const EDIT_RACE = "http://familysearch.org/v1/EditRace";
    const DELETE_RACE = "http://familysearch.org/v1/DeleteRace";
    const ADD_TRIBE_NAME = "http://familysearch.org/v1/AddTribeName";
    const EDIT_TRIBE_NAME = "http://familysearch.org/v1/EditTribeName";
    const DELETE_TRIBE_NAME = "http://familysearch.org/v1/DeleteTribeName";
    const ADD_OTHER_FACT = "http://familysearch.org/v1/AddOtherFact";
    const EDIT_OTHER_FACT = "http://familysearch.org/v1/EditOtherFact";
    const DELETE_OTHER_FACT = "http://familysearch.org/v1/DeleteOtherFact";

    // Gender Changes
    const ADD_GENDER = "http://familysearch.org/v1/AddGender";
    const EDIT_GENDER = "http://familysearch.org/v1/EditGender";
    const DELETE_GENDER = "http://familysearch.org/v1/DeleteGender";

    // Name Changes
    const ADD_BIRTH_NAME = "http://familysearch.org/v1/AddBirthName";
    const EDIT_BIRTH_NAME = "http://familysearch.org/v1/EditBirthName";
    const DELETE_BIRTH_NAME = "http://familysearch.org/v1/DeleteBirthName";
    const ADD_AKA_NAME = "http://familysearch.org/v1/AddAkaName";
    const EDIT_AKA_NAME = "http://familysearch.org/v1/EditAkaName";
    const DELETE_AKA_NAME = "http://familysearch.org/v1/DeleteAkaName";
    const ADD_ALTERNATE_NAME = "http://familysearch.org/v1/AddAlternateName";
    const EDIT_ALTERNATE_NAME = "http://familysearch.org/v1/EditAlternateName";
    const DELETE_ALTERNATE_NAME = "http://familysearch.org/v1/DeleteAlternateName";
    const ADD_MARRIED_NAME = "http://familysearch.org/v1/AddMarriedName";
    const EDIT_MARRIED_NAME = "http://familysearch.org/v1/EditMarriedName";
    const DELETE_MARRIED_NAME = "http://familysearch.org/v1/DeleteMarriedName";
    const ADD_NICK_NAME = "http://familysearch.org/v1/AddNickName";
    const EDIT_NICK_NAME = "http://familysearch.org/v1/EditNickName";
    const DELETE_NICK_NAME = "http://familysearch.org/v1/DeleteNickName";
    const ADD_OTHER_NAME = "http://familysearch.org/v1/AddOtherName";
    const EDIT_OTHER_NAME = "http://familysearch.org/v1/EditOtherName";
    const DELETE_OTHER_NAME = "http://familysearch.org/v1/DeleteOtherName";

    // Lineage Changes
    const ADD_LINEAGE = "http://familysearch.org/v1/AddLineage";
    const EDIT_LINEAGE = "http://familysearch.org/v1/EditLineage";
    const DELETE_LINEAGE = "http://familysearch.org/v1/DeleteLineage";

    // LDS Ordinance Changes
    const COMPLETE_BAPTISM = "http://familysearch.org/v1/CompleteBaptism";
    const COMPLETE_CONFIRMATION = "http://familysearch.org/v1/CompleteConfirmation";
    const COMPLETE_INITIATORY = "http://familysearch.org/v1/CompleteInitiatory";
    const COMPLETE_ENDOWMENT = "http://familysearch.org/v1/CompleteEndowment";
    const COMPLETE_COUPLE_SEALING = "http://familysearch.org/v1/CompleteCoupleSealing";
    const COMPLETE_SEALING_TO_PARENTS = "http://familysearch.org/v1/CompleteSealingToParents";

    // Note Changes
    const ADD_PERSON_NOTE = "http://familysearch.org/v1/AddPersonNote";
    const EDIT_PERSON_NOTE = "http://familysearch.org/v1/EditPersonNote";
    const DELETE_PERSON_NOTE = "http://familysearch.org/v1/DeletePersonNote";
    const ADD_CHILD_PARENTS_NOTE = "http://familysearch.org/v1/AddChildParentsNote";
    const EDIT_CHILD_PARENTS_NOTE = "http://familysearch.org/v1/EditChildParentsNote";
    const DELETE_CHILD_PARENTS_NOTE = "http://familysearch.org/v1/DeleteChildParentsNote";
    const ADD_COUPLE_NOTE = "http://familysearch.org/v1/AddCoupleNote";
    const EDIT_COUPLE_NOTE = "http://familysearch.org/v1/EditCoupleNote";
    const DELETE_COUPLE_NOTE = "http://familysearch.org/v1/DeleteCoupleNote";
}
