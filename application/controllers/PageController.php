<?php

/**
 * PageController
 *
 * @author dev. Dmitry Kamyshov <dk@company.com>
 * @package Controller
 * @version 2.0.0
 */

namespace Controllers {

    class PageController
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
        static private $instance = NULL;

        /**
         * @return object
         */
        static function getInstance()
        {
            if (self::$instance == NULL) {
                self::$instance = new PageController();
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
            throw new \Exception('PageController: No main action is set', 404);
        }

        /**
         * Show 'about' page.
         */
        public function aboutAction()
        {
            $page = new \Models\Page();
            $about_page_values = $page->getPageBySlug('about_page_values');
            $about_page_about = $page->getPageBySlug('about_page_about');
            $about_page_video = $page->getPageBySlug('about_page_video');

            if (!empty($about_page_values) && !empty($about_page_about) && !empty($about_page_video)) {
                $this->_tpl->assign("about_page_values", $about_page_values['content']);
                $this->_tpl->assign("about_page_about", $about_page_about['content']);
                $this->_tpl->assign("about_page_video", $about_page_video['content']);
            } else {
                throw new \Exception('PageController: Page does not exist', 404);
            }
        }

        /**
         * Show 'services' page.
         */
        public function servicesAction()
        {

        }

        /**
         * Show 'technology' page.
         */
        public function technologyAction()
        {
            $page = new \Models\Page();
            $technology_page_features = $page->getPageBySlug('technology_page_features');
            $technology_page_prices = $page->getPageBySlug('technology_page_prices');

            if (!empty($technology_page_features) && !empty($technology_page_prices)) {
                $this->_tpl->assign("technology_page_features", $technology_page_features['content']);
                $this->_tpl->assign("technology_page_prices", $technology_page_prices['content']);
            } else {
                throw new \Exception('PageController: Page does not exist', 404);
            }
        }

        /**
         * Show 'products' page.
         */
        public function productsAction()
        {
            $product = new \Models\Product();
            $page = new \Models\Page();
            $servers = [];

            $products_page_features = $page->getPageBySlug('products_page_features');
            $products_page_text = $page->getPageBySlug('products_page_text');
            //$technology_page_features = $page->getPageBySlug('technology_page_features');
            $main_page_features = $page->getPageBySlug('main_page_features');

            if (!empty($main_page_features) && !empty($products_page_text)) {
                $this->_tpl->assign("main_page_features", $main_page_features['content']);
                //$this->_tpl->assign("technology_page_features", $technology_page_features['content']);
                $this->_tpl->assign("products_page_features", $products_page_features['content']);
                $this->_tpl->assign("products_page_text", $products_page_text['content']);
            } else {
                throw new \Exception('PageController: Page does not exist', 404);
            }

            $categories = $product->getServerCategories();
            $groups = $product->getServerGroups();

            if (!empty($categories)) {
                foreach ($categories as $key => $category) {
                    $servers_tmp = $product->getServersForServersPageByCategory($category['id'], $category['slug']);

                    if (!empty($servers_tmp)) {
                        $servers[$category['slug']] = $servers_tmp;
                        $description = $page->getPageBySlug($category['slug'] . '-description');

                        if (!empty($description)) {
                            $categories[$key]['description'] = $description;
                        }
                    }
                }
            }

            $this->_tpl->assign('servers', $servers);
            $this->_tpl->assign('categories', $categories);
            $this->_tpl->assign('groups', $groups);
        }

        /**
         * Show 'contact' page.
         */
        public function contactAction()
        {

        }

        /**
         * Show 'error404' page.
         */
        public function error404Action()
        {

        }

        /**
         * Show 'syserror' page.
         */
        public function syserrorAction()
        {

        }

    }

}