<?php

/**
 * Helper is a wide site support functions
 *
 * @author dev. Dmitry Kamyshov <dk@company.com>
 * @package Controller
 * @subpackage System
 * @version 2.0.0
 */

namespace System {

    class Helper
    {
        /**
         * Gets full URL relatively from base_href
         *
         * @param string $path
         * @return string
         */
        public static function getFullURL($path)
        {
            $base_url = \Models\Registry::get('base_href');
            return rtrim($base_url, '/') . (empty($path) ? '' : '/' . trim($path, '/'));
        }

        /**
         * Redirect to the URL and add|remove if needed the language prefix
         *
         * @param string $header
         * @throws \Exception
         */
        public static function redirect($header)
        {
            $language_is_set = false;

            $parsed_url = parse_url($header);
            if (!empty($parsed_url['host']) && $parsed_url['host'] !== DOMAIN_NAME) {
                // We redirect to other site
                header("Location: " . $header);
                exit;
            }

            if ($parsed_url === false) {
                throw new \Exception('Helper: URL can\'t be parsed', 404);
            }

            // Parse URI
            if (!empty($parsed_url['path'])) {
                $path_tokens = explode('/', trim($parsed_url['path'], '/'));

                if (isset($path_tokens[0]) && !empty($path_tokens[0])) {
                    $check_language = self::isLanguage($path_tokens[0]);
                    if ($check_language !== false) {
                        $language = $check_language;
                        $language_is_set = true;
                    }
                }

                if ($language_is_set && $language === DEFAULT_LANGUAGE) {
                    // Remove default language from URL
                    unset($path_tokens[0]);
                } elseif (!$language_is_set && $_SESSION['user']['lang'] !== DEFAULT_LANGUAGE) {
                    // Other than default languages we add to the URL
                    array_unshift($path_tokens, $_SESSION['user']['lang']);
                }

                $parsed_url['path'] = implode('/', $path_tokens);
            } else {
                if ($_SESSION['user']['lang'] !== DEFAULT_LANGUAGE) {
                    // Other than default languages we add to the URL
                    $parsed_url['path'] = $_SESSION['user']['lang'];
                }
            }

            $result_url = self::assembleURL($parsed_url);

            if ($result_url === '') {
                $result_url = '/';
            }
            header("HTTP/1.1 301 Moved Permanently");
            header("Location: " . $result_url);
            exit;
        }

        /**
         * Assembles back parsed URL by parse_url() func
         *
         * @param array $parsed_url
         * @return string
         */
        public static function assembleURL($parsed_url)
        {
            $result_url = '';

            if (!empty($parsed_url) && is_array($parsed_url)) {
                if (!empty($parsed_url['scheme']) && !empty($parsed_url['host'])) {
                    $result_url .= $parsed_url['scheme'] . '://' . $parsed_url['host'];
                } elseif (empty($parsed_url['scheme']) && !empty($parsed_url['host'])) {
                    $result_url .= '//' . $parsed_url['host'];
                }

                if (!empty($parsed_url['path']) && $parsed_url['path'] !== '/') {
                    $result_url .= '/' . trim($parsed_url['path'], '/');
                }

                if (!empty($parsed_url['query'])) {
                    $result_url .= '/?' . $parsed_url['query'];
                }

                if (!empty($parsed_url['fragment'])) {
                    $result_url .= '#' . $parsed_url['fragment'];
                }
            }
            return $result_url;
        }

        /**
         * Removes given parameter in url query
         *
         * @param string $url
         * @param string $key
         * @return string
         * @throws \Exception
         */
        public static function removeQueryParameterFromURL($url, $key)
        {
            $parsed_url = parse_url($url);
            if ($parsed_url === false) {
                throw new \Exception('Helper: URL can\'t be parsed', 404);
            }
            if (!empty($parsed_url['query'])) {
                parse_str($parsed_url['query'], $query);
                if (array_key_exists($key, $query)) {
                    unset($query[$key]);
                    $parsed_url['query'] = http_build_query($query);
                }
            }
            return self::assembleURL($parsed_url);
        }

