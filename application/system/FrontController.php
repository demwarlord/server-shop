<?php

/**
 * FrontController
 *
 * Language support included.
 *
 * @author dev. Dmitry Kamyshov <dk@company.com>
 * @package Controller
 * @subpackage System
 * @version 2.0.0
 */

namespace System {

    class FrontController
    {

        /**
         * Shared object class instance
         *
         * @var string
         */
        static private $instance = null;

        /**
         * Custom routes
         *
         * @var array
         */
        private $_custom_routes = [];

        /**
         * Request data
         *
         * @var array
         */
        private $_request = [];

        /**
         * Redirect data
         *
         * @var string
         */
        private $_language_redirect = '';

        /**
         * Current URL
         *
         * @var string
         */
        private $_current_url = '';

        /**
         * Current canonical URL
         *
         * @var string
         */
        private $_current_canonical_url = '';

        /**
         * Use shared object instance instead of creating multiple instanses.
         *
         * @return object
         */
        static function getInstance()
        {

            if (self::$instance == null) {
                self::$instance = new FrontController();
            }
            return self::$instance;
        }

        /**
         * Constructor
         */
        private function __construct()
        {
            $this->_custom_routes = \Models\Registry::get('custom_routes');
        }

        /**
         * Set current URL
         *
         * @param string $url
         */
        public function setURL($url)
        {
            $this->_current_url = \System\Helper::normalizeURL($url);
        }

        /**
         * Set current canonical URL
         *
         * @param string $url
         */
        public function setCanonicalURL($url)
        {
            $this->_current_canonical_url = \System\Helper::normalizeURL(\System\Helper::removeQueryParametersFromURL($url));
        }

        /**
         * Get current URL
         *
         * @return string
         */
        public function getURL()
        {
            return $this->_current_url;
        }

        /**
         * Get current canonical URL
         *
         * @return string
         */
        public function getCanonicalURL()
        {
            return $this->_current_canonical_url;
        }

        /**
         * Pre-core process set routing, language
         *
         * @param string $controller
         * @param string $action
         * @param array $params
         * @throws \Exception
         */
        public function setRouting($controller = '', $action = '', $params = [])
        {
            $this->_language_redirect = '';
            $this->setURL($_SERVER['REQUEST_URI']);
            $this->setCanonicalURL($_SERVER['REQUEST_URI']);
            $this->_request = $this->_getRequest($controller, $action, $params);
        }

        /**
         * Core process
         *
         * @throws \Exception
         */
        public function executeAction()
        {
            $tpl = \Models\Registry::get('tpl');

            // Check input parameters for controller and action names
            if (empty($this->_request)) {
                // Fatal error. Routing is not set
                throw new \Exception('FrontController: Routing is not set', 10000);
            }

            $controller = $this->_request['controller'];
            $action = $this->_request['action'];

            // Check if the requested controller exists
            if (!file_exists(CONTROLLERS_DIR . ucfirst($controller) . 'Controller.php')) {
                throw new \Exception(
                    'FrontController: Can\'t find controller: '
                        . CONTROLLERS_DIR . ucfirst($controller)
                        . 'Controller.php [URL:'.$_SERVER['REQUEST_URI'].']',
                    404
                );
            }

            \Models\Registry::set('controller', $controller);
            $controller_name = '\\Controllers\\' . ucfirst($controller) . 'Controller';

            // Get the requested controller object
            if (is_callable($controller_name . '::getInstance')) {
                $actual_controller = $controller_name::getInstance();
            } else {
                throw new \Exception(
                    'FrontController: Can\'t call controller: ' . $controller_name,
                    10001
                );
            }

            // Check if the requested action exists
            $action_name = $action . "Action";

            error_log(explode('\\', $controller_name)[2] . "::" . $action_name);

            if (!method_exists($actual_controller, $action_name)) {
                error_log($controller_name . "::" . $action_name . " -> !method_exists");
                throw new \Exception(
                    'FrontController: Can\'t find method: ' . $controller_name . "::" . $action_name,
                    404
                );
            }

            \Models\Registry::set('action', $action);
            $tpl->assign('actionName', $action);
            $tpl_name = $action . ".tpl";

            // Assign calculated values to the template
            $tpl->assign('controller', $controller);
            $tpl->assign('section', $controller . ":" . $action);

            if ($controller !== 'index') {
                $tpl_name = $controller . "/" . $tpl_name;
            }

            $tpl->assign('action', $tpl_name);

            $seo = \System\Helper::seo($action, $controller, $this->_request['params']);

            if (!empty($seo)) {
                $tpl->assign('seo', $seo);
            }

            $tpl->assign('canonical_url', $this->_current_canonical_url);

            // Execute controller action
            call_user_func(array($actual_controller, $action_name));
        }

