<?php

namespace Gedcomx\Tests\Unit;

use Gedcomx\Common\Attribution;
use Gedcomx\Common\ResourceReference;
use Gedcomx\Extensions\FamilySearch\Platform\Tree\AdditionalAttribution;
use Gedcomx\Extensions\FamilySearch\Platform\Tree\ChangeType;
use Gedcomx\Extensions\FamilySearch\Platform\Tree\FamilySearchFactType;
use Gedcomx\Extensions\FamilySearch\Platform\Tree\FamilySearchIdentifierType;
use Gedcomx\Extensions\FamilySearch\Platform\Tree\FamilyTreeFactQualifierType;
use Gedcomx\Extensions\FamilySearch\Platform\Tree\Group;
use Gedcomx\Extensions\FamilySearch\Platform\Tree\GroupMember;
use Gedcomx\Extensions\FamilySearch\Platform\Tree\MatchCollection;
use Gedcomx\Extensions\FamilySearch\Platform\Tree\NameFormInfo;
use Gedcomx\Extensions\FamilySearch\Platform\Tree\NameFormOrder;
use Gedcomx\Extensions\FamilySearch\Platform\Tree\PersonInfo;
use Gedcomx\Extensions\FamilySearch\Platform\Tree\RelationshipRole;
use Gedcomx\Extensions\FamilySearch\Platform\Tree\SearchCollection;
use Gedcomx\Extensions\FamilySearch\Platform\Tree\SearchInfo;
use Gedcomx\Extensions\FamilySearch\Platform\Tree\SourceReferenceTagType;
use Gedcomx\Extensions\FamilySearch\Platform\Tree\ThirdPartyAccess;
use Gedcomx\Extensions\FamilySearch\Platform\Tree\Tree;
use Gedcomx\Extensions\FamilySearch\Platform\Tree\TreePersonReference;
use Gedcomx\Tests\ApiTestCase;

/**
 * Comprehensive tests for 18 newly implemented FamilySearch extension classes
 * Tests construction, getters/setters, and serialization/deserialization
 */
class NewFamilySearchExtensionsTests extends ApiTestCase
{
    // ========== ENUM TESTS ==========

    /**
     * Test ChangeType enum constants
     */
    public function testChangeTypeConstants()
    {
        $this->assertEquals('http://familysearch.org/v1/CreatePerson', ChangeType::CREATE_PERSON);
        $this->assertEquals('http://familysearch.org/v1/DeletePerson', ChangeType::DELETE_PERSON);
        $this->assertEquals('http://familysearch.org/v1/AddBirth', ChangeType::ADD_BIRTH);
        $this->assertEquals('http://familysearch.org/v1/MergePerson', ChangeType::MERGE_PERSON);
        $this->assertEquals('http://familysearch.org/v1/CreateCoupleRelationship', ChangeType::CREATE_COUPLE_RELATIONSHIP);
        $this->assertEquals('http://familysearch.org/v1/CreateChildAndParentsRelationship', ChangeType::CREATE_CHILD_AND_PARENTS_RELATIONSHIP);
    }

    /**
     * Test FamilySearchFactType enum constants
     */
    public function testFamilySearchFactTypeConstants()
    {
        $this->assertEquals('http://familysearch.org/v1/Affiliation', FamilySearchFactType::AFFILIATION);
        $this->assertEquals('http://familysearch.org/v1/LifeSketch', FamilySearchFactType::LIFE_SKETCH);
        $this->assertEquals('http://familysearch.org/v1/TribeName', FamilySearchFactType::TRIBE_NAME);
        $this->assertEquals('http://familysearch.org/v1/TitleOfNobility', FamilySearchFactType::TITLE_OF_NOBILITY);
    }

    /**
     * Test FamilySearchIdentifierType enum constants
     */
    public function testFamilySearchIdentifierTypeConstants()
    {
        $this->assertEquals(
            'http://familysearch.org/v1/ChildAndParentsRelationship',
            FamilySearchIdentifierType::CHILD_AND_PARENTS_RELATIONSHIP
        );
        $this->assertEquals(
            'http://familysearch.org/v1/MemoryPerson',
            FamilySearchIdentifierType::MEMORY_PERSON
        );
        $this->assertEquals(
            'http://familysearch.org/v1/FamilyTreePerson',
            FamilySearchIdentifierType::FAMILY_TREE_PERSON
        );
    }

