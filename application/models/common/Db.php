<?php

/**
 * Db class
 *
 * @author dev. Dmitry Kamyshov <dk@company.com>
 * @package Model
 * @subpackage common
 * @version 2.0.0
 */

namespace Models {

    class Db {

        /**
         * Shared object class instance
         *
         * @var object|null
         */
        static private $instance = NULL;

        /**
         * Count of requests to DB
         *
         * @var int
         */
        protected $queriesNum = 0;

        /**
         * Total time database queries
         *
         * @var float
         */
        protected $queriesTime = 0;

        /**
         * Database connection
         *
         * @var resource
         */
        protected $connect = '';

        /**
         * Pattern of "Singleton"
         *
         * @return string object copy
         */
        static function getInstance() {
            if (self::$instance == NULL) {
                self::$instance = new DB(
                        DB_HOST, DB_USER, DB_PASS, DB_NAME
                );
            }

            return self::$instance;
        }

        /**
         * Constructor
         *
         * @param string $host
         * @param string $user
         * @param string $pass
         * @param string $dbname
         * @return resource
         */
        private function __construct($host, $user, $pass, $dbname) {
            try {
                $this->connect = mysqli_connect($host, $user, $pass, $dbname);

                if (mysqli_connect_errno()) {
                    throw new \Exception("DB: Error connecting to DB:".mysqli_error($this->connect), 10003);
                }

                $this->query("SET NAMES UTF8");
            } catch (Exception $e) {
                echo $e->getMessage();
            }

            return $this->connect;
        }

        /**
         * Protection against cloning facility without first creating an instance of it
         *
         * @return void
         */
        private function __clone() {

        }

        /**
         * Get all rows as an assoc array
         *
         * @param string $query
         * @param bool $use_cache
         * @return array|false
         */
        public function getAll($query, $use_cache = false) {
            if ($use_cache) {
                $result = $this->getFromCache($query);

                if ($result !== false) {
                    return $result;
                }
            }

            $result = $this->query($query);

            if ($result) {
                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        $selectRes[] = $row;
                    }

                    mysqli_free_result($result);

                    if ($use_cache) {
                        $this->saveToCache($query, $selectRes);
                    }

                    return $selectRes;
                }
            }
            return false;
        }

        /**
         * Get a result row as an enumerated array
         *
         * @param string $query
         * @param bool $use_cache
         * @return array|false
         */
        public function getOneRow($query, $use_cache = false) {
            if ($use_cache) {
                $result = $this->getFromCache($query);

                if ($result !== false) {
                    return $result;
                }
            }

            $result = $this->query($query);

            if ($result) {
                if (mysqli_num_rows($result) > 0) {
                    $row = mysqli_fetch_row($result);

                    if ($use_cache) {
                        $this->saveToCache($query, $row);
                    }

                    return $row;
                }
            }

            return false;
        }

        /**
         * Fetch a result row as an associative array
         *
         * @param string $query
         * @param bool $use_cache
         * @return array|false
         */
        public function getOneRowAssoc($query, $use_cache = false) {
            if ($use_cache) {
                $result = $this->getFromCache($query);

                if ($result !== false) {
                    return $result;
                }
            }

            $result = $this->query($query);

            if ($result) {
                if (mysqli_num_rows($result) > 0) {
                    $row = mysqli_fetch_assoc($result);

                    if ($use_cache) {
                        $this->saveToCache($query, $row);
                    }

                    return $row;
                }
            }

            return false;
        }

        /**
         * Fetch a first el of a result row
         *
         * @param string $query
         * @param bool $use_cache
         * @return string|false
         */
        public function getOne($query, $use_cache = false) {
            if ($use_cache) {
                $result = $this->getFromCache($query);

                if ($result !== false) {
                    return $result;
                }
            }

            $result = $this->query($query);

            if ($result) {
                if (mysqli_num_rows($result) > 0) {
                    $row = $result->fetch_assoc();
                    $keys = array_keys($row);
                    mysqli_free_result($result);

                    if ($use_cache) {
                        $this->saveToCache($query, $row[$keys[0]]);
                    }

                    return $row[$keys[0]];
                }
            }

            return false;
        }

        /**
         * Performs a query on the database
         *
         * @param string $query
         * @return bool
         */
        public function query($query) {
            $this->queriesNum++;
            $start_time = microtime(true);
            $result = mysqli_query($this->connect, $query);
            $this->setQueriesTime(microtime(true) - $start_time);

            return $result;
        }

        /**
         * Performs a query on the database
         *
         * @param string $query
         * @return bool
         */
        public function multyQuery($query) {
            $this->queriesNum++;
            $start_time = microtime(true);
            $result = mysqli_multi_query($this->connect, $query);
            $this->setQueriesTime(microtime(true) - $start_time);

            return $result;
        }

        /**
         * Inserts a row into a database
         *
         * @param string $query
         * @return string|false
         */
        public function insert($query) {
            $result = $this->query($query);

            if ($result) {
                return mysqli_insert_id($this->connect);
            }

            return false;
        }

        /**
         * Performs an update query on the database
         *
         * @param string $query
         */
        public function update($query) {
            $result = $this->query($query);

            if ($result) {
                return $result;
            } else {
                return false;
            }
        }

        /**
         * Gets the number of rows in a result
         *
         * @param string $query
         * @return int
         */
        public function getNumRows($query) {
            $result = $this->query($query);

            if ($result) {
                return mysqli_num_rows($result);
            }

            return false;
        }

        /**
         * Gets the number of affected rows in a previous MySQL operation
         *
         * @return int
         */
        public function getAffectedRows() {
            return mysqli_affected_rows($this->connect);
        }

        /**
         * Increases queries time
         *
         * @param float
         */
        public function setQueriesTime($time) {
            $this->queriesTime += $time;
        }

        /**
         * Gets queries time
         *
         * @return string
         */
        public function getQueriesTime() {
            return sprintf("%01.4f", $this->queriesTime);
        }

        /**
         * Gets queries number
         *
         * @return int
         */
        public function getQueriesNum() {
            return $this->queriesNum;
        }

        /**
         * Escapes special characters in a string for use in an SQL statement, taking into account the current charset of the connection
         *
         * @param string $str
         * @return string
         */
        public function escapeData($str) {
            return mysqli_real_escape_string($this->connect, trim($str));
        }

        /**
         * Returns a string description of the last error
         *
         * @return string
         */
        public function getError() {
            return mysqli_error($this->connect);
        }

        /**
         * Destructor
         */
        public function __destruct() {
            if (!mysqli_close($this->connect)) {

            }
        }

        /**
         * Gets data from REDIS cache if exists
         *
         * @param string $sql
         * @return bool|array
         */
        private function getFromCache($sql)
        {
            $cache_key = "shop_query_" . md5($sql);

            if (\Models\Cache::getInstance()->exists($cache_key)) {
                $result = \Models\Cache::getInstance()->get($cache_key);
            } else {
                $result = false;
            }

            return $result;
        }

        /**
         * Saves the data into REDIS cache
         *
         * @param string $sql
         * @param array $data
         */
        private function saveToCache($sql, $data)
        {
            $cache_key = "shop_query_" . md5($sql);
            \Models\Cache::getInstance()->set($cache_key, $data, 3600 * 24 * 7);
        }

    }
}