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

namespace Gedcomx\Extensions\FamilySearch\Platform\Artifacts;

/**
 * Enumeration of known artifact display states.
 *
 * @package Gedcomx\Extensions\FamilySearch\Platform\Artifacts
 */
class ArtifactDisplayState
{
    /**
     * The artifact is processing.
     */
    const PROCESSING = "http://gedcomx.org/v1/Processing";

    /**
     * The artifact upload failed.
     */
    const UPLOAD_FAILED = "http://gedcomx.org/v1/UploadFailed";

    /**
     * The artifact processing failed.
     */
    const PROCESSING_FAILED = "http://gedcomx.org/v1/ProcessingFailed";

    /**
     * The artifact has been restricted.
     */
    const RESTRICTED = "http://gedcomx.org/v1/Restricted";

    /**
     * The artifact has been approved.
     */
    const APPROVED = "http://gedcomx.org/v1/Approved";
}
