<?php

/**
 * ErrorController
 *
 * @author dev. Dmitry Kamyshov <dk@company.com>
 * @package Controller
 * @subpackage System
 * @version 2.0.0
 */

namespace System {

    class ErrorController {

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
        static function getInstance() {
            if (self::$instance == null) {
                self::$instance = new ErrorController();
            }
            return self::$instance;
        }

        /**
         * Constructor
         *
         */
        private function __construct() {

        }

        public function HandleError(\Exception $e = null) {
            if ($e->getCode() === 99999) {
                // Fatal Error. Can't continue do anything
                if (DEVELOPMENT) {
                    if (DEBUG_MAIL) {
                        mail(DEBUG_MAIL, DOMAIN_NAME . ': Fatal Error 99999', print_r($e, true));
                    }
                    error_log("Exception Error 99999: " . $e->getMessage());
                }
                die('Fatal Error');

            } elseif ($e->getCode() === 404) {
                if (DEVELOPMENT) {
                    if (DEBUG_MAIL) {
                        mail(DEBUG_MAIL, DOMAIN_NAME . ': Exception Error 404', print_r($e, true));
                    }
                    error_log("Exception Error 404: " . $e->getMessage());
                }
                \System\Helper::redirect404();
            } else {
                if (DEBUG_MAIL) {
                    mail(DEBUG_MAIL, DOMAIN_NAME . ': Exception Error /CODE: ' . $e->getCode(), print_r($e, true));
                }

                error_log("Exception Error: " . $e->getMessage() . " /CODE: " . $e->getCode());
                \System\Helper::redirectSystemError($e->getCode());
            }
        }

        // TODO ATC
        public function HandleAjaxError(\Exception $e = null) {
            if ($e->getCode() === 99999) {
                // Fatal Error. Can't continue do anything
                if (DEVELOPMENT) {
                    if (DEBUG_MAIL) {
                        mail(DEBUG_MAIL, DOMAIN_NAME . ': Ajax Fatal Error 99999', print_r($e, true));
                    }
                    error_log("Ajax Exception Error 99999: " . $e->getMessage());
                }

                die('Fatal Error');
            } elseif ($e->getCode() === 404) {
                if (DEVELOPMENT) {
                    if (DEBUG_MAIL) {
                        mail(DEBUG_MAIL, DOMAIN_NAME . ': Ajax Exception Error 404', print_r($e, true));
                    }
                    error_log("Ajax Exception Error 404: " . $e->getMessage());
                }
            } else {
                if (DEBUG_MAIL) {
                    mail(DEBUG_MAIL, DOMAIN_NAME . ': Ajax Exception Error /CODE: ' . $e->getCode(), print_r($e, true));
                }
                error_log("Ajax Exception Error: " . $e->getMessage() . " /CODE: " . $e->getCode());
            }

            echo 0; // Result to ajax request
        }

    }

}