    /**
     * Test FamilyTreeFactQualifierType enum constants
     */
    public function testFamilyTreeFactQualifierTypeConstants()
    {
        $this->assertEquals('http://familysearch.org/v1/Event', FamilyTreeFactQualifierType::EVENT);
    }

    /**
     * Test SourceReferenceTagType enum constants
     */
    public function testSourceReferenceTagTypeConstants()
    {
        $this->assertEquals('http://gedcomx.org/Name', SourceReferenceTagType::NAME);
        $this->assertEquals('http://gedcomx.org/Gender', SourceReferenceTagType::GENDER);
    }

    /**
     * Test RelationshipRole enum constants
     */
    public function testRelationshipRoleConstants()
    {
        $this->assertEquals('http://familysearch.org/v1/Parent1', RelationshipRole::PARENT1);
        $this->assertEquals('http://familysearch.org/v1/Parent2', RelationshipRole::PARENT2);
        $this->assertEquals('http://familysearch.org/v1/Child', RelationshipRole::CHILD);
        $this->assertEquals('http://familysearch.org/v1/Spouse1', RelationshipRole::SPOUSE1);
        $this->assertEquals('http://familysearch.org/v1/Spouse2', RelationshipRole::SPOUSE2);
    }

    /**
     * Test ThirdPartyAccess enum constants
     */
    public function testThirdPartyAccessConstants()
    {
        $this->assertEquals('http://familysearch.org/v1/AnyApps', ThirdPartyAccess::ANY_APPS);
        $this->assertEquals('http://familysearch.org/v1/CompanyApps', ThirdPartyAccess::COMPANY_APPS);
        $this->assertEquals('http://familysearch.org/v1/None', ThirdPartyAccess::NONE);
        $this->assertEquals('http://familysearch.org/v1/OTHER', ThirdPartyAccess::OTHER);
    }

    /**
     * Test MatchCollection enum constants
     */
    public function testMatchCollectionConstants()
    {
        $this->assertEquals('https://familysearch.org/platform/collections/tree', MatchCollection::TREE);
        $this->assertEquals('https://familysearch.org/platform/collections/records', MatchCollection::RECORDS);
        $this->assertEquals('https://familysearch.org/platform/collections/trees', MatchCollection::LLS);
        $this->assertEquals('https://familysearch.org/platform/collections/user_trees', MatchCollection::USER_TREES);
    }

    /**
     * Test SearchCollection enum constants and getId method
     */
    public function testSearchCollectionConstants()
    {
        $this->assertEquals('https://familysearch.org/platform/collections/tree', SearchCollection::TREE);
        $this->assertEquals('0', SearchCollection::TREE_ID);
        $this->assertEquals('https://familysearch.org/platform/collections/user_trees', SearchCollection::USER_TREES);
        $this->assertEquals('10', SearchCollection::USER_TREES_ID);

        // Test static getId method
        $this->assertEquals('0', SearchCollection::getId(SearchCollection::TREE));
        $this->assertEquals('10', SearchCollection::getId(SearchCollection::USER_TREES));
    }

    /**
     * Test NameFormOrder enum constants
     */
    public function testNameFormOrderConstants()
    {
        $this->assertEquals('http://familysearch.org/v1/Eurotypic', NameFormOrder::EUROTYPIC);
        $this->assertEquals('http://familysearch.org/v1/Sinotypic', NameFormOrder::SINOTYPIC);
    }

    // ========== DATA CLASS TESTS ==========

