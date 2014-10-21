<?php

    namespace Gedcomx\Common;

    use Gedcomx\Source\SourceReference;

    class CustomEntity
    {
        const JSON_INDENTIFIER = 'customEntities';

        private $id;
        private $refToSomething;
        private $uniqueKeyedItems;
        private $keyedItems;
        private $source;

        public function __construct($id)
        {
            $this->id = $id;
        }

        public function getId()
        {
            return $this->id;
        }

        public function setId($id)
        {
            $this->id = $id;
        }

        public function getRefToSomething()
        {
            return $this->refToSomething;
        }

        public function setRefToSomething($refToSomething)
        {
            $this->refToSomething = $refToSomething;
        }

        public function getKeyedItems()
        {
            return $this->keyedItems;
        }

        public function setKeyedItems(array $keyedItems)
        {
            $this->keyedItems = $keyedItems;
        }

        public function getUniqueKeyedItems()
        {
            return $this->uniqueKeyedItems;
        }

        public function setUniqueKeyedItems(array $uniqueKeyedItems)
        {
            $this->uniqueKeyedItems = $uniqueKeyedItems;
        }

        public function getSource()
        {
            return $this->source;
        }

        public function setSource(SourceReference $source)
        {
            $this->source = $source;
        }
    }
