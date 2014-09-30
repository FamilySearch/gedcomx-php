<?php

namespace Gedcomx\Common;

interface Attributable {

    /**
     * Attribution metadata for a genealogical resource. Attribution data is necessary to support
     * a sound <a href="https://wiki.familysearch.org/en/Genealogical_Proof_Standard">genealogical proof statement</a>.
     *
     * @return Attribution metadata for a genealogical resource. Attribution data is necessary to support
     * a sound <a href="https://wiki.familysearch.org/en/Genealogical_Proof_Standard">genealogical proof statement</a>.
     */
    public function getAttribution();

    /**
     * Attribution metadata for a genealogical resource. Attribution data is necessary to support
     * a sound <a href="https://wiki.familysearch.org/en/Genealogical_Proof_Standard">genealogical proof statement</a>.
     *
     * @param attribution Attribution metadata for a genealogical resource. Attribution data is necessary to support
     * a sound <a href="https://wiki.familysearch.org/en/Genealogical_Proof_Standard">genealogical proof statement</a>.
     */
    public function setAttribution(Attribution $attribution);
} 