    /**
     * Test AdditionalAttribution construction
     */
    public function testAdditionalAttributionConstruction()
    {
        $additionalAttr = new AdditionalAttribution();

        $contributor = new ResourceReference();
        $contributor->setResource('https://familysearch.org/platform/users/USER123');
        $additionalAttr->setContributor($contributor);

        $additionalAttr->setModified(1625097600000);
        $additionalAttr->setChangeMessage('Added birth date from parish records');

        $this->assertNotNull($additionalAttr->getContributor());
        $this->assertEquals('https://familysearch.org/platform/users/USER123', $additionalAttr->getContributor()->getResource());
        $this->assertEquals(1625097600000, $additionalAttr->getModified());
        $this->assertEquals('Added birth date from parish records', $additionalAttr->getChangeMessage());
    }

    /**
     * Test AdditionalAttribution from array
     */
    public function testAdditionalAttributionFromArray()
    {
        $additionalAttr = new AdditionalAttribution([
            'contributor' => [
                'resource' => 'https://familysearch.org/platform/users/USER456'
            ],
            'modified' => 1625097600000,
            'changeMessage' => 'Verified with census record'
        ]);

        $this->assertNotNull($additionalAttr->getContributor());
        $this->assertEquals(1625097600000, $additionalAttr->getModified());
        $this->assertEquals('Verified with census record', $additionalAttr->getChangeMessage());
    }

    /**
     * Test AdditionalAttribution JSON round-trip
     */
    public function testAdditionalAttributionJsonRoundTrip()
    {
        $additionalAttr = new AdditionalAttribution([
            'contributor' => ['resource' => '#USER1'],
            'modified' => 1625097600000,
            'changeMessage' => 'Test change'
        ]);

        $json = $additionalAttr->toJson();
        $this->assertStringContainsString('USER1', $json);
        $this->assertStringContainsString('Test change', $json);

        $decoded = json_decode($json, true);
        $additionalAttr2 = new AdditionalAttribution($decoded);
        $this->assertEquals('Test change', $additionalAttr2->getChangeMessage());
    }

    /**
     * Test NameFormInfo construction
     */
    public function testNameFormInfoConstruction()
    {
        $nameInfo = new NameFormInfo();
        $nameInfo->setOrder(NameFormOrder::SINOTYPIC);

        $this->assertEquals(NameFormOrder::SINOTYPIC, $nameInfo->getOrder());
    }

    /**
     * Test NameFormInfo from array
     */
    public function testNameFormInfoFromArray()
    {
        $nameInfo = new NameFormInfo([
            'order' => NameFormOrder::EUROTYPIC
        ]);

        $this->assertEquals(NameFormOrder::EUROTYPIC, $nameInfo->getOrder());
    }

    /**
     * Test NameFormInfo JSON round-trip
     */
    public function testNameFormInfoJsonRoundTrip()
    {
        $nameInfo = new NameFormInfo(['order' => NameFormOrder::SINOTYPIC]);

        $json = $nameInfo->toJson();
        $this->assertStringContainsString('Sinotypic', $json);

        $decoded = json_decode($json, true);
        $nameInfo2 = new NameFormInfo($decoded);
        $this->assertEquals(NameFormOrder::SINOTYPIC, $nameInfo2->getOrder());
    }

    /**
     * Test SearchInfo construction
     */
    public function testSearchInfoConstruction()
    {
        $searchInfo = new SearchInfo();
        $searchInfo->setTotalHits(150);
        $searchInfo->setCloseHits(25);

        $this->assertEquals(150, $searchInfo->getTotalHits());
        $this->assertEquals(25, $searchInfo->getCloseHits());
    }

    /**
     * Test SearchInfo from array
     */
    public function testSearchInfoFromArray()
    {
        $searchInfo = new SearchInfo([
            'totalHits' => 200,
            'closeHits' => 30
        ]);

        $this->assertEquals(200, $searchInfo->getTotalHits());
        $this->assertEquals(30, $searchInfo->getCloseHits());
    }

    /**
     * Test SearchInfo JSON round-trip
     */
    public function testSearchInfoJsonRoundTrip()
    {
        $searchInfo = new SearchInfo(['totalHits' => 100, 'closeHits' => 15]);

        $json = $searchInfo->toJson();
        $this->assertStringContainsString('100', $json);
        $this->assertStringContainsString('15', $json);

        $decoded = json_decode($json, true);
        $searchInfo2 = new SearchInfo($decoded);
        $this->assertEquals(100, $searchInfo2->getTotalHits());
        $this->assertEquals(15, $searchInfo2->getCloseHits());
    }

