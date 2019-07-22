<?php

/**
 * Api model
 *
 * @author dev. Dmitry Kamyshov <dk@company.com>
 * @package Model
 * @subpackage common
 * @version 2.0.0
 */

namespace Models {

    class Api
    {

        /**
         * Gateway API URL
         *
         * @var string
         */
        private $url = URL_CORE_API;

        /**
         * CURL Handler
         *
         * @var reference
         */
        private $ch = null;

        /**
         * Shared object class instance
         *
         * @var string
         */
        static private $instance = null;

        /**
         * Use shared object instance instead of creating multiple instanses.
         *
         * @return object
         */
        public static function getInstance()
        {
            if (self::$instance == null) {
                self::$instance = new Api();
            }

            return self::$instance;
        }

        /**
         * Contructor
         *
         */
        public function __construct()
        {
            $this->ch = curl_init();
            curl_setopt($this->ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
            curl_setopt($this->ch, CURLOPT_URL, $this->url);
            curl_setopt($this->ch, CURLOPT_USERAGENT, 'Manage API Client 1.0');
            curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($this->ch, CURLOPT_POST, 1);
        }

        public function __call($name, $arguments)
        {
            $request = (empty($arguments[1])) ? array() : $arguments[1];
            $request['controller'] = $name;

            if (empty($arguments[0])) {
                throw new \Exception('Api: Action is not defined', 10002);
            }

            $request['action'] = $arguments[0];
            curl_setopt($this->ch, CURLOPT_POSTFIELDS, json_encode($request));
            $result = curl_exec($this->ch);

            if (curl_errno($this->ch)) {
                error_log(curl_error($this->ch));
            }

            $result = json_decode($result, true);


            if (!empty($result['errorcode'])) {
                error_log($result['errorcode'] . ':' . $result['error']);
            }

            return $result;
        }

        /**
         * Cleaner
         *
         */
        public function __destruct()
        {
            curl_close($this->ch);
        }
    }
}