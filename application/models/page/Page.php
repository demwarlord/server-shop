<?php

/**
* Page model
*
* @author dev. Dmitry Kamyshov <dk@company.com>
* @package Model
* @version 2.0.0
*/

namespace Models {

    class Page
    {

        /**
        * Current language.
        *
        * @var string
        */
        private $_lang = '';

        /**
        * Constructor.
        */
        public function __construct()
        {
            $this->_lang = isset($_SESSION['user']['lang']) ?
                    \Models\Db::getInstance()->escapeData($_SESSION['user']['lang']) : DEFAULT_LANGUAGE;
        }

        /**
        * Get static page by slug.
        *
        * @return array|false
        */
        public function getPageBySlug($slug)
        {

            $sql = "SELECT "
                     . "`p`.`id`, "
                     . "`p`.`slug`, "
                     . "`p`.`title_" . $this->_lang . "` as 'title', "
                     . "`p`.`content_" . $this->_lang . "` as 'content', "
                     . "`p`.`meta_description`, "
                     . "`p`.`meta_keywords`, "
                     . "`p`.`is_active` "
                 . "FROM `pages` `p` "
                 . "WHERE `p`.`is_active` = 1 "
                 . "AND `p`.`slug` = '" . \Models\Db::getInstance()->escapeData($slug) . "'";

            $cache_key = "shop_page_content_" . md5($sql);
            if (\Models\Cache::getInstance()->exists($cache_key)) {
                $page_content = \Models\Cache::getInstance()->get($cache_key);
            } else {
                $page_content = \Models\Db::getInstance()->getOneRowAssoc($sql);
                \Models\Cache::getInstance()->set($cache_key, $page_content, 3600 * 24 * 7);
            }
            return $page_content;
        }

        /**
        * Get static page by id.
        *
        * @return array|false
        */
        public function getPageById($id)
        {
            $sql = "SELECT "
                     . "`p`.`id`, "
                     . "`p`.`slug`, "
                     . "`p`.`title_" . $this->_lang . "` as 'title', "
                     . "`p`.`content_" . $this->_lang . "` as 'content', "
                     . "`p`.`meta_description`, "
                     . "`p`.`meta_keywords`, "
                     . "`p`.`is_active` "
                 . "FROM `pages` `p` "
                 . "WHERE `p`.`is_active` = 1 "
                 . "AND `p`.`id` = '" . \Models\Db::getInstance()->escapeData($id) . "'";

            $cache_key = "shop_page_content_" . md5($sql);

            if (\Models\Cache::getInstance()->exists($cache_key)) {
                $page_content = \Models\Cache::getInstance()->get($cache_key);
            } else {
                $page_content = \Models\Db::getInstance()->getOneRowAssoc($sql);
                \Models\Cache::getInstance()->set($cache_key, $page_content, 3600 * 24 * 7);
            }

            return $page_content;
        }

        public function getPageSeoBySlug($slug)
        {
            if (!empty($slug)) {
                $sql = "SELECT "
                        . "`id`, "
                        . "`meta_description_" . $this->_lang . "` `meta_description`, "
                        . "`meta_keywords_" . $this->_lang . "` `meta_keywords`, "
                        . "`robots`, "
                        . "`title_" . $this->_lang . "` `title` "
                     . "FROM `pages_seo` "
                     . "WHERE `slug` = '" . $slug . "'";

                return \Models\Db::getInstance()->getOneRowAssoc($sql);
            } else {
                return false;
            }
        }

    }
}