    /**
     * Test PersonInfo construction
     */
    public function testPersonInfoConstruction()
    {
        $personInfo = new PersonInfo();
        $personInfo->setCanUserEdit(true);
        $personInfo->setVisibleToAll(false);
        $personInfo->setVisibleToAllWhenUsingFamilySearchApps(true);
        $personInfo->setTreeId('TREE123');

        $this->assertTrue($personInfo->getCanUserEdit());
        $this->assertFalse($personInfo->getVisibleToAll());
        $this->assertTrue($personInfo->getVisibleToAllWhenUsingFamilySearchApps());
        $this->assertEquals('TREE123', $personInfo->getTreeId());
    }

    /**
     * Test PersonInfo from array
     */
    public function testPersonInfoFromArray()
    {
        $personInfo = new PersonInfo([
            'canUserEdit' => true,
            'visibleToAll' => true,
            'treeId' => 'TREE456'
        ]);

        $this->assertTrue($personInfo->getCanUserEdit());
        $this->assertTrue($personInfo->getVisibleToAll());
        $this->assertEquals('TREE456', $personInfo->getTreeId());
    }

    /**
     * Test PersonInfo JSON round-trip
     */
    public function testPersonInfoJsonRoundTrip()
    {
        $personInfo = new PersonInfo([
            'canUserEdit' => true,
            'visibleToAll' => false,
            'treeId' => 'TREE789'
        ]);

        $json = $personInfo->toJson();
        $this->assertStringContainsString('TREE789', $json);

        $decoded = json_decode($json, true);
        $personInfo2 = new PersonInfo($decoded);
        $this->assertTrue($personInfo2->getCanUserEdit());
        $this->assertEquals('TREE789', $personInfo2->getTreeId());
    }

    /**
     * Test GroupMember construction
     */
    public function testGroupMemberConstruction()
    {
        $member = new GroupMember();
        $member->setGroupId('GROUP1');
        $member->setCisId('USER001');
        $member->setContactName('John Smith');
        $member->setStatus('active');

        $this->assertEquals('GROUP1', $member->getGroupId());
        $this->assertEquals('USER001', $member->getCisId());
        $this->assertEquals('John Smith', $member->getContactName());
        $this->assertEquals('active', $member->getStatus());
    }

    /**
     * Test GroupMember from array
     */
    public function testGroupMemberFromArray()
    {
        $member = new GroupMember([
            'groupId' => 'GROUP2',
            'cisId' => 'USER002',
            'contactName' => 'Jane Doe',
            'status' => 'invited'
        ]);

        $this->assertEquals('GROUP2', $member->getGroupId());
        $this->assertEquals('USER002', $member->getCisId());
        $this->assertEquals('Jane Doe', $member->getContactName());
        $this->assertEquals('invited', $member->getStatus());
    }

    /**
     * Test GroupMember JSON round-trip
     */
    public function testGroupMemberJsonRoundTrip()
    {
        $member = new GroupMember([
            'cisId' => 'USER999',
            'contactName' => 'Test User',
            'status' => 'active'
        ]);

        $json = $member->toJson();
        $this->assertStringContainsString('USER999', $json);
        $this->assertStringContainsString('Test User', $json);

        $decoded = json_decode($json, true);
        $member2 = new GroupMember($decoded);
        $this->assertEquals('USER999', $member2->getCisId());
        $this->assertEquals('Test User', $member2->getContactName());
    }

    // ========== CONTAINER/COLLECTION TESTS ==========

    /**
     * Test Group construction with arrays
     */
    public function testGroupConstruction()
    {
        $group = new Group();
        $group->setId('GROUP123');
        $group->setName('Smith Family Research');
        $group->setDescription('Collaborative research on Smith family');
        $group->setTreeIds(['TREE1', 'TREE2', 'TREE3']);

        $member1 = new GroupMember();
        $member1->setCisId('USER001');
        $member1->setContactName('John Smith');
        $group->addMember($member1);

        $member2 = new GroupMember();
        $member2->setCisId('USER002');
        $member2->setContactName('Jane Doe');
        $group->addMember($member2);

        $this->assertEquals('GROUP123', $group->getId());
        $this->assertEquals('Smith Family Research', $group->getName());
        $this->assertCount(3, $group->getTreeIds());
        $this->assertCount(2, $group->getMembers());
        $this->assertEquals('USER001', $group->getMembers()[0]->getCisId());
    }

