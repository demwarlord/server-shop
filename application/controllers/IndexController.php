<?php

/**
 * IndexController
 *
 * @author dev. Dmitry Kamyshov <dk@company.com>
 * @package Controller
 * @version 2.0.0
 */

namespace Controllers {

    class IndexController
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
                self::$instance = new IndexController();
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
         * All pages common configuration
         * Add user profile in session. Get cart items for all pages
         */
        public function loadAllPagesCommonConfiguration()
        {
            if (!AJAX) {
                // Set up user info
                if (!isset($_SESSION['email_validated'])) {
                    $_SESSION['email_validated'] = false;
                }

                if (!isset($_SESSION['user_validated'])) {
                    $_SESSION['user_validated'] = false;
                }

                if (!isset($_SESSION['user_logged'])) {
                    $_SESSION['user_logged'] = false;
                }

                if (!isset($_SESSION['user_social_logged'])) {
                    $_SESSION['user_social_logged'] = false;
                }

                if (!isset($_SESSION['news_subscribe_email'])) {
                    $_SESSION['news_subscribe_email'] = '';
                }

                if (!isset($_SESSION['min_period'])) {
                    $_SESSION['min_period'] = 1;
                }

                // Set up payment statuses
                if (!empty($_SESSION['order_pay_hash'])) {
                    $result = $this->_api->order(
                        'getPaymentStatus',
                        ['order_pay_hash' => $_SESSION['order_pay_hash']]
                    );

                    if (!empty($result) && $result['errorcode'] == 0) {
                        if ($result['result'] > 1) {
                            \Controllers\AjaxController::getInstance()->checkCompletePaymentAndNewUserLogin();
                            // Set up session
                            \Models\User::clearPaymentSession();
                            // Finalize payment
                            $_SESSION['order_payment_finalize'] = 1;
                        } else {

                            if ($_SESSION['order_pay_expire'] < time()) { // Expired so reset payment procedure
                                \Models\User::clearPaymentSession(false);
                            } else {
                                $_SESSION['order_payment_status'] = $result['result'];
                            }
                        }
                    } else {
                        // TODO:think about what to do if error
                    }
                } else {
                    \Models\User::clearPaymentSession(false);
                }

                // Set up referer
                if (!empty($_SERVER['HTTP_REFERER'])) {
                    $host = parse_url($_SERVER['HTTP_REFERER'])['host'];

                    if ($host == DOMAIN_NAME) {
                        $_SESSION['ref_link'] = $_SERVER['HTTP_REFERER'];
                    }
                } else {
                    $_SESSION['ref_link'] = SITE_URL_ROOT;
                }

                // Set up cart
                \Controllers\AjaxController::getInstance()->getCartItemsAction();

                // Set up smarty template variables
                $this->_tpl->assign('development', DEVELOPMENT);

                $user = new \Models\User();

                $this->_tpl->assign('user_logged', $_SESSION['user_logged']);
                $this->_tpl->assign('user_validated', $_SESSION['user_validated']);
                $this->_tpl->assign('user_social_logged', $_SESSION['user_social_logged']);
                $this->_tpl->assign('order_payment_status', $_SESSION['order_payment_status']);
                $this->_tpl->assign('news_subscribe_email', $_SESSION['news_subscribe_email']);

                if (!empty($_SESSION['user_logged'])) {
                    $this->_tpl->assign('user_profile', $_SESSION['user_logged_profile']);
                } elseif (!empty($_SESSION['user_social_logged'])) {
                    $user_social_profile = $user->getUserSocialProfileForUserProfile(
                        $_SESSION['user_social_profile']['provider_id'],
                        $_SESSION['user_social_profile']['last_id']
                    );
                    $this->_tpl->assign('user_profile', $user_social_profile);
                } else {
                    $this->_tpl->assign('user_profile', []);
                }

                // Set up vars for the footer
                // Here TODO getting latest tweets
                $latest_tweets = [
                    [
                        'text' => 'Confucius: Life is really simple, but we insist on making it complicated.',
                        'tag'  => '<a href="#">#famousquotes</a>',
                        'time' => '8 mins ago'
                    ],
                    [
                        'text' => 'host is best 4ever',
                        'tag'  => '<a href="#">#hosting</a> <a href="#">#bestservices</a>',
                        'time' => '2 days ago'
                    ],
                ];
                $this->_tpl->assign('latest_tweets', $latest_tweets);

                // Set up seo variables for index page
                $hreflang = [];
                $supported_languages_info = unserialize(SITE_LANGUAGES);

                if (!empty($supported_languages_info)) {
                    foreach ($supported_languages_info as $supported_language => $info) {
                        if ($_SESSION['user']['lang'] !== $supported_language) {
                            if ($supported_language !== DEFAULT_LANGUAGE) {
                                $hreflang[] = [
                                    'href' => \System\Helper::addLanguageToURL($supported_language, \System\FrontController::getInstance()->getURL()),
                                    'lang' => $supported_language
                                        ];
                            } else {
                                $hreflang[] = [
                                    'href' => \System\Helper::removeLanguageFromURL(\System\FrontController::getInstance()->getURL()),
                                    'lang' => $supported_language
                                        ];
                                }
                        }
                        if ($_SESSION['user']['lang'] === $supported_language) {
                            $_SESSION['user']['locale'] = $info['locale'];
                            $this->_tpl->assign('locale', $info['locale']);
                        }
                    }
                } else {
                    // Fatal error. Supported languages are not set
                    throw new \Exception('IndexController: Supported languages are not set', 99999);
                }
                $this->_tpl->assign('hreflang', $hreflang);

            }

            // For all requests AJAX and !AJAX
            // Initialize JS variables
            $_SESSION['site_js_config']['lang'] = $_SESSION['user']['lang'];

            $_SESSION['site_js_config']['domain_name'] = DOMAIN_NAME;
            $_SESSION['site_js_config']['domain_www'] = URL_WWW;
            $_SESSION['site_js_config']['domain_manage'] = URL_MANAGE;
            $_SESSION['site_js_config']['domain_my'] = URL_MY;
            $_SESSION['site_js_config']['domain_gateway'] = URL_GATEWAY;
            $_SESSION['site_js_config']['domain_pay'] = URL_PAY;

            // Set up language seo variables
            $base_href = PROTO . DOMAIN_NAME . ($_SESSION['user']['lang'] === DEFAULT_LANGUAGE ? '' : '/' . $_SESSION['user']['lang']);
            $this->_tpl->assign('base_href', $base_href);
            \Models\Registry::set('base_href', $base_href);

        }

