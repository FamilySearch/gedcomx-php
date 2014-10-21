<?php

    namespace Gedcomx\Common;

    interface HasTransientProperties
    {
        /**
         * Get the transient properties.
         *
         * @return the transient properties.
         */
        public function getTransientProperties();

        /**
         * Get a transient (non-serialized) property.
         *
         * @param string name The name of the property.
         *
         * @return mixed The property.
         */
        public function getTransientProperty($name);

        /**
         * Set a transient (non-serialized) property.
         *
         * @param string name the name of the property.
         * @param mixed  value the property value.
         */
        public function setTransientProperty($name, $value);
    }