        /**
         * Removes all parameters in url query
         *
         * @param string $url
         * @return string
         * @throws \Exception
         */
        public static function removeQueryParametersFromURL($url)
        {
            $parsed_url = parse_url($url);
            if ($parsed_url === false) {
                throw new \Exception('Helper: URL can\'t be parsed', 404);
            }
            $parsed_url['query'] = '';
            return self::assembleURL($parsed_url);
        }

        /**
         * Put the URL to normalized form
         *
         * @param string $url
         * @return string
         * @throws \Exception
         */
        public static function normalizeURL($url)
        {
            $parsed_url = parse_url($url);
            if ($parsed_url === false) {
                throw new \Exception('Helper: URL can\'t be parsed', 404);
            }
            if (empty($parsed_url['scheme'])) {
                $parsed_url['scheme'] = str_replace('://', '', PROTO);
            }
            if (empty($parsed_url['host'])) {
                $parsed_url['host'] = DOMAIN_NAME;
            }
            return self::assembleURL($parsed_url);
        }

        /**
         * Just remove language prefix from URL
         *
         * @param string $url
         * @return string
         * @throws \Exception
         */
        public static function removeLanguageFromURL($url)
        {
            // Parse URI
            $parsed_url = parse_url($url);
            if ($parsed_url === false) {
                throw new \Exception('Helper: URL can\'t be parsed', 404);
            }
            if (!isset($parsed_url['path'])) {
                $parsed_url['path'] = '';
            }
            $path_tokens = explode('/', trim($parsed_url['path'], '/'));

            if (isset($path_tokens[0]) && !empty($path_tokens[0])) {
                if (self::isLanguage($path_tokens[0]) !== false) {
                    // We catch some language
                    unset($path_tokens[0]);
                }
            }
            $parsed_url['path'] = implode('/', $path_tokens);

            return self::assembleURL($parsed_url);
        }

        /**
         * Adds or replaces language prefix to the given URL
         *
         * @param string $lang
         * @param string $url
         * @return string
         * @throws \Exception
         */
        public static function addLanguageToURL($lang, $url)
        {
            if (self::isLanguage($lang) && !empty($url)) {
                // Parse URI
                $parsed_url = parse_url($url);
                if ($parsed_url === false) {
                    throw new \Exception('Helper: URL can\'t be parsed', 404);
                }
                if (!isset($parsed_url['path'])) {
                    $parsed_url['path'] = '';
                }
                $path_tokens = explode('/', trim($parsed_url['path'], '/'));

                if (isset($path_tokens[0]) && !empty($path_tokens[0])) {
                    if (self::isLanguage($path_tokens[0]) !== false) {
                        // We catch some language
                        unset($path_tokens[0]);
                    }
                }

                // Add language to the URL
                array_unshift($path_tokens, $lang);

                $parsed_url['path'] = implode('/', $path_tokens);

                return self::assembleURL($parsed_url);
            }
            return false;
        }

        /**
         * Checks if input parameter is a language or not
         *
         * @param string $string
         * @return string|false
         * @throws \Exception
         */
        public static function isLanguage($string)
        {
            if (!empty($string) && strlen($string) === 2) {
                $supported_languages_info = unserialize(SITE_LANGUAGES);

                if (!empty($supported_languages_info)) {
                    foreach ($supported_languages_info as $supported_language => $info) {
                        if ($string === $supported_language) {
                            // We catch some language
                            return $supported_language;
                        }
                    }
                } else {
                    // Fatal error. Supported languages are not set
                    throw new \Exception('Helper: Supported languages are not set', 99999);
                }
            }
            return false;
        }

        /**
         * Gets alternate languages list
         *
         * @return string
         * @throws \Exception
         */
        public static function getAlternateLanguages()
        {
            $alternate_languages = '';
            $supported_languages_info = unserialize(SITE_LANGUAGES);

            if (!empty($supported_languages_info)) {
                foreach ($supported_languages_info as $supported_language => $info) {
                    if ($supported_language !== DEFAULT_LANGUAGE) {
                        $alternate_languages .= (empty($alternate_languages) ? '' : ',') . $supported_language;
                    }
                }
            } else {
                // Fatal error. Supported languages are not set
                throw new \Exception('Helper: Supported languages are not set', 99999);
            }
            return $alternate_languages;
        }

