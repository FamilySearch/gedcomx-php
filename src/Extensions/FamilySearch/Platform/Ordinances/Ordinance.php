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

use Gedcomx\Common\ResourceReference;
use Gedcomx\Conclusion\Conclusion;
use Gedcomx\Conclusion\Date;

/**
 * An ordinance conclusion.
 *
 * Represents an LDS temple ordinance with all its associated data including
 * status, participants, reservations, and completion information.
 *
 * @package Gedcomx\Extensions\FamilySearch\Platform\Ordinances
 */
class Ordinance extends Conclusion
{
    /**
     * The type of ordinance.
     *
     * @var string
     */
    private $type;

    /**
     * The status of the ordinance.
     *
     * @var string
     */
    private $status;

    /**
     * Additional information regarding the ordinance status.
     *
     * @var string[]
     */
    private $statusReasons;

    /**
     * User specific actions for this ordinance.
     *
     * @var OrdinanceActions
     */
    private $actions;

    /**
     * The principal person associated with the ordinance.
     *
     * @var ResourceReference
     */
    private $person;

    /**
     * The sex type of the principal person in the ordinance.
     *
     * @var string
     */
    private $sexType;

    /**
     * The participants in this ordinance.
     *
     * @var OrdinanceParticipant[]
     */
    private $participants;

    /**
     * Reservation for this ordinance.
     *
     * @var OrdinanceReservation
     */
    private $reservation;

    /**
     * Secondary reservation for this ordinance.
     *
     * @var OrdinanceReservation
     */
    private $secondaryReservation;

    /**
     * Caller reservation for this ordinance.
     *
     * @var OrdinanceReservation
     */
    private $callerReservation;

    /**
     * The code for the temple at which the ordinance was performed.
     *
     * @var string
     */
    private $templeCode;

    /**
     * The completion date of this ordinance.
     *
     * @var Date
     */
    private $completeDate;

    /**
     * The full name of the person, generally in the native name form.
     *
     * @var string
     */
    private $fullName;

    /**
     * The type of ordinance.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * The type of ordinance.
     *
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * Get the known ordinance type enumeration value.
     *
     * @return string|null
     */
    public function getKnownType()
    {
        if ($this->type) {
            $parts = explode('/', $this->type);
            return end($parts);
        }
        return null;
    }

    /**
     * Set the ordinance type from a known enumeration value.
     *
     * @param string $knownType One of the OrdinanceType constants
     */
    public function setKnownType($knownType)
    {
        $this->type = $knownType;
    }

    /**
     * The status of the ordinance.
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * The status of the ordinance.
     *
     * @param string $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * Get the known ordinance status enumeration value.
     *
     * @return string|null
     */
    public function getKnownStatus()
    {
        if ($this->status) {
            $parts = explode('/', $this->status);
            return end($parts);
        }
        return null;
    }

    /**
     * Set the ordinance status from a known enumeration value.
     *
     * @param string $knownStatus One of the OrdinanceStatus constants
     */
    public function setKnownStatus($knownStatus)
    {
        $this->status = $knownStatus;
    }

    /**
     * Additional information regarding the ordinance status.
     *
     * @return string[]
     */
    public function getStatusReasons()
    {
        return $this->statusReasons;
    }

    /**
     * Additional information regarding the ordinance status.
     *
     * @param string[] $statusReasons
     */
    public function setStatusReasons($statusReasons)
    {
        $this->statusReasons = $statusReasons;
    }

    /**
     * Add a status reason.
     *
     * @param string $statusReason
     */
    public function addStatusReason($statusReason)
    {
        if ($this->statusReasons == null) {
            $this->statusReasons = array();
        }
        $this->statusReasons[] = $statusReason;
    }

    /**
     * User specific actions for this ordinance.
     *
     * @return OrdinanceActions
     */
    public function getActions()
    {
        return $this->actions;
    }

    /**
     * User specific actions for this ordinance.
     *
     * @param OrdinanceActions $actions
     */
    public function setActions($actions)
    {
        $this->actions = $actions;
    }

    /**
     * The principal person associated with the ordinance.
     *
     * @return ResourceReference
     */
    public function getPerson()
    {
        return $this->person;
    }

    /**
     * The principal person associated with the ordinance.
     *
     * @param ResourceReference $person
     */
    public function setPerson($person)
    {
        $this->person = $person;
    }

