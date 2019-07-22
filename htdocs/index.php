<?php

$start_time = microtime(1);
require_once(__DIR__ . '/../application/config/config.inc.php');
require_once(__DIR__ . "/../application/system/Init.php");

try {
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
        if (!defined('AJAX'))
            define('AJAX', true);
    } else {
        if (!defined('AJAX'))
            define('AJAX', false);
    }

    Init::run();

    $tpl = \Models\Registry::get('tpl');
    $action = \Models\Registry::get('action');
    $controller = \Models\Registry::get('controller');

    if (AJAX || in_array($action, ['generateSitemap', 'clearRedisCache', 'cartNotification'])) {
        $template = $controller . '/' . $action . '.tpl';
        $tpl->assign('controller', $controller);
        $tpl->assign('ajax', true);

        if (file_exists(TEMPLATE_DIR . $template)) {
            $tpl->display($template);
        }
    } elseif ($controller != 'ajax') {
        $tpl->assign('gentime', microtime(1) - $start_time);
        $tpl->display('index.tpl');
    }
} catch (Exception $e) {

    if (AJAX) {
        \System\ErrorController::getInstance()->HandleAjaxError($e);
    } else {
        \System\ErrorController::getInstance()->HandleError($e);
        $tpl = \Models\Registry::get('tpl');
        $tpl->display('index.tpl');
    }
}