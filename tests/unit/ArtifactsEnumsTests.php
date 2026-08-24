<?php

namespace Gedcomx\Tests\Unit;

use Gedcomx\Extensions\FamilySearch\Platform\Artifacts\ArtifactAccessPermission;
use Gedcomx\Extensions\FamilySearch\Platform\Artifacts\ArtifactDisplayState;
use Gedcomx\Extensions\FamilySearch\Platform\Artifacts\ArtifactScreeningState;
use Gedcomx\Tests\ApiTestCase;

class ArtifactsEnumsTest extends ApiTestCase
{
    public function testArtifactAccessPermissionEnum()
    {
        $this->assertEquals('http://familysearch.org/v1/Allowed', ArtifactAccessPermission::ALLOWED);
        $this->assertEquals('http://familysearch.org/v1/Denied', ArtifactAccessPermission::DENIED);
    }

    public function testArtifactDisplayStateEnum()
    {
        $this->assertEquals('http://gedcomx.org/v1/Processing', ArtifactDisplayState::PROCESSING);
        $this->assertEquals('http://gedcomx.org/v1/UploadFailed', ArtifactDisplayState::UPLOAD_FAILED);
        $this->assertEquals('http://gedcomx.org/v1/ProcessingFailed', ArtifactDisplayState::PROCESSING_FAILED);
        $this->assertEquals('http://gedcomx.org/v1/Restricted', ArtifactDisplayState::RESTRICTED);
        $this->assertEquals('http://gedcomx.org/v1/Approved', ArtifactDisplayState::APPROVED);
    }

    public function testArtifactScreeningStateEnum()
    {
        $this->assertEquals('http://gedcomx.org/v1/Pending', ArtifactScreeningState::PENDING);
        $this->assertEquals('http://gedcomx.org/v1/Approved', ArtifactScreeningState::APPROVED);
        $this->assertEquals('http://gedcomx.org/v1/Restricted', ArtifactScreeningState::RESTRICTED);
    }

    public function testArtifactAccessPermissionValues()
    {
        $allowed = ArtifactAccessPermission::ALLOWED;
        $denied = ArtifactAccessPermission::DENIED;

        $this->assertNotEquals($allowed, $denied);
        $this->assertIsString($allowed);
        $this->assertIsString($denied);
    }

    public function testArtifactDisplayStateValues()
    {
        $processing = ArtifactDisplayState::PROCESSING;
        $uploadFailed = ArtifactDisplayState::UPLOAD_FAILED;
        $processingFailed = ArtifactDisplayState::PROCESSING_FAILED;
        $restricted = ArtifactDisplayState::RESTRICTED;
        $approved = ArtifactDisplayState::APPROVED;

        $this->assertNotEquals($processing, $approved);
        $this->assertNotEquals($uploadFailed, $processingFailed);
        $this->assertIsString($processing);
        $this->assertIsString($approved);
    }

    public function testArtifactScreeningStateValues()
    {
        $pending = ArtifactScreeningState::PENDING;
        $approved = ArtifactScreeningState::APPROVED;
        $restricted = ArtifactScreeningState::RESTRICTED;

        $this->assertNotEquals($pending, $approved);
        $this->assertNotEquals($approved, $restricted);
        $this->assertIsString($pending);
        $this->assertIsString($approved);
    }

    public function testArtifactAccessPermissionNamespace()
    {
        // ArtifactAccessPermission uses FamilySearch namespace
        $this->assertStringContainsString('familysearch.org/v1/', ArtifactAccessPermission::ALLOWED);
        $this->assertStringContainsString('familysearch.org/v1/', ArtifactAccessPermission::DENIED);
    }

    public function testArtifactDisplayStateNamespace()
    {
        // ArtifactDisplayState uses GEDCOM X namespace
        $this->assertStringContainsString('gedcomx.org/v1/', ArtifactDisplayState::PROCESSING);
        $this->assertStringContainsString('gedcomx.org/v1/', ArtifactDisplayState::APPROVED);
    }

    public function testArtifactScreeningStateNamespace()
    {
        // ArtifactScreeningState uses GEDCOM X namespace (deprecated)
        $this->assertStringContainsString('gedcomx.org/v1/', ArtifactScreeningState::PENDING);
        $this->assertStringContainsString('gedcomx.org/v1/', ArtifactScreeningState::APPROVED);
    }

    public function testArtifactScreeningStateAndDisplayStateOverlap()
    {
        // Both have APPROVED and RESTRICTED constants
        $this->assertEquals(
            'http://gedcomx.org/v1/Approved',
            ArtifactScreeningState::APPROVED
        );
        $this->assertEquals(
            'http://gedcomx.org/v1/Approved',
            ArtifactDisplayState::APPROVED
        );

        $this->assertEquals(
            'http://gedcomx.org/v1/Restricted',
            ArtifactScreeningState::RESTRICTED
        );
        $this->assertEquals(
            'http://gedcomx.org/v1/Restricted',
            ArtifactDisplayState::RESTRICTED
        );
    }

    public function testArtifactDisplayStateUniqueValues()
    {
        // These are unique to ArtifactDisplayState
        $this->assertEquals('http://gedcomx.org/v1/Processing', ArtifactDisplayState::PROCESSING);
        $this->assertEquals('http://gedcomx.org/v1/UploadFailed', ArtifactDisplayState::UPLOAD_FAILED);
        $this->assertEquals('http://gedcomx.org/v1/ProcessingFailed', ArtifactDisplayState::PROCESSING_FAILED);
    }

    public function testArtifactScreeningStateUniqueValues()
    {
        // PENDING is unique to ArtifactScreeningState
        $this->assertEquals('http://gedcomx.org/v1/Pending', ArtifactScreeningState::PENDING);
    }

    public function testEnumConstantsAreAccessible()
    {
        // Test that all constants can be accessed
        $this->assertNotNull(ArtifactAccessPermission::ALLOWED);
        $this->assertNotNull(ArtifactAccessPermission::DENIED);

        $this->assertNotNull(ArtifactDisplayState::PROCESSING);
        $this->assertNotNull(ArtifactDisplayState::UPLOAD_FAILED);
        $this->assertNotNull(ArtifactDisplayState::PROCESSING_FAILED);
        $this->assertNotNull(ArtifactDisplayState::RESTRICTED);
        $this->assertNotNull(ArtifactDisplayState::APPROVED);

        $this->assertNotNull(ArtifactScreeningState::PENDING);
        $this->assertNotNull(ArtifactScreeningState::APPROVED);
        $this->assertNotNull(ArtifactScreeningState::RESTRICTED);
    }
}
