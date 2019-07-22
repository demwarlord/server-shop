<?php

/**
 * Cache class (Error codes 24xxx)
 *
 * @author dev. Dmitry Kamyshov <dk@company.com>
 * @package Model
 * @subpackage common
 * @version 2.0.0
 */

namespace Models {

    class Cache
    {

        /**
         * Shared object class instance
         *
         * @var object|null
         */
        static private $instance = null;

        /**
         * redis connection
         *
         * @var resource
         */
        protected $_connection = '';


        public static function getInstance()
        {
            if (self::$instance == null) {
                self::$instance = new Cache();
            }

            return self::$instance;
        }

        /**
         * Constructor
         *
         * @return resource
         */
        private function __construct()
        {
            try {
                $redis = new \Redis();
                $redis->connect(REDIS_SERVER);
                $this->_connection = $redis;
            } catch (Exception $e) {
                throw new \Exception("CacheModel: Error connecting to server", 24204);
            }

            return $this->_connection;
        }

        public function get($key)
        {
            $data = $this->_connection->get($key);
            if (!empty($data)) {
                return unserialize($data);
            } else {
                return false;
            }
        }

        public function set($key, $data, $expire = 60)
        {
            if (REDIS_CACHING) {
                $result = $this->_connection->set($key, serialize($data));
                $this->_connection->expire($key, $expire);

                return $result;
            } else {
                return false;
            }
        }

        public function exists($key)
        {
            if (REDIS_CACHING) {
                return $this->_connection->exists($key);
            } else {
                return false;
            }
        }

        public function flushAll()
        {
            return $this->_connection->flushAll();
        }

        public function deleteKeys($keys)
        {
            if (!empty($keys) && is_array($keys)) {
                return $this->_connection->delete($keys);
            }
        }

        public function getKeys($prefix)
        {
            return $this->_connection->keys($prefix . '*');
        }
    }
}