    /**
     * Test Group from array
     */
    public function testGroupFromArray()
    {
        $group = new Group([
            'id' => 'GROUP456',
            'name' => 'Research Group',
            'treeIds' => ['TREE1', 'TREE2'],
            'members' => [
                ['cisId' => 'USER1', 'contactName' => 'User One'],
                ['cisId' => 'USER2', 'contactName' => 'User Two']
            ]
        ]);

        $this->assertEquals('GROUP456', $group->getId());
        $this->assertCount(2, $group->getTreeIds());
        $this->assertCount(2, $group->getMembers());
        $this->assertEquals('USER1', $group->getMembers()[0]->getCisId());
    }

    /**
     * Test Group JSON round-trip
     */
    public function testGroupJsonRoundTrip()
    {
        $group = new Group([
            'id' => 'GROUP789',
            'name' => 'Test Group',
            'treeIds' => ['TREE1'],
            'members' => [
                ['cisId' => 'USER123', 'contactName' => 'Test User']
            ]
        ]);

        $json = $group->toJson();
        $this->assertStringContainsString('GROUP789', $json);
        $this->assertStringContainsString('Test Group', $json);

        $decoded = json_decode($json, true);
        $group2 = new Group($decoded);
        $this->assertEquals('GROUP789', $group2->getId());
        $this->assertCount(1, $group2->getMembers());
    }

    /**
     * Test Tree construction with access controls
     */
    public function testTreeConstruction()
    {
        $tree = new Tree();
        $tree->setId('TREE123');
        $tree->setName('Smith Family Tree');
        $tree->setDescription('Main family tree');
        $tree->setGroupIds(['GROUP1', 'GROUP2']);
        $tree->setOwnerAccess(ThirdPartyAccess::ANY_APPS);
        $tree->setGroupAccess(ThirdPartyAccess::COMPANY_APPS);
        $tree->setAllAccess(ThirdPartyAccess::NONE);
        $tree->setHidden(false);
        $tree->setPrivate(true);

        $this->assertEquals('TREE123', $tree->getId());
        $this->assertEquals('Smith Family Tree', $tree->getName());
        $this->assertCount(2, $tree->getGroupIds());
        $this->assertEquals(ThirdPartyAccess::ANY_APPS, $tree->getOwnerAccess());
        $this->assertFalse($tree->getHidden());
        $this->assertTrue($tree->getPrivate());
    }

    /**
     * Test Tree from array
     */
    public function testTreeFromArray()
    {
        $tree = new Tree([
            'id' => 'TREE456',
            'name' => 'Test Tree',
            'groupIds' => ['GROUP1'],
            'ownerAccess' => ThirdPartyAccess::ANY_APPS,
            'hidden' => false,
            'private' => true
        ]);

        $this->assertEquals('TREE456', $tree->getId());
        $this->assertEquals('Test Tree', $tree->getName());
        $this->assertCount(1, $tree->getGroupIds());
        $this->assertEquals(ThirdPartyAccess::ANY_APPS, $tree->getOwnerAccess());
    }

    /**
     * Test Tree JSON round-trip
     */
    public function testTreeJsonRoundTrip()
    {
        $tree = new Tree([
            'id' => 'TREE789',
            'name' => 'Round Trip Tree',
            'groupIds' => ['G1', 'G2'],
            'ownerAccess' => ThirdPartyAccess::COMPANY_APPS
        ]);

        $json = $tree->toJson();
        $this->assertStringContainsString('TREE789', $json);
        $this->assertStringContainsString('Round Trip Tree', $json);

        $decoded = json_decode($json, true);
        $tree2 = new Tree($decoded);
        $this->assertEquals('TREE789', $tree2->getId());
        $this->assertCount(2, $tree2->getGroupIds());
    }

