<?php

    namespace Gedcomx\Rs\Client\Options;

    use Guzzle\Http\Message\Request;

    class QueryParameter implements StateTransitionOption
    {
        const ACCESS_TOKEN = "access_token";
        const COUNT = "count";
        const GENERATIONS = "generations";
        const SEARCH_QUERY = "q";
        const START = "start";

        private $replace;
        private $name;
        private $value;

        /**
         * @param boolean $replace
         * @param string  $name
         * @param string  $value,...
         */
        public function __construct($replace, $name, $value)
        {
            $this->replace = $replace;
            $this->name = $name;
            if (func_num_args() > 3) {
                $args = func_get_args();
                array_shift(array_shift($args));
                $this->value = $args;
            } else {
                $this->value = $value;
            }
        }

        public function apply(Request $request)
        {
            $query = $request->getQuery(false);
            $query->add( $this->name, $this->value );
        }

        public static function accessToken($value)
        {
            return new QueryParameter(true, self::ACCESS_TOKEN, $value);
        }

        public static function count($value)
        {
            return new QueryParameter(true, self::COUNT, $value);
        }

        public static function generations($value)
        {
            return new QueryParameter(true, self::GENERATIONS, $value);
        }

        public static function searchQuery($value)
        {
            return new QueryParameter(true, self::SEARCH_QUERY, $value);
        }

        public static function start($value)
        {
            return new QueryParameter(true, self::START, $value);
        }

    }