    /**
     * The sex type of the principal person in the ordinance.
     *
     * @return string
     */
    public function getSexType()
    {
        return $this->sexType;
    }

    /**
     * The sex type of the principal person in the ordinance.
     *
     * @param string $sexType
     */
    public function setSexType($sexType)
    {
        $this->sexType = $sexType;
    }

    /**
     * Get the known sex type enumeration value.
     *
     * @return string|null
     */
    public function getKnownSexType()
    {
        if ($this->sexType) {
            $parts = explode('/', $this->sexType);
            return end($parts);
        }
        return null;
    }

    /**
     * Set the sex type from a known enumeration value.
     *
     * @param string $knownSexType One of the OrdinanceSexType constants
     */
    public function setKnownSexType($knownSexType)
    {
        $this->sexType = $knownSexType;
    }

    /**
     * The participants in this ordinance.
     *
     * @return OrdinanceParticipant[]
     */
    public function getParticipants()
    {
        return $this->participants;
    }

    /**
     * The participants in this ordinance.
     *
     * @param OrdinanceParticipant[] $participants
     */
    public function setParticipants($participants)
    {
        $this->participants = $participants;
    }

    /**
     * Add a participant.
     *
     * @param OrdinanceParticipant $participant
     */
    public function addParticipant($participant)
    {
        if ($this->participants == null) {
            $this->participants = array();
        }
        $this->participants[] = $participant;
    }

    /**
     * Reservation for this ordinance.
     *
     * @return OrdinanceReservation
     */
    public function getReservation()
    {
        return $this->reservation;
    }

    /**
     * Reservation for this ordinance.
     *
     * @param OrdinanceReservation $reservation
     */
    public function setReservation($reservation)
    {
        $this->reservation = $reservation;
    }

    /**
     * Secondary reservation for this ordinance.
     *
     * @return OrdinanceReservation
     */
    public function getSecondaryReservation()
    {
        return $this->secondaryReservation;
    }

    /**
     * Secondary reservation for this ordinance.
     *
     * @param OrdinanceReservation $secondaryReservation
     */
    public function setSecondaryReservation($secondaryReservation)
    {
        $this->secondaryReservation = $secondaryReservation;
    }

    /**
     * Caller reservation for this ordinance.
     *
     * @return OrdinanceReservation
     */
    public function getCallerReservation()
    {
        return $this->callerReservation;
    }

    /**
     * Caller reservation for this ordinance.
     *
     * @param OrdinanceReservation $callerReservation
     */
    public function setCallerReservation($callerReservation)
    {
        $this->callerReservation = $callerReservation;
    }

    /**
     * The code for the temple at which the ordinance was performed.
     *
     * @return string
     */
    public function getTempleCode()
    {
        return $this->templeCode;
    }

    /**
     * The code for the temple at which the ordinance was performed.
     *
     * @param string $templeCode
     */
    public function setTempleCode($templeCode)
    {
        $this->templeCode = $templeCode;
    }

    /**
     * The completion date of this ordinance.
     *
     * @return Date
     */
    public function getCompleteDate()
    {
        return $this->completeDate;
    }

    /**
     * The completion date of this ordinance.
     *
     * @param Date $completeDate
     */
    public function setCompleteDate($completeDate)
    {
        $this->completeDate = $completeDate;
    }

    /**
     * The full name of the person, generally in the native name form.
     *
     * @return string
     */
    public function getFullName()
    {
        return $this->fullName;
    }

    /**
     * The full name of the person, generally in the native name form.
     *
     * @param string $fullName
     */
    public function setFullName($fullName)
    {
        $this->fullName = $fullName;
    }

    /**
     * Returns the associative array for this Ordinance
     *
     * @return array
     */
    public function toArray()
    {
        $a = parent::toArray();
        if ($this->type) {
            $a["type"] = $this->type;
        }
        if ($this->status) {
            $a["status"] = $this->status;
        }
        if ($this->statusReasons) {
            $a["statusReasons"] = $this->statusReasons;
        }
        if ($this->actions) {
            $a["actions"] = $this->actions->toArray();
        }
        if ($this->person) {
            $a["person"] = $this->person->toArray();
        }
        if ($this->sexType) {
            $a["sexType"] = $this->sexType;
        }
        if ($this->participants) {
            $ab = array();
            foreach ($this->participants as $i => $x) {
                $ab[$i] = $x->toArray();
            }
            $a['participants'] = $ab;
        }
        if ($this->reservation) {
            $a["reservation"] = $this->reservation->toArray();
        }
        if ($this->secondaryReservation) {
            $a["secondaryReservation"] = $this->secondaryReservation->toArray();
        }
        if ($this->callerReservation) {
            $a["callerReservation"] = $this->callerReservation->toArray();
        }
        if ($this->templeCode) {
            $a["templeCode"] = $this->templeCode;
        }
        if ($this->completeDate) {
            $a["completeDate"] = $this->completeDate->toArray();
        }
        if ($this->fullName) {
            $a["fullName"] = $this->fullName;
        }
        return $a;
    }