        /**
         * Search custom pattern in URI
         *
         * @param array $path_tokens
         * @return array
         */
        private function _searchCustomURI(&$path_tokens)
        {
            $custom_route = [];
            $uri = '/' . implode('/', $path_tokens) . '/';
            foreach ($this->_custom_routes as $key => $value) {
                $needle = '/' . strtolower(trim($key, '/')) . '/';
                $pos = stripos($uri, $needle);
                if ($pos !== false && $pos === 0) {
                    $custom_route = $value;
                    $uri = substr($uri, strlen($needle));
                    $path_tokens = array_filter(explode('/', trim($uri, '/')), 'strlen');
                    break;
                }
            }
            return $custom_route;
        }

        /**
         * Search custom params in the final $params and process them
         *
         * @param array $request
         */
        private function _processCustomParams(&$request)
        {
            $custom_params = \Models\Registry::get('custom_params');
            if (!empty($custom_params)) {

                if (!empty($request)) {
                    foreach ($request as $key => $param) {
                        foreach ($custom_params as $key_custom => $custom_param) {

                            if ($key_custom === $key) {
                                error_log('FrontController::_processCustomParams: Custom param found and deleting: '.$key_custom);
                                $this->_current_url = \System\Helper::removeQueryParameterFromURL($this->_current_url, $key);

                                if (!empty($custom_param['action'])) {
                                    if (is_callable([$this, 'custom_param_handler__'.$custom_param['action']])) {
                                        // Execute custom action
                                        call_user_func([$this, 'custom_param_handler__'.$custom_param['action']], $param);
                                    } else {
                                        error_log('FrontController::_processCustomParams: Custom param action is not callable: '.$custom_param['action']);
                                    }
                                }
                                unset($request[$key]);
                            }
                        }
                    }
                }
            }
        }

        /**
         * Language set based on browser headers HTTP_ACCEPT_LANGUAGE
         */
        private function _setSiteLanguageByBrowserHeader()
        {
            if(isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) && !isset($_SESSION['user']['lang'])) {
                // TODO: Full parsing HTTP_ACCEPT_LANGUAGE
                // (http://stackoverflow.com/questions/2316476/how-to-get-the-language-value-from-serverhttp-accept-language-using-php)
                $lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
                $supported_languages_info = unserialize(SITE_LANGUAGES);

                // Set default language
                $_SESSION['user']['lang'] = DEFAULT_LANGUAGE;
                if (!empty($supported_languages_info)) {
                    foreach ($supported_languages_info as $supported_language => $info) {
                        if (in_array($lang, $info['suitable'])) {
                            $_SESSION['user']['lang'] = $supported_language;
                                $this->_language_redirect = $this->_current_url;
                            }
                        }
                } else {
                    // Fatal error. Supported languages are not set
                    throw new \Exception('FrontController: Supported languages are not set', 99999);
                }
            } elseif (!isset($_SESSION['user']['lang'])) {
                $_SESSION['user']['lang'] = DEFAULT_LANGUAGE;

            } elseif (isset($_SESSION['user']['lang']) && $_SESSION['user']['lang'] !== DEFAULT_LANGUAGE && !AJAX) {
                $this->_language_redirect = $this->_current_url;
            }
        }

        /**
         * Sets site language if there is apropriate token in URL is set
         *
         * @param string $token
         * @return bool
         */
        private function _setSiteLanguageByURIToken($token)
        {
            if (strlen($token) === 2) {
                $supported_languages_info = unserialize(SITE_LANGUAGES);
                if (!empty($supported_languages_info)) {
                    foreach ($supported_languages_info as $supported_language => $info) {
                        if ($token === $supported_language) {
                            $_SESSION['user']['lang'] = $supported_language;
                            if ($supported_language === DEFAULT_LANGUAGE) {
                                // We have met default lang ("en") in URL. Do redirect that will remove it from the URL
                                $this->_language_redirect = $this->_current_url;
                            }
                            return true;
                        }
                    }
                } else {
                    // Fatal error. Supported languages are not set
                    throw new \Exception('FrontController: Supported languages are not set', 99999);
                }
            }
            // If language is not set but differs from default then we must set it to URL (exept AJAX)
            if (isset($_SESSION['user']['lang']) && $_SESSION['user']['lang'] !== DEFAULT_LANGUAGE && !AJAX) {
                $this->_language_redirect = $this->_current_url;
            }
            return false;
        }