        /**
         * Redirects to the 404 page
         */
        public static function redirect404()
        {
            // Redirect if there is a custom route for 404 page
            self::redirect404custom();
            // Else collect statistics
            self::collect404statistics();
            header("HTTP/1.0 404 Not Found");
            $frontController = \System\FrontController::getInstance();
            $frontController->setRouting('page', 'error404');
            $frontController->executeAction();
        }

        /**
         * Redirects from 404 if finds a custom route
         */
        public static function redirect404custom() {
            $custom_404_routes = \Models\Registry::get('custom_404_routes');

            if (!empty($custom_404_routes)) {
                $url = isset($_SERVER['REQUEST_URI'])?strtolower($_SERVER['REQUEST_URI']):'';

                if (!empty($url)) {
                    $url = trim(self::removeLanguageFromURL($url), '/');

                    foreach ($custom_404_routes as $route => $urls) {
                        $custom_route = strtolower(trim($route, '/'));

                        if (!empty($urls)) {
                            foreach ($urls as $needle_url) {
                                $needle_url = strtolower(trim($needle_url, '/'));
                                if ($needle_url === $url) {
                                    error_log("REDIR404CUSTOM:".$url);
                                    self::redirect($custom_route);
                                    break;
                                }
                            }
                        }
                    }
                }
            }
        }

        /**
         * Collects 404 statistics
         */
        public static function collect404statistics()
        {
            $url = isset($_SERVER['REQUEST_URI'])?strtolower($_SERVER['REQUEST_URI']):'';
            $referer = isset($_SERVER['HTTP_REFERER'])?strtolower($_SERVER['HTTP_REFERER']):'';
            $agent = isset($_SERVER['HTTP_USER_AGENT'])?strtolower($_SERVER['HTTP_USER_AGENT']):'';
            $hash = md5($url . $referer . $agent);

            $sql = "INSERT INTO `error404_statistics` "
                    . " (`hash`, `url`, `referer`, `browser`) "
                    . " VALUES "
                    . " ('".$hash."', '".$url."', '".$referer."', '".$agent."') "
                    . " ON DUPLICATE KEY UPDATE `count` = `count` + 1";
            \Models\Db::getInstance()->query($sql);
        }

        /**
         * Redirects to the Error page
         */
        public static function redirectSystemError($error_code)
        {
            $frontController = \System\FrontController::getInstance();
            $frontController->setRouting('page', 'syserror', [$error_code]);
            $frontController->executeAction();
        }

        /**
         * TODO: ATC
         */
        public static function seo($action, $controller, $request)
        {
            if ($action === 'details') {
                $slug = 'details_' . substr($request[0], 0, 4);
            } else if ((!empty ($request[0]) && !empty($request[1])) && ($request[0] === 1 || $request[0] === 2)) {
                $slug = $request[1];
            } else if ($action === 'main') {
                $slug = $controller;
            } else if ($action === 'category') {
                $slug = $controller.'_category_'.preg_replace('/[\W]/','_',$request[0]);
            } else if ($action === 'article') {
                $slug = $controller.'_article_'.preg_replace('/[\W]/','_',$request[0]);
            } else if (!empty($request) && array_key_exists('slug', $request)) {
                $slug = $request['slug'];
            } else {
                $slug = false;
            }

            if (!empty($slug)) {
                $page = new \Models\Page();

                return $page->getPageSeoBySlug($slug);
            } else {
                return false;
            }

        }

        /**
         * Gets JSON from POST
         */
        public static function getJSONParameters()
        {
            $data = file_get_contents("php://input");

            if (!empty($data)) {
                $data_decoded = json_decode($data, true);

                if ($data_decoded !== null && is_array($data_decoded)) {
                    return $data_decoded;
                }
            }

            return false;
        }
    }
}