    /**
     * Initializes this Ordinance from an associative array
     *
     * @param array $o
     */
    public function initFromArray(array $o)
    {
        if (isset($o['type'])) {
            $this->type = $o["type"];
            unset($o['type']);
        }
        if (isset($o['status'])) {
            $this->status = $o["status"];
            unset($o['status']);
        }
        if (isset($o['statusReasons'])) {
            $this->statusReasons = $o["statusReasons"];
            unset($o['statusReasons']);
        }
        if (isset($o['actions'])) {
            $this->actions = new OrdinanceActions($o["actions"]);
            unset($o['actions']);
        }
        if (isset($o['person'])) {
            $this->person = new ResourceReference($o["person"]);
            unset($o['person']);
        }
        if (isset($o['sexType'])) {
            $this->sexType = $o["sexType"];
            unset($o['sexType']);
        }
        $this->participants = array();
        if (isset($o['participants'])) {
            foreach ($o['participants'] as $i => $x) {
                $this->participants[$i] = new OrdinanceParticipant($x);
            }
            unset($o['participants']);
        }
        if (isset($o['reservation'])) {
            $this->reservation = new OrdinanceReservation($o["reservation"]);
            unset($o['reservation']);
        }
        if (isset($o['secondaryReservation'])) {
            $this->secondaryReservation = new OrdinanceReservation($o["secondaryReservation"]);
            unset($o['secondaryReservation']);
        }
        if (isset($o['callerReservation'])) {
            $this->callerReservation = new OrdinanceReservation($o["callerReservation"]);
            unset($o['callerReservation']);
        }
        if (isset($o['templeCode'])) {
            $this->templeCode = $o["templeCode"];
            unset($o['templeCode']);
        }
        if (isset($o['completeDate'])) {
            $this->completeDate = new Date($o["completeDate"]);
            unset($o['completeDate']);
        }
        if (isset($o['fullName'])) {
            $this->fullName = $o["fullName"];
            unset($o['fullName']);
        }
        parent::initFromArray($o);
    }

    /**
     * Sets a known child element of Ordinance from an XML reader.
     *
     * @param \XMLReader $xml The reader.
     *
     * @return bool Whether a child element was set.
     */
    protected function setKnownChildElement(\XMLReader $xml)
    {
        $happened = parent::setKnownChildElement($xml);
        if ($happened) {
            return true;
        }
        else if (($xml->localName == 'statusReason') && ($xml->namespaceURI == 'http://familysearch.org/v1/')) {
            if ($this->statusReasons == null) {
                $this->statusReasons = array();
            }
            $this->statusReasons[] = $xml->readString();
            return true;
        }
        else if (($xml->localName == 'actions') && ($xml->namespaceURI == 'http://familysearch.org/v1/')) {
            $this->actions = new OrdinanceActions($xml);
            return true;
        }
        else if (($xml->localName == 'person') && ($xml->namespaceURI == 'http://familysearch.org/v1/')) {
            $this->person = new ResourceReference($xml);
            return true;
        }
        else if (($xml->localName == 'participant') && ($xml->namespaceURI == 'http://familysearch.org/v1/')) {
            if ($this->participants == null) {
                $this->participants = array();
            }
            $this->participants[] = new OrdinanceParticipant($xml);
            return true;
        }
        else if (($xml->localName == 'reservation') && ($xml->namespaceURI == 'http://familysearch.org/v1/')) {
            $this->reservation = new OrdinanceReservation($xml);
            return true;
        }
        else if (($xml->localName == 'secondaryReservation') && ($xml->namespaceURI == 'http://familysearch.org/v1/')) {
            $this->secondaryReservation = new OrdinanceReservation($xml);
            return true;
        }
        else if (($xml->localName == 'callerReservation') && ($xml->namespaceURI == 'http://familysearch.org/v1/')) {
            $this->callerReservation = new OrdinanceReservation($xml);
            return true;
        }
        else if (($xml->localName == 'templeCode') && ($xml->namespaceURI == 'http://familysearch.org/v1/')) {
            $this->templeCode = $xml->readString();
            return true;
        }
        else if (($xml->localName == 'completeDate') && ($xml->namespaceURI == 'http://familysearch.org/v1/')) {
            $this->completeDate = new Date($xml);
            return true;
        }
        else if (($xml->localName == 'fullName') && ($xml->namespaceURI == 'http://familysearch.org/v1/')) {
            $this->fullName = $xml->readString();
            return true;
        }
        return false;
    }

