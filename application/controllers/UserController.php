<?php

/**
 * UsersController
 *
 * @author dev. Dmitry Kamyshov <dk@company.com>
 * @package Controller
 * @version 2.0.0
 */

namespace Controllers {

    class UserController
    {

        /**
         * Smarty object.
         *
         * @var object
         */
        private $_tpl = '';

        /**
         * Api object.
         *
         * @var object
         */
        private $_api = '';

        /**
         * @var object|null
         */
        static private $instance = null;

        /**
         * @return object
         */
        static function getInstance()
        {
            if (self::$instance == null) {
                self::$instance = new UserController();
            }
            return self::$instance;
        }

        /**
         * Constructor
         */
        private function __construct()
        {
            $this->_tpl = \Models\Registry::get('tpl');
            $this->_api = \Models\Api::getInstance();
        }

        /**
        * Main action. Called when another method is not specified.
        */
        public function mainAction()
        {
            throw new \Exception('UserController: No main action is set', 404);
        }

        public function loginAction()
        {
            if (!empty($_SESSION['user_logged'])) {
                \System\Helper::redirect('/dashboard/overview/');
            }

            if (!empty($_POST['login']) && !empty($_POST['password'])) {
                $params = [
                    'login' => $_POST['login'],
                    'password' => $_POST['password']
                ];
                $params['ip'] = $_SERVER['REMOTE_ADDR'];
                $params['agent'] = $_SERVER['HTTP_USER_AGENT'];
                $params['site'] = 'company.com';
                $result = $this->_api->user('loginUser', $params);

                if (!empty($result) && $result['errorcode'] == 0) {
                    if (!empty($result['result']['data']) && !isset($result['result']['data']['auth_error'])) {

                        // set up session
                        \Models\User::clearValidatedProfile();
                        \Models\User::clearSocialProfile();
                        $_SESSION['user_logged'] = true;
                        $_SESSION['user_logged_profile'] = $result['result']['data'];
                        \Models\User::setUserInfo();

                        if (!empty($_SESSION['cart'])) {
                            \System\Helper::redirect('/order/');
                        } else {
                            \System\Helper::redirect('/dashboard/overview/');
                        }
                    }
                }

                $this->_tpl->assign("wrong_login", 1);

            } else {
                $this->_tpl->assign("wrong_login", 0);
            }
        }

        public function logoutAction()
        {
            if (!empty($_SESSION['user_logged'])) {

                $params['hash'] = $_SESSION['user_logged_profile']['hash'];
                $result = $this->_api->user('logoutUser', $params);

                if (!empty($result) && $result['errorcode'] == 0 &&
                        empty($result['result']['data']['status']) &&
                        $_SESSION['user_logged_profile']['hash'] == $result['result']['data']['hash']) {

                    // set up session
                    \Models\User::clearLoggedProfile();
                    \Models\User::clearPaymentSession();

                    // TODO: think about this
                    if (SetCookie('login', '', time('now') - 60, "/")) {
                        \System\Helper::redirect(SITE_URL_ROOT);
                        return;
                    }
                }
            }
            \System\Helper::redirect($_SESSION['ref_link']);
            return;
        }

        public function forgotpasswordAction()
        {
            if (!empty($_SESSION['user_logged'])) {
                \System\Helper::redirect('/dashboard/overview/');
            }
        }
    }
}