    /**
     * Test TreePersonReference construction (inheritance test)
     */
    public function testTreePersonReferenceConstruction()
    {
        $ref = new TreePersonReference();

        $personRef = new ResourceReference();
        $personRef->setResource('https://familysearch.org/platform/tree/persons/PERSON123');
        $ref->setTreePerson($personRef);

        $treeRef = new ResourceReference();
        $treeRef->setResource('https://familysearch.org/platform/tree/trees/TREE456');
        $ref->setTree($treeRef);

        $attribution = new Attribution();
        $contributor = new ResourceReference();
        $contributor->setResource('#USER1');
        $attribution->setContributor($contributor);
        $ref->setAttribution($attribution);

        $this->assertNotNull($ref->getTreePerson());
        $this->assertNotNull($ref->getTree());
        $this->assertNotNull($ref->getAttribution());
        $this->assertEquals('https://familysearch.org/platform/tree/persons/PERSON123', $ref->getTreePerson()->getResource());
    }

    /**
     * Test TreePersonReference from array
     */
    public function testTreePersonReferenceFromArray()
    {
        $ref = new TreePersonReference([
            'treePerson' => ['resource' => '#PERSON1'],
            'tree' => ['resource' => '#TREE1'],
            'attribution' => [
                'contributor' => ['resource' => '#USER1']
            ]
        ]);

        $this->assertNotNull($ref->getTreePerson());
        $this->assertNotNull($ref->getTree());
        $this->assertNotNull($ref->getAttribution());
    }

    /**
     * Test TreePersonReference JSON round-trip
     */
    public function testTreePersonReferenceJsonRoundTrip()
    {
        $ref = new TreePersonReference([
            'treePerson' => ['resource' => '#PERSON999'],
            'tree' => ['resource' => '#TREE999']
        ]);

        $json = $ref->toJson();
        $this->assertStringContainsString('PERSON999', $json);
        $this->assertStringContainsString('TREE999', $json);

        $decoded = json_decode($json, true);
        $ref2 = new TreePersonReference($decoded);
        $this->assertNotNull($ref2->getTreePerson());
        $this->assertNotNull($ref2->getTree());
    }

    // ========== NULL/EMPTY TESTS ==========

    /**
     * Test classes handle null values gracefully
     */
    public function testNullValues()
    {
        // Test empty construction
        $searchInfo = new SearchInfo();
        $this->assertNull($searchInfo->getTotalHits());
        $this->assertNull($searchInfo->getCloseHits());

        $personInfo = new PersonInfo();
        // PersonInfo has default values, not null
        $this->assertFalse($personInfo->getCanUserEdit());
        $this->assertTrue($personInfo->getVisibleToAll());
        $this->assertNull($personInfo->getTreeId());

        $group = new Group();
        $this->assertNull($group->getId());
        $this->assertNull($group->getMembers());

        $tree = new Tree();
        $this->assertNull($tree->getId());
        $this->assertNull($tree->getGroupIds());
    }

    /**
     * Test empty arrays
     */
    public function testEmptyArrays()
    {
        $group = new Group();
        $group->setTreeIds([]);
        $group->setMembers([]);

        $this->assertIsArray($group->getTreeIds());
        $this->assertIsArray($group->getMembers());
        $this->assertCount(0, $group->getTreeIds());
        $this->assertCount(0, $group->getMembers());
    }

    // ========== COMPLEX NESTED OBJECT TESTS ==========

