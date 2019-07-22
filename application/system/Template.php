<?php

namespace System {

    require_once(SMARTY_DIR . "Smarty.class.php");

    class Template extends \Smarty
    {
        public function __construct()
        {
            parent::__construct();

            $this->template_dir = TEMPLATE_DIR;
            $this->compile_dir  = COMPILE_DIR;
            $this->cache_dir    = CACHE_DIR;
            $this->config_dir   = SMARTY_DIR . "configs" . DS;
            $this->caching      = false;
            $this->setDefaultPaths();
            $this->registerClass('Helper', '\System\Helper');
        }

        /**
         * Initialization of all paths in the templates.
         *
         * @return void
         */
        public function setDefaultPaths()
        {
            $var = [];

            $var['cnf']['url']['index'] = SITE_URL_ROOT;
            $var['cnf']['domain']['name'] = DOMAIN_NAME;
            $var['cnf']['design']['css'] = SITE_CSS_DIR;
            $var['cnf']['design']['img'] = SITE_IMG_DIR;
            $var['cnf']['design']['js'] = SITE_JS_DIR;

            $var['cnf']['domain']['www'] = URL_WWW;
            $var['cnf']['domain']['manage'] = URL_MANAGE;
            $var['cnf']['domain']['my'] = URL_MY;
            $var['cnf']['domain']['gateway'] = URL_GATEWAY;
            $var['cnf']['domain']['pay'] = URL_PAY;

            $var['cnf']['rulang']['path'] = SITE_RULANG_FILE;
            $var['cnf']['englang']['path'] = SITE_ENLANG_FILE;

            $var['cnf']['proto'] = PROTO;
            $var['cnf']['http'] = HTTP;
            $var['cnf']['https'] = HTTPS;

            $this->assign($var);
        }

    }

}