<?php

@include_once('local.settings.php');

if (!defined('CACHE_VERSION')) {
    define('CACHE_VERSION', '18122014');
}

if (!defined('DEVELOPMENT')) {
    define('DEVELOPMENT', false);
}

if (!defined('DEBUG_MAIL')) {
    define('DEBUG_MAIL', false);
}

// PROTOCOL
if (!defined('PROTO')) {
    if (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] === 'on')) {
        define('PROTO', 'https://');
    } else {
        define('PROTO', 'http://');
    }
}

if (!defined('HTTPS')) {
    define('HTTPS', 'https://');
}
if (!defined('HTTP')) {
    define('HTTP', 'http://');
}

// SUPPORTED LANGUAGES
define('SITE_LANGUAGES', serialize([
    'ru' => [
        'caption'   => 'Russian',
        'locale'    => 'ru_RU',
        'suitable'  => ['ru', 'uk']
        ],
    'en' => [
        'caption' => 'English',
        'locale'    => 'en_US',
        'suitable' => ['en', 'de']
        ],
]));

define('DEFAULT_LANGUAGE', 'en');

// BASIC FOLDER PATHS
define('APP_PATH', realpath(dirname(__FILE__) . '/../') . '/');
define('SITE_PATH_ROOT', preg_replace('|/application|', '', APP_PATH));
define('SITE_PUBLIC_ROOT', SITE_PATH_ROOT . 'htdocs/');
define('SITE_IMG_ROOT', SITE_PUBLIC_ROOT . 'img/');
define('SITE_EMAIL_IMG_ROOT', SITE_IMG_ROOT . 'email/');
define('TMP_DIR', SITE_PATH_ROOT . 'tmp/');

require_once(realpath(SITE_PATH_ROOT) . "/config.db.php");

// APP PATHS
define('CONFIG_DIR', APP_PATH . 'config/');
define('LIBS_DIR', APP_PATH . 'libs/');
define('TEMPLATE_DIR', APP_PATH . 'templates/');
define('EMAIL_TEMPLATE_DIR', TEMPLATE_DIR . 'common/email/');
define('EMAIL_TEMPLATE_DIR_EN', EMAIL_TEMPLATE_DIR . 'en/');
define('EMAIL_TEMPLATE_DIR_RU', EMAIL_TEMPLATE_DIR . 'ru/');
define('EMAIL_TEMPLATE_DIR_UA', EMAIL_TEMPLATE_DIR . 'ua/');
define('CONTROLLERS_DIR', APP_PATH . 'controllers/');
define('SYSTEM_DIR', APP_PATH . 'system/');

// LIBS PATHS
define('SMARTY_DIR', LIBS_DIR . 'Smarty-3.1.15/');

// TMP PATHS
define('COMPILE_DIR', TMP_DIR . 'templates_c/');
define('CACHE_DIR', TMP_DIR . 'cache/');

// URL CONSTANTS
if (!defined('DOMAIN_NAME')) {
    define('DOMAIN_NAME', $_SERVER['SERVER_NAME']);
}

define('SITE_URL_ROOT', PROTO . DOMAIN_NAME . '/');
define('SITE_JS_DIR', SITE_URL_ROOT . 'js/');
define('SITE_IMG_DIR', SITE_URL_ROOT . 'img/');
define('SITE_CSS_DIR', SITE_URL_ROOT . 'css/');

if (DEVELOPMENT) {
    if (!defined('MINIMIZED')) {
        define('MINIMIZED', '');
    }
} else {
    if (!defined('MINIMIZED')) {
        define('MINIMIZED', '.min');
    }
}

// LOCALIZATION FILES
define('SITE_RULANG_FILE', APP_PATH . 'langs/lang.ru');
define('SITE_ENLANG_FILE', APP_PATH . 'langs/lang.en');

// LIMITS
define('MAX_FILE_SIZE', 2097152);
define('MAX_IDLE_TIME', 10);

// SITEMAP GENERATOR
define('SITEMAP_FILE', LIBS_DIR . 'SitemapGenerator.php');

// OTHER SITES
if (!defined('URL_GATEWAY')) {
    define('URL_GATEWAY', HTTPS . 'gateway.company.com');
}
if (!defined('URL_CORE_API')) {
    define('URL_CORE_API', HTTPS . 'core.company.com');
}
if (!defined('URL_PAY')) {
    define('URL_PAY', HTTPS . 'pay.company.com');
}
if (!defined('URL_MANAGE')) {
    define('URL_MANAGE', HTTPS . 'manage.company.com');
}
if (!defined('URL_MY')) {
    define('URL_MY', HTTPS . 'my.company.com');
}
if (!defined('URL_WWW')) {
    define('URL_WWW', PROTO . DOMAIN_NAME);
}

// EMAILS
define('EML_NOREPLY', 'noreply@company.com');
define('EML_MODERATOR', 'mf@company.com');
define('EML_SUPPORT', 'support@company.com');

// EMAIL OPTIONS
define('MAIL_UNICODE', "Content-type: text/plain; charset=\"utf-8\"");

// REDIS
define('REDIS_SERVER', '127.0.0.1');
if (!defined('REDIS_CACHING')) {
    define('REDIS_CACHING', true);
}

// MAGIC NUMBERS
define('PROMOCODE_TYPE_LIMITED', 1);
define('PROMOCODE_TYPE_COUNTLESS', 2);
define('PROMOCODE_TYPE_WITHOUT_PAY', 3);
define('PROMOCODE_TYPE_JUST_REGISTER', 4);
define('PROMOCODE_DISCOUNT_TYPE_PERCENT', 1);
define('PROMOCODE_DISCOUNT_TYPE_EURO', 2);

// DISCOUNTS
define('SHOP_PERIOD_DISCOUNTS', serialize([
    1 => 0,
    3 => 2.5,
    6 => 5,
    12 => 10,
]));

if (!defined('USER_SALT')) define('USER_SALT',   'kjhKJh2$$3kjH*&');

if (!defined('USE_CACHE')) {
    define('USE_CACHE', false);
}

if (!defined('IP_FILTER')) {
    define('IP_FILTER', serialize([
    ]));
};

