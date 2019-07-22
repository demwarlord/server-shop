<?php

function host_com__autoload($className) {
    $a = explode('\\', $className);

    // We assume only 2 lvl names ie \Models\Class...
    $fileName = ((empty($a[1])) ? $a[0] : $a[1]) . '.php';

    if (!empty($a[1]) && $a[0] == 'System') {
        // SYSTEM CLASSES
        if (file_exists(SYSTEM_DIR . $fileName)) {
            require SYSTEM_DIR . $fileName;
            return;
        }
    }

    if (!empty($a[1]) && $a[0] == 'Models') {
        // MODEL CLASSES
        $dirs = glob(APP_PATH . 'models/*', GLOB_ONLYDIR);
        foreach ($dirs as $dir) {
            if (!file_exists($dir . '/' . $fileName))
                continue;
            require $dir . '/' . $fileName;
            return;
        }
    }

    if (!empty($a[1]) && $a[0] == 'Controllers') {
        // CONTROLLER CLASSES
        if (file_exists(APP_PATH . 'controllers/' . $fileName)) {
            require APP_PATH . 'controllers/' . $fileName;
            return;
        }
    }
}

register_shutdown_function(function() {
    $error = error_get_last();
    if (!empty($error)) {
        if ($error['type'] === E_ERROR || $error['type'] === E_PARSE) {
            if (DEBUG_MAIL) {
                mail(DEBUG_MAIL, DOMAIN_NAME . ': Fatal Error', print_r($error, true));
            }
        } else {
            if (DEBUG_MAIL) {
                mail(DEBUG_MAIL, DOMAIN_NAME . ': Error', print_r($error, true));
            }
        }
    }
    return true;
});

if (is_callable('spl_autoload_register')) {
    spl_autoload_register('host_com__autoload');
}

function is_session_started() {
    if (php_sapi_name() !== 'cli') {
        if (version_compare(phpversion(), '5.4.0', '>=')) {
            return session_status() === PHP_SESSION_ACTIVE ? true : false;
        } else {
            return session_id() === '' ? false : true;
        }
    }
    return false;
}

class Init {

    final public static function run() {
        self::setSession();
        self::setTemplate();
        self::setLocation();
        self::setRoutes();

        // Do set routing. Here we set language
        \System\FrontController::getInstance()->setRouting();
        // Do get data & set variables common for all pages, header, footer etc.
        \Controllers\IndexController::getInstance()->loadAllPagesCommonConfiguration();
        // Do action
        \System\FrontController::getInstance()->executeAction();
    }

    public static function setTemplate() {
        \Models\Registry::set('tpl', new \System\Template());
    }

    public static function setLocation() {

    }

    public static function setRoutes() {
        \Models\Registry::set('custom_routes', require(CONFIG_DIR . 'custom.routes.php'));
        \Models\Registry::set('custom_404_routes', require(CONFIG_DIR . 'custom.404.routes.php'));
        \Models\Registry::set('custom_params', require(CONFIG_DIR . 'custom.params.php'));
    }

    public static function setSession() {
        if (is_session_started() === false) {
            session_name(str_replace('.','_',strtolower(DOMAIN_NAME)) . '_session');
            session_start();
            if (isset($_REQUEST['PHPSESSID'])) unset ($_REQUEST['PHPSESSID']);
            if (isset($_REQUEST['server_loc_session'])) unset ($_REQUEST['server_loc_session']);
        }
    }

}