    /**
     * Sets a known attribute of Ordinance from an XML reader.
     *
     * @param \XMLReader $xml The reader.
     *
     * @return bool Whether an attribute was set.
     */
    protected function setKnownAttribute(\XMLReader $xml)
    {
        if (($xml->localName == 'type') && (empty($xml->namespaceURI))) {
            $this->type = $xml->value;
            return true;
        }
        if (($xml->localName == 'status') && (empty($xml->namespaceURI))) {
            $this->status = $xml->value;
            return true;
        }
        if (($xml->localName == 'sexType') && (empty($xml->namespaceURI))) {
            $this->sexType = $xml->value;
            return true;
        }

        return parent::setKnownAttribute($xml);
    }

    /**
     * Writes the contents of this Ordinance to an XML writer. The startElement is expected to be already provided.
     *
     * @param \XMLWriter $writer The XML writer.
     */
    public function writeXmlContents(\XMLWriter $writer)
    {
        if ($this->type) {
            $writer->writeAttribute('type', $this->type);
        }
        if ($this->status) {
            $writer->writeAttribute('status', $this->status);
        }
        if ($this->sexType) {
            $writer->writeAttribute('sexType', $this->sexType);
        }
        if ($this->statusReasons) {
            foreach ($this->statusReasons as $statusReason) {
                $writer->writeElementNs('fs', 'statusReason', null, $statusReason);
            }
        }
        if ($this->actions) {
            $writer->startElementNs('fs', 'actions', null);
            $this->actions->writeXmlContents($writer);
            $writer->endElement();
        }
        if ($this->person) {
            $writer->startElementNs('fs', 'person', null);
            $this->person->writeXmlContents($writer);
            $writer->endElement();
        }
        if ($this->participants) {
            foreach ($this->participants as $participant) {
                $writer->startElementNs('fs', 'participant', null);
                $participant->writeXmlContents($writer);
                $writer->endElement();
            }
        }
        if ($this->reservation) {
            $writer->startElementNs('fs', 'reservation', null);
            $this->reservation->writeXmlContents($writer);
            $writer->endElement();
        }
        if ($this->secondaryReservation) {
            $writer->startElementNs('fs', 'secondaryReservation', null);
            $this->secondaryReservation->writeXmlContents($writer);
            $writer->endElement();
        }
        if ($this->callerReservation) {
            $writer->startElementNs('fs', 'callerReservation', null);
            $this->callerReservation->writeXmlContents($writer);
            $writer->endElement();
        }
        if ($this->templeCode) {
            $writer->writeElementNs('fs', 'templeCode', null, $this->templeCode);
        }
        if ($this->completeDate) {
            $this->completeDate->toXml($writer);
        }
        if ($this->fullName) {
            $writer->writeElementNs('fs', 'fullName', null, $this->fullName);
        }
        parent::writeXmlContents($writer);
    }

    /**
     * Writes this Ordinance to an XML writer.
     *
     * @param \XMLWriter $writer The XML writer.
     * @param bool $includeNamespaces Whether to write out the namespaces in the element.
     */
    public function toXml(\XMLWriter $writer, $includeNamespaces = true)
    {
        $writer->startElementNS('fs', 'ordinance', null);
        if ($includeNamespaces) {
            $writer->writeAttributeNs('xmlns', 'gx', null, 'http://gedcomx.org/v1/');
            $writer->writeAttributeNs('xmlns', 'fs', null, 'http://familysearch.org/v1/');
        }
        $this->writeXmlContents($writer);
        $writer->endElement();
    }
}