        /**
         * Main page.
         */
        public function mainAction()
        {
            $page = new \Models\Page();
            $main_page_features = $page->getPageBySlug('main_page_features');
            $main_page_facts = $page->getPageBySlug('main_page_facts');
            $main_page_reviews = $page->getPageBySlug('main_page_reviews');

            if (!empty($main_page_features) && !empty($main_page_facts) && !empty($main_page_reviews)) {
                $this->_tpl->assign("main_page_features", $main_page_features['content']);
                $this->_tpl->assign("main_page_facts", $main_page_facts['content']);
                $this->_tpl->assign("main_page_reviews", $main_page_reviews['content']);
            } else {
                throw new \Exception('IndexController: Page does not exist', 404);
            }
        }

        /**
         * Setting template language.
         */
        public function userLanguageAction()
        {
            if (isset($_REQUEST[0]) && count($_REQUEST) === 1 && strlen($_REQUEST[0]) === 2) {
                $check_language = \System\Helper::isLanguage($_REQUEST[0]);
                if ($check_language !== false) {
                    $_SESSION['user']['lang'] = $check_language;
                    // New language is set so we remove old lang from URL and put new lang and get user back
                    \System\Helper::redirect(\System\Helper::removeLanguageFromURL($_SESSION['ref_link']));
                }
            }
            // throw new \Exception('IndexController: The language is not supported', 404);
            // New language is not set because of error so we redirect user back with selected language
            \System\Helper::redirect($_SESSION['ref_link']);
        }

        /**
         * Geting captcha.
         */
        public function getCaptchaAction()
        {
            require_once (LIBS_DIR . 'securimage/securimage.php');

            $img = new \Securimage();
            $img->image_height = 70;
            $img->image_width = $img->image_height * M_E;
            $img->show();

            die();
        }

        public function clearRedisCacheAction()
        {
            $keys = \Models\Cache::getInstance()->getKeys('shop');

            if (!empty($keys) && is_array($keys)) {
                \Models\Cache::getInstance()->deleteKeys($keys);
                \System\Helper::redirect('/');
            } else {
                throw new \Exception('IndexController: redis is empty', 404);
            }
        }

    }
}