    /**
     * Test complex nested group structure
     */
    public function testComplexGroupStructure()
    {
        $group = new Group([
            'id' => 'COMPLEX-GROUP',
            'name' => 'Complex Research Group',
            'description' => 'A group with multiple members and trees',
            'codeOfConduct' => 'Be respectful and collaborative',
            'treeIds' => ['TREE1', 'TREE2', 'TREE3', 'TREE4'],
            'members' => [
                [
                    'groupId' => 'COMPLEX-GROUP',
                    'cisId' => 'USER001',
                    'contactName' => 'Alice Johnson',
                    'status' => 'active'
                ],
                [
                    'groupId' => 'COMPLEX-GROUP',
                    'cisId' => 'USER002',
                    'contactName' => 'Bob Smith',
                    'status' => 'active'
                ],
                [
                    'groupId' => 'COMPLEX-GROUP',
                    'cisId' => 'USER003',
                    'contactName' => 'Carol Williams',
                    'status' => 'invited'
                ]
            ]
        ]);

        // Verify structure
        $this->assertEquals('COMPLEX-GROUP', $group->getId());
        $this->assertCount(4, $group->getTreeIds());
        $this->assertCount(3, $group->getMembers());
        $this->assertEquals('Alice Johnson', $group->getMembers()[0]->getContactName());
        $this->assertEquals('invited', $group->getMembers()[2]->getStatus());

        // Test JSON round-trip
        $json = $group->toJson();
        $decoded = json_decode($json, true);
        $group2 = new Group($decoded);

        $this->assertEquals('COMPLEX-GROUP', $group2->getId());
        $this->assertCount(4, $group2->getTreeIds());
        $this->assertCount(3, $group2->getMembers());
    }

    /**
     * Test complex tree with all properties
     */
    public function testComplexTreeStructure()
    {
        $tree = new Tree([
            'id' => 'COMPLEX-TREE',
            'groupIds' => ['GROUP1', 'GROUP2', 'GROUP3'],
            'name' => 'Complex Family Tree',
            'description' => 'A comprehensive family tree',
            'startingPersonId' => 'PERSON-ROOT',
            'hidden' => false,
            'private' => true,
            'collectionId' => 'COLLECTION-1',
            'ownerAccess' => ThirdPartyAccess::ANY_APPS,
            'groupAccess' => ThirdPartyAccess::COMPANY_APPS,
            'allAccess' => ThirdPartyAccess::NONE
        ]);

        $this->assertEquals('COMPLEX-TREE', $tree->getId());
        $this->assertCount(3, $tree->getGroupIds());
        $this->assertEquals('PERSON-ROOT', $tree->getStartingPersonId());
        $this->assertFalse($tree->getHidden());
        $this->assertTrue($tree->getPrivate());
        $this->assertEquals(ThirdPartyAccess::ANY_APPS, $tree->getOwnerAccess());
        $this->assertEquals(ThirdPartyAccess::COMPANY_APPS, $tree->getGroupAccess());
        $this->assertEquals(ThirdPartyAccess::NONE, $tree->getAllAccess());

        // Test JSON round-trip
        $json = $tree->toJson();
        $decoded = json_decode($json, true);
        $tree2 = new Tree($decoded);

        $this->assertEquals('COMPLEX-TREE', $tree2->getId());
        $this->assertCount(3, $tree2->getGroupIds());
    }

    /**
     * Test TreePersonReference with full attribution
     */
    public function testComplexTreePersonReference()
    {
        $ref = new TreePersonReference([
            'treePerson' => [
                'resource' => 'https://familysearch.org/platform/tree/persons/PERSON-123',
                'resourceId' => 'PERSON-123'
            ],
            'tree' => [
                'resource' => 'https://familysearch.org/platform/tree/trees/TREE-456',
                'resourceId' => 'TREE-456'
            ],
            'attribution' => [
                'contributor' => [
                    'resource' => 'https://familysearch.org/platform/users/USER-789',
                    'resourceId' => 'USER-789'
                ],
                'modified' => 1625097600000,
                'changeMessage' => 'Linked from alternate tree'
            ]
        ]);

        $this->assertNotNull($ref->getTreePerson());
        $this->assertNotNull($ref->getTree());
        $this->assertNotNull($ref->getAttribution());
        $this->assertEquals(1625097600000, $ref->getAttribution()->getModified());
        $this->assertEquals('Linked from alternate tree', $ref->getAttribution()->getChangeMessage());

        // Test JSON round-trip
        $json = $ref->toJson();
        $decoded = json_decode($json, true);
        $ref2 = new TreePersonReference($decoded);

        $this->assertNotNull($ref2->getAttribution());
        $this->assertEquals('Linked from alternate tree', $ref2->getAttribution()->getChangeMessage());
    }
}