        /**
         * Parse request URI to find passed parameters
         *
         * URI format:
         *
         * /[ru/]controller/[action/][param1/][param2/][?param3_name=param3_value]...
         * /[ru/]custom-uri/[param1/][param2/][?param3_name=param3_value]...
         *
         * @return array
         */
        private function _getRequest($controller = '', $action = '', $params = [])
        {
            // Initial and default settings
            $request = [];
            $request['params'] = [];
            $request['controller'] = 'index';
            $request['action'] = 'main';
            $language_is_set = false;

            // If settings come from func params
            if (!empty($controller)) {
                $request['controller'] = $controller;

                if (!empty($action)) {
                    $request['action'] = $action;
                }
                $request['params'] = $params;

                // If we have internal routing (404 and Syserror) we do not redirect
                $language_is_set = true;
            } else {
                $uri = $this->_current_url;

                // Take path (before '?') from URI separate
                $parsed_uri_path = parse_url($uri, PHP_URL_PATH);

                // Parse URI
                $path_tokens = explode('/', trim($parsed_uri_path, '/'));

                if (empty($path_tokens[0])) {
                    $path_tokens = [];
                } elseif (count(array_filter($path_tokens, 'strlen')) != count($path_tokens)) {
                    // If we met nothing between slashes (i.e. "//")
                    // throw new \Exception('FrontController: Empty parameters in URI: ' . $_SERVER['REQUEST_URI'], 404);
                    // Redirect to root
                    \System\Helper::redirect(SITE_URL_ROOT);
                }

                // Let's see what come from parameter uri (i.e. after "?")
                if (isset($_REQUEST['controller']) && empty($path_tokens)) {
                    // We have just parameter uri and have "controller" param
                    $request['controller'] = $_REQUEST['controller'];
                    unset($_REQUEST['controller']);
                    // If we have "action" param
                    if (isset($_REQUEST['action'])) {
                        $request['action'] = $_REQUEST['action'];
                        unset($_REQUEST['action']);
                    }
                    // If we have "lang" param
                    if (isset($_REQUEST['lang'])) {
                        $language_is_set = $this->_setSiteLanguageByURIToken($_REQUEST['lang']);
                        if (!$language_is_set) {
                            // We have wrong language parameter
                            throw new \Exception('FrontController: The language is not supported', 404);
                        }
                        unset($_REQUEST['lang']);
                    }
                } elseif (isset($_REQUEST['controller']) && !empty($path_tokens)) {
                    // We have both "controller" param and controller token - wrong combination
                    throw new \Exception('FrontController: Path and controller is set', 404);

                } else {
                    // We haven't parameter uri just tokens
                    if (isset($path_tokens[0]) && !empty($path_tokens[0])) {
                        // First token may be a language
                        $language_is_set = $this->_setSiteLanguageByURIToken($path_tokens[0]);

                        if ($language_is_set) {
                            $i = 1;
                            // Remove language from tokens
                            unset($path_tokens[0]);
                        } else {
                            // We didn't recognize language in the first token
                            // We assume that the first token is not lang but controller
                            $i = 0;
                        }

                        // If we have more tokens
                        if (!empty($path_tokens)) {
                            $custom_route = $this->_searchCustomURI($path_tokens);
                            // If we met custom route
                            if (!empty($custom_route)) {
                                $request['controller'] = $custom_route['controller'];
                                $request['action'] = $custom_route['action'];
                                $request['params'] = $custom_route['params'];
                                $language_redirect = (isset($custom_route['language_redirect']) ?
                                        $custom_route['language_redirect'] : true);
                            } else {
                                // In the rest cases we take controller and action from tokens
                                $request['controller'] = $path_tokens[$i];
                                unset($path_tokens[$i]);
                                // Second token is to action
                                if (isset($path_tokens[$i+1]) && !empty($path_tokens[$i+1])) {
                                    $request['action'] = $path_tokens[$i+1];
                                    unset($path_tokens[$i+1]);
                                }
                            }
                        }
                    }
                    // Here we handle all global custom query parameters
                    if (count($_REQUEST) > 0) {
                        $this->_processCustomParams($_REQUEST);
                    }
                    // Save the rest of query params into Registry
                    \Models\Registry::set('request_params', $_REQUEST);

                    // Rest of tokens go to params
                    if (!empty($path_tokens)) {
                        $request['params'] = array_merge($request['params'], $path_tokens);
                    }
                }
            }
            // Rewrite $_REQUEST by found params
            $_REQUEST = $request['params'];

            // At last if we didn't get uri lang parameters then set lang from browser header or default
            if (!$language_is_set) {
                $this->_setSiteLanguageByBrowserHeader();
            }

            // If we have to redirect and have no special URLs that can't be redirected
            if (!empty($this->_language_redirect) && (!isset($language_redirect) || $language_redirect)) {
                \System\Helper::redirect($this->_language_redirect);
            }

            return $request;
        }

        /**
         * Hereinafter special methods for handling custom params
         * Mandatory naming: custom_param_handler__methodName
         */

        /**
         * Handles fb_locale param from Facebook
         *
         * @param string $param
         */
        private function custom_param_handler__setLanguage($param)
        {
            if (!empty($param)) {
                $supported_languages_info = unserialize(SITE_LANGUAGES);

                if (!empty($supported_languages_info)) {
                    foreach ($supported_languages_info as $supported_language => $info) {
                        if (strtolower($param) === strtolower($info['locale'])) {
                            // We catch some language
                            error_log('FrontController::__setLanguage: Setting lang by FB param to: '.$supported_language);
                            $_SESSION['user']['lang'] = $supported_language;
                            $this->_language_redirect = \System\Helper::removeLanguageFromURL($this->_current_url);
                        }
                    }
                } else {
                    // Fatal error. Supported languages are not set
                    throw new \Exception('FrontController: Supported languages are not set', 99999);
                }
            }
        }
    }
}
