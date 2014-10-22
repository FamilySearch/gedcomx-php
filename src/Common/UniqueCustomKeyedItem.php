<?php


    namespace Gedcomx\Common;

    use Gedcomx\Support\HasJsonKey;

    class UniqueCustomKeyedItem implements HasJsonKey
    {

        private $key;
        private $val1;
        private $val2;

        public function __construct()
        {
            $this->key = guid();
        }

        /**
         * @return bool
         */
        public function isHasUniqueKey()
        {
            return true;
        }

        public function getJsonKey()
        {
            return $this->getKey();
        }

        public function setJsonKey($jsonKey)
        {
            $this->setKey($jsonKey);
        }

        public function getKey()
        {
            return $this->key;
        }

        public function setKey($key)
        {
            $this->key = $key;
        }

        public function getVal1()
        {
            return $this->val1;
        }

        public function setVal1($val1)
        {
            $this->val1 = $val1;
        }

        public function getVal2()
        {
            return $this->val2;
        }

        public function setVal2($val2)
        {
            $this->val2 = $val2;
        }
    }
