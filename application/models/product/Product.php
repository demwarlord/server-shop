<?php

/**
 * Products model
 *
 * @author dev. Dmitry Kamyshov <dk@company.com>
 * @package Model
 * @version 2.0.0
 */

namespace Models {

    class Product
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
         * Getting product by its id.
         *
         * @param  int $id product id
         * @return array|false
         */
        public function getServerHardwareProduct($id)
        {
            $sql = "SELECT "
                    . " `p`.`id`, "
                    . " `p`.`product_custom_id`, "
                    . " `p`.`equivalent_quantity`, "
                    . " `p`.`product_name_" . $this->_lang . "` AS `name`, "
                    . " `p`.`short_product_name_" . $this->_lang . "` AS `short_name`, "
                    . " `p`.`value_" . $this->_lang . "` AS `value`, "
                    . " `p`.`additional_info_" . $this->_lang . "` AS `info`, "
                    . " `p`.`properties`, "
                    . " `p`.`price`, "
                    . " `p`.`setup_id`, "
                    . " `p`.`minimal_period`, "
                    . " `p`.`technical_description` "
                    . "FROM `server_hardware_products` `p` "
                    . "WHERE `id` = '" . \Models\Db::getInstance()->escapeData($id) . "'";

            return \Models\Db::getInstance()->getOneRowAssoc($sql, USE_CACHE);
        }

        /**
         * Getting product by its custom id.
         *
         * @param  int $custom_id product custom id
         * @return array|false
         */
        public function getServerHardwareProductByCustomId($custom_id)
        {
            $sql = "SELECT "
                    . " `p`.`id`, "
                    . " `p`.`product_custom_id`, "
                    . " `p`.`equivalent_quantity`, "
                    . " `p`.`product_name_" . $this->_lang . "` AS `name`, "
                    . " `p`.`short_product_name_" . $this->_lang . "` AS `short_name`, "
                    . " `p`.`value_" . $this->_lang . "` AS `value`, "
                    . " `p`.`additional_info_" . $this->_lang . "` AS `info`, "
                    . " `p`.`properties`, "
                    . " `p`.`price`, "
                    . " `p`.`setup_id`, "
                    . " `p`.`minimal_period`, "
                    . " `p`.`technical_description` "
                    . "FROM `server_hardware_products` `p` "
                    . "WHERE `product_custom_id` = '" . \Models\Db::getInstance()->escapeData($custom_id) . "'";

            return \Models\Db::getInstance()->getOneRowAssoc($sql, USE_CACHE);
        }

        /**
         * Getting server by url, with discount info.
         *
         * result:
         *
         * Array
         * (
         *     [id] => 1
         *     [server_custom_id] => 498
         *     [category_id] => 1
         *     [caption] => Intel Server 2m
         *     [description] => This affordable dedicated server...
         *     [short_caption] => IS2M
         *     [url] => is2m
         *     [is_active] => 1
         *     [in_stock] => 1
         *     [monthly_fee] => 55.00
         *     [setup_id] => 0
         *     [setup_fee] => 0.00
         *     [server_model] => Fujitsu Primergy TX120 S3
         *     [brand] => Fujitsu
         *     [group_id] => 1
         *     [discount] => 10
         *     [discounts_info] => Array
         *         (
         *             [0] => Array
         *                 (
         *                     [discount_id] => 1
         *                     [discount_caption] => Special "HOT SUMMER 2013" prices!
         *                     [discount_short_caption] => Special summer discount
         *                     [discount_custom_id] => 422
         *                     [discount] => 10.00
         *                     [quantity] => 1
         *                 )
         *         )
         * )
         *
         * @param  string $url server url
         * @return array|false
         */
        public function getServer($url)
        {
            $db = \Models\Db::getInstance();
            $sql = "SELECT "
                    . " `s`.`id`, "
                    . " `s`.`server_custom_id`, "
                    . " `s`.`category_id`, "
                    . " `s`.`caption_" . $this->_lang . "` AS `caption`, "
                    . " `s`.`description_" . $this->_lang . "` AS `description`, "
                    . " `s`.`short_caption`, "
                    . " `s`.`url`, "
                    . " `s`.`properties`, "
                    . " `s`.`is_active`, "
                    . " `s`.`in_stock`, "
                    . " `s`.`monthly_fee`, "
                    . " `s`.`old_setup_fee`, "
                    . " `s`.`setup_id`, "
                    . " IFNULL(`sf`.`price`, 0) AS `setup_fee`, "
                    . " `s`.`discount_id`, "
                    . " `s`.`server_model`, "
                    . " `s`.`brand`, "
                    . " `s`.`badge`, "
                    . " `s`.`group_id` "
                    . " FROM `servers` `s` "
                    . " LEFT JOIN `setup_fees` `sf` "
                    . " ON (`sf`.`id` = `s`.`setup_id`) "
                    . " WHERE `s`.`url` = '" . $db->escapeData($url) . "' "
                    . " AND `s`.`is_active` = '1'";

            $server = $db->getOneRowAssoc($sql, USE_CACHE);

            if (!empty($server)) {
                $server['discount'] = 0;
                $server['properties'] = (empty($server['properties']) ? [] : json_decode($server['properties'], true));

                // reseller discounts
                if (!empty($_SESSION['user_logged_profile']['discount_info'])) {
                    foreach ($_SESSION['user_logged_profile']['discount_info'] as $discount) {
                        if ($discount['server_product_id'] == $server['server_custom_id']) {
                            $server['discount'] += ($discount['discount'] * $discount['quantity']);
                            $server['discounts_info'][] = $discount;
                        }
                    }
                //
                } elseif ($server['discount_id'] != 0) {
                    $discount_info = $this->getDiscountById($server['discount_id']);
                    $server['discount'] = $discount_info['discount'];
                    $server['discounts_info'][] = [
                        'discount_id' => $discount_info['id'],
                        'discount_caption' => $discount_info['discount_caption'],
                        'discount_short_caption' => $discount_info['discount_short_caption'],
                        'discount_custom_id' => $discount_info['discount_custom_id'],
                        'discount' => $discount_info['discount'],
                        'quantity' => 1
                    ];
                }
                unset($server['discount_id']);

                return $server;
            }

            return false;
        }

        public function getDiscountByCustomId($custom_id)
        {
            $sql = "SELECT "
                    . " `di`.`id`, "
                    . " `di`.`discount_custom_id`, "
                    . " `di`.`discount`, "
                    . " `di`.`caption_" . $this->_lang . "` AS `discount_caption`, "
                    . " `di`.`short_caption_" . $this->_lang . "` AS `discount_short_caption` "
                    . " FROM `discounts` `di` "
                    . " WHERE `di`.`discount_custom_id` = '" . \Models\Db::getInstance()->escapeData($custom_id) . "' ";

            return \Models\Db::getInstance()->getOneRowAssoc($sql, USE_CACHE);
        }

        public function getDiscountById($id)
        {
            $sql = "SELECT "
                    . " `di`.`id`, "
                    . " `di`.`discount_custom_id`, "
                    . " `di`.`discount`, "
                    . " `di`.`caption_" . $this->_lang . "` AS `discount_caption`, "
                    . " `di`.`short_caption_" . $this->_lang . "` AS `discount_short_caption` "
                    . " FROM `discounts` `di` "
                    . " WHERE `di`.`id` = '" . \Models\Db::getInstance()->escapeData($id) . "' ";

            return \Models\Db::getInstance()->getOneRowAssoc($sql, USE_CACHE);
        }

        /**
         * Getting server by id.
         *
         * result:
         *      see getServer($url)
         *
         * @param  string $id server id
         * @return array|false
         */
        public function getServerById($id)
        {
            $db = \Models\Db::getInstance();
            $sql = "SELECT `url` "
                    . " FROM `servers` "
                    . " WHERE `id` = '" . $db->escapeData($id) . "' ";

            $url = $db->getOne($sql, USE_CACHE);

            if (!empty($url)) {
                return $this->getServer($url);
            }

            return false;
        }

        public function getServerCaptionByCustomId($custom_id)
        {
            $db = \Models\Db::getInstance();
            $sql = "SELECT "
                    . " `caption_" . $this->_lang . "` AS `caption` "
                 . " FROM `servers` "
                 . " WHERE `server_custom_id` = '" . $db->escapeData($custom_id) . "' ";

            return $db->getOne($sql, USE_CACHE);
        }

        public function getServerURLByCustomId($custom_id)
        {
            $db = \Models\Db::getInstance();
            $sql = "SELECT `url` "
                    . " FROM `servers` "
                    . " WHERE `server_custom_id` = '" . $db->escapeData($custom_id) . "' ";

            return $db->getOne($sql, USE_CACHE);
        }

        /**
         * Getting servers withou prices.
         *
         * result:
         *
         * Array
         * (
         *     [0] => Array
         *         (
         *             [id] => 1
         *             [category_id] => 1
         *             [caption] => Intel Server 2m
         *             [short_caption] => IS2M
         *             [description] => This affordable dedicated...
         *             [url] => is2m
         *             [is_active] => 1
         *             [in_stock] => 1
         *             [group_id] => 1
         *         )
         *     [1] => Array
         *         (
         *             [id] => 2
         *             [category_id] => 1
         *             [caption] => Intel Server 4m
         *             [short_caption] => IS4M
         *             [description] => The cheapest server to get...
         *             [url] => is4m
         *             [is_active] => 1
         *             [in_stock] => 1
         *             [group_id] => 1
         *        )
         *
         * @return array|false
         */
        public function getServers()
        {
            $sql = "SELECT "
                    . " `s`.`id`, "
                    . " `s`.`category_id`, "
                    . " `s`.`caption_" . $this->_lang . "` AS `caption`, "
                    . " `s`.`short_caption`, "
                    . " `s`.`description_" . $this->_lang . "` AS `description`, "
                    . " `s`.`url`, "
                    . " `s`.`is_active`, "
                    . " `s`.`in_stock`, "
                    . " `s`.`group_id` "
                    . " FROM `servers` `s` "
                    . " WHERE `is_active` = '1'";

            return \Models\Db::getInstance()->getAll($sql, USE_CACHE);
        }

        /**
         * Getting server locations.
         *
         * result:
         *
         * Array
         * (
         *     [0] => Array
         *         (
         *             [id] => 1
         *             [location_name] => Luxembourg
         *             [location_name_en] => Luxembourg
         *             [location_code] => LU
         *         )
         * )
         *
         * @param int $id
         * @return array
         */
        public function getServerLocations($id)
        {
            $sql = "SELECT "
                    . " `sl`.`id`, "
                    . " `sl`.`location_name_" . $this->_lang . "` AS `location_name`, "
                    . " `sl`.`location_name_en`, "
                    . " `sl`.`location_code` "
                    . " FROM `servers_location_references` `slr` "
                    . " LEFT JOIN `server_locations` `sl` "
                    . " ON (`sl`.`id` = `slr`.`location_id`) "
                    . " WHERE `slr`.`server_id` = '" . \Models\Db::getInstance()->escapeData($id) . "' ";

            $result = \Models\Db::getInstance()->getAll($sql, USE_CACHE);

            return ($result === false ? [] : $result);
        }

        public function getServerLocationByName($server_id, $location_name)
        {
            $locations = $this->getServerLocations($server_id);

            foreach ($locations as $location) {
                if (strcasecmp($location['location_name_en'], $location_name) == 0) {
                    return $location;
                }
            }

            return false;
        }

        /**
         * Getting server group.
         *
         * result:
         *
         * Array
         * (
         *     [id] => 1
         *     [order_index] => 100
         *     [group_name] => Performance servers
         * )
         *
         * @param int $id
         * @return array
         */
        public function getServerGroup($id)
        {
            $sql = "SELECT "
                    . " `g`.`id`, "
                    . " `g`.`order_index`, "
                    . " `g`.`group_name_" . $this->_lang . "` AS `group_name` "
                    . " FROM `server_groups` `g` "
                    . " WHERE `g`.`id` = '" . \Models\Db::getInstance()->escapeData($id) . "' ";

            $result = \Models\Db::getInstance()->getOneRowAssoc($sql, USE_CACHE);

            return ($result === false ? [] : $result);
        }

        /**
         * Getting server groups.
         *
         * result:
         *
         * Array
         * (
         *     [0] => Array
         *         (
         *             [id] => 1
         *             [order_index] => 100
         *             [group_name] => Performance servers
         *         )
         *     [1] => Array
         *         (
         *             [id] => 2
         *             [order_index] => 200
         *             [group_name] => Enterprise-level and customizable servers
         *         )
         *     [2] => Array
         *         (
         *             [id] => 3
         *             [order_index] => 300
         *             [group_name] => Ready-to-go configurations
         *         )
         * )
         *
         * @return array|false
         */
        public function getServerGroups()
        {
            $sql = "SELECT "
                    . " `id`, "
                    . " `order_index`, "
                    . " `group_name_" . $this->_lang . "` AS `group_name` "
                    . " FROM `server_groups` "
                    . " ORDER BY `order_index`";

            $server_groups = \Models\Db::getInstance()->getAll($sql, USE_CACHE);

            return $server_groups;
        }

        /**
         * Getting products.
         *
         * result
         *
         * Array
         * (
         *     [0] => Array
         *         (
         *             [id] => 771
         *             [in_configurator] => 1
         *             [in_overview] => 0
         *             [order_index] => 0
         *             [server_id] => 1
         *             [sub_category_id] => 20
         *             [product_id] => 113
         *             [is_default_product] => 0
         *             [product_custom_id] => 0
         *             [equivalent_quantity] => 1
         *             [name] => 32 bit
         *             [short_name] => 32 bit
         *             [value] => 32 bit
         *             [info] =>
         *             [price] => 0.00
         *             [setup_fee] => 0.00
         *             [setup_id] => 0
         *             [minimal_period] => 1
         *             [technical_description] =>
         *         )
         *     [1] => Array
         *         (
         *             [id] => 28
         *             [in_configurator] => 0
         *             [in_overview] => 1
         *             [order_index] => 0
         *             [server_id] => 1
         *             [sub_category_id] => 12
         *             [product_id] => 22
         *             [is_default_product] => 1
         *             [product_custom_id] => 0
         *             [equivalent_quantity] => 1
         *             [name] => included
         *             [short_name] => yes
         *             [value] => yes
         *             [info] =>
         *             [price] => 0.00
         *             [setup_fee] => 0.00
         *             [setup_id] => 0
         *             [minimal_period] => 1
         *             [technical_description] =>
         *        )
         *
         * @param  string $url server url
         * @return array|false
         */
        public function getServerHardwareProducts($url)
        {
            $server = $this->getServer($url);

            if ($server === false) {
                return false;
            }

            $sql = "SELECT "
                    . " `sp`.*, "
                    . " `p`.`product_custom_id`, "
                    . " `p`.`equivalent_quantity`, "
                    . " `p`.`product_name_" . $this->_lang . "` AS `name`, "
                    . " `p`.`short_product_name_" . $this->_lang . "` AS `short_name`, "
                    . " `p`.`value_" . $this->_lang . "` AS `value`, "
                    . " `p`.`additional_info_" . $this->_lang . "` AS `info`, "
                    . " `p`.`properties`, "
                    . " `p`.`price`, "
                    . " IFNULL(`sf`.`price`, 0) AS `setup_fee`, "
                    . " `p`.`setup_id`, "
                    . " `p`.`minimal_period`, "
                    . " `p`.`technical_description`, "
                    . " `sc`.`sub_category_name_" . $this->_lang . "` AS `label` "
                    . " FROM `servers_products` `sp` "
                    . " LEFT JOIN  `server_hardware_products` `p` "
                    . " ON (`sp`.`product_id` = `p`.`id`) "
                    . " LEFT JOIN `server_hardware_sub_categories` `sc` "
                    . " ON (`sp`.`sub_category_id` = `sc`.`id`) "
                    . " LEFT JOIN `setup_fees` `sf` "
                    . " ON (`p`.`setup_id` = `sf`.`id`) "
                    . " WHERE `sp`.`server_id` = '" . \Models\Db::getInstance()->escapeData($server['id']) . "' "
                    . " ORDER BY `sp`.`order_index` ";

            return \Models\Db::getInstance()->getAll($sql, USE_CACHE);
        }

        /**
         * Getting setup fees.
         *
         * result:
         *
         * Array
         * (
         *     [0] => Array
         *         (
         *             [id] => 1
         *             [setup_custom_id] => 430
         *             [name] => SSD setup fee
         *             [price] => 25.00
         *         )
         *     [1] => Array
         *         (
         *             [id] => 2
         *             [setup_custom_id] => 496
         *             [name] => RAM Upgrade 1x8GB 1066MHz module (setup)
         *             [price] => 50.00
         *         )
         *     [2] => Array
         *         (
         *             [id] => 3
         *             [setup_custom_id] => 497
         *             [name] => RAM Upgrade 2x8GB 1066MHz module (setup)
         *             [price] => 100.00
         *         )
         *     [3] => Array
         *         (
         *             [id] => 4
         *             [setup_custom_id] => 327
         *             [name] => Mounting 1U hardware
         *             [price] => 39.00
         *         )
         * )
         *
         * @return array|false
         */
        public function getSetupFees($id = false)
        {
            $condition = ($id === false ? "" : " WHERE `id` = '" . \Models\Db::getInstance()->escapeData($id) . "' ");
            $sql = "SELECT * FROM `setup_fees` "
                    . $condition;

            return \Models\Db::getInstance()->getAll($sql, USE_CACHE);
        }

        /**
         * Getting server categories.
         *
         * result:
         *
         * Array
         * (
         *     [0] => Array
         *         (
         *             [id] => 1
         *             [name] => Dedicated servers
         *         )
         *     [1] => Array
         *         (
         *             [id] => 4
         *             [name] => Housing
         *         )
         *     [2] => Array
         *         (
         *             [id] => 2
         *             [name] => Virtual servers
         *         )
         * )
         *
         * @return array|false
         */
        public function getServerCategories($id = false)
        {
            $condition = ($id === false ? "" : " WHERE `a`.`id` = '" . \Models\Db::getInstance()->escapeData($id) . "' ");
            $sql = "SELECT "
                    . " `a`.`id`, "
                    . " `a`.`slug`, "
                    . " `a`.`category_name_" . $this->_lang . "` AS `name` "
                    . " FROM `server_categories` `a` "
                    . " INNER JOIN `servers` `b` "
                    . " ON (`a`.`id` = `b`.`category_id` AND `b`.`is_active` = 1) "
                    . $condition
                    . " GROUP BY `name`";

            return \Models\Db::getInstance()->getAll($sql, USE_CACHE);
        }

        /**
         * Get the representation of category at a specific page
         *
         * result:
         *
         * Array
         * (
         *     [0] => Array
         *         (
         *             [category_id] => 1
         *             [page_name] => dedicated
         *             [subcategory_id] => 22
         *             [subcategory_name] => Processor
         *             [subcategory_index] => cpu
         *             [product_values] => 1
         *         )
         *     [1] => Array
         *         (
         *             [category_id] => 1
         *             [page_name] => dedicated
         *             [subcategory_id] => 1
         *             [subcategory_name] => Storage
         *             [subcategory_index] => hdd
         *             [product_values] => 1
         *         )
         * )
         *
         * @param int $id Category Id
         * @param string $page Page name
         * @return array|false
         */
        public function getCategoryRepresentation($id, $page)
        {
            $sql = "SELECT "
                    . "`a`.`category_id`, "
                    . "`a`.`page_name`, "
                    . "`a`.`subcategory_id`, "
                    . "`a`.`subcategory_name_" . $this->_lang . "` AS 'subcategory_name', "
                    . "`a`.`subcategory_index`, "
                    . "`a`.`product_values` "
                    . " FROM `server_category_representation` `a` "
                    . " WHERE `category_id` = '" . \Models\Db::getInstance()->escapeData($id) . "' "
                    . " AND `page_name` = '" . \Models\Db::getInstance()->escapeData($page) . "' ";

            $category_representation = \Models\Db::getInstance()->getAll($sql, USE_CACHE);

            return $category_representation;
        }

        /**
         * Getting Hardware Categories.
         *
         * result:
         *
         * Array
         * (
         *     [0] => Array
         *         (
         *             [id] => 10
         *             [is_active] => 1
         *             [order_index] => 10
         *             [in_configurator] => 1
         *             [name] => Space configuration
         *             [short_name] => Space configuration
         *             [as_header] => 0
         *             [in_overview] => 0
         *         )
         *     [1] => Array
         *         (
         *             [id] => 8
         *             [is_active] => 1
         *             [order_index] => 10
         *             [in_configurator] => 1
         *             [name] => CPU Configuration
         *             [short_name] => CPU
         *             [as_header] => 0
         *             [in_overview] => 1
         *         )
         * )
         *
         * @param  string $url server url
         * @return array|false
         */
        public function getServerHardwareCategories($url = false)
        {
            $server = $url ? $this->getServer($url) : false;
            $condition = ($url !== false && $server !== false) ? " WHERE `server_id` = '"
                    . \Models\Db::getInstance()->escapeData($server ['id']) . "' AND `in_configurator` = '1' " : "";

            $sql = "SELECT "
                    . " `id`, "
                    . " `is_active`, "
                    . " `order_index`, "
                    . " `in_configurator`, "
                    . " `category_name_" . $this->_lang . "` AS `name`, "
                    . " `short_category_name_" . $this->_lang . "` AS short_name, "
                    . " `in_overview_as_header` AS `as_header`, "
                    . " `in_overview` "
                    . " FROM `server_hardware_categories` `cat` "
                    . " RIGHT JOIN "
                    . " (SELECT "
                    . " `category_id`, "
                    . " `in_configurator`, "
                    . " `in_overview` "
                    . " FROM `servers_products` `a` "
                    . " INNER JOIN `server_hardware_sub_categories` `b` "
                    . " ON `a`.`sub_category_id` = `b`.`id` "
                    . $condition
                    . " GROUP BY `category_id` ) `sub` "
                    . " ON (`sub`.`category_id` = `cat`.`id`) "
                    . " ORDER BY `order_index`";

            return \Models\Db::getInstance()->getAll($sql, USE_CACHE);
        }

        /**
         * Getting Hardware Product Groups.
         *
         * result:
         *
         * Array
         * (
         *     [0] => Array
         *         (
         *             [id] => 22
         *             [server_id] => 16
         *             [is_active] => 1
         *             [is_required] => 1
         *             [in_configurator] => 1
         *             [in_overview] => 1
         *             [category_id] => 8
         *             [name] => CPU
         *         )
         *     [1] => Array
         *         (
         *             [id] => 1
         *             [server_id] => 16
         *             [is_active] => 1
         *             [is_required] => 1
         *             [in_configurator] => 1
         *             [in_overview] => 1
         *             [category_id] => 2
         *             [name] => First drive
         *         )
         *     [2] => Array
         *         (
         *             [id] => 2
         *             [server_id] => 16
         *             [is_active] => 1
         *             [is_required] => 1
         *             [in_configurator] => 1
         *             [in_overview] => 1
         *             [category_id] => 2
         *             [name] => Second drive
         *         )
         * )
         *
         * @param  string $url server url
         * @return array|false
         */
        public function getServerHardwareSubCategories($url = false)
        {
            $server = $url ? $this->getServer($url) : false;
            $condition = ($url !== false && $server !== false) ? " WHERE `server_id` = '"
                    . \Models\Db::getInstance()->escapeData($server['id']) . "' " : "";

            $sql = "SELECT "
                    . " `b`.`id`, "
                    . " `server_id`, "
                    . " `is_active`,  "
                    . " `is_required`, "
                    . " `in_configurator`, "
                    . " `in_overview`, "
                    . " `category_id`, "
                    . " `sub_category_name_" . $this->_lang . "` AS `name` "
                    . " FROM `servers_products` `a` "
                    . " INNER JOIN `server_hardware_sub_categories` `b` "
                    . " ON `a`.`sub_category_id` = `b`.`id` "
                    . $condition
                    . " GROUP BY `name` "
                    . " ORDER BY `b`.`order_index`";

            return \Models\Db::getInstance()->getAll($sql, USE_CACHE);
        }

        /**
         * Getting server hardware subcategory by id.
         *
         * result:
         *
         * Array
         * (
         *     [id] => 1
         *     [category_id] => 2
         *     [is_required] => 1
         *     [name] => First drive
         * )
         *
         * @param  $id product group id
         * @return array|false
         */
        public function getServerHardwareSubCategory($id)
        {
            $sql = "SELECT "
                    . " `id`, "
                    . "`category_id`, "
                    . "`is_required`, "
                    . "`sub_category_name_" . $this->_lang . "` AS `name` "
                    . "FROM `server_hardware_sub_categories` "
                    . "WHERE `id` = '" . \Models\Db::getInstance()->escapeData($id) . "' ";

            return \Models\Db::getInstance()->getOneRowAssoc($sql, USE_CACHE);
        }

        public function getConfiguratorProductCrossDependencies()
        {
            $sql = "SELECT * "
                    . " FROM `server_product_cross_dependencies`"
                    . " ORDER BY `condition_cat`, `condition_prod` ";

            $detailed = \Models\Db::getInstance()->getAll($sql, USE_CACHE);

            $sql = "SELECT `dependent_cat` "
                    . " FROM `server_product_cross_dependencies`"
                    . " GROUP BY `dependent_cat` ";

            $optimized_res = \Models\Db::getInstance()->query($sql);
            $optimized = mysqli_fetch_array($optimized_res, MYSQLI_NUM);

            return [
                'detailed' => $detailed,
                'optimized' => $optimized
            ];
        }

        /**
         * Get server monthly price by counting price of the default products
         *
         * result:
         *
         * Array
         * (
         *     [id] => 1
         *     [server_custom_id] => 498
         *     [category_id] => 1
         *     [caption] => Intel Server 2m
         *     [description] => This affordable dedicated...
         *     [short_caption] => IS2M
         *     [url] => is2m
         *     [is_active] => 1
         *     [in_stock] => 1
         *     [monthly_fee] => 55.00
         *     [setup_id] => 0
         *     [setup_fee] => 0.00
         *     [server_model] => Fujitsu Primergy TX120 S3
         *     [brand] => Fujitsu
         *     [group_id] => 1
         *     [discount] => 10
         *     [discounts_info] => Array
         *         (
         *             [0] => Array
         *                 (
         *                     [server_product_id] => 498
         *                     [server_caption] => Intel Server 2m
         *                     [discount_id] => 1
         *                     [discount_caption] => Special "HOT SUMMER 2013" prices!
         *                     [discount_short_caption] => Special summer discount
         *                     [discount_custom_id] => 422
         *                     [discount] => 10.00
         *                     [quantity] => 1
         *                 )
         *         )
         *     [complete_monthly_fee] => 55.00
         *     [complete_setup_fee] => 0.00
         *     [group_info] => Array
         *         (
         *             [id] => 1
         *             [order_index] => 100
         *             [group_name] => Performance servers
         *        )
         *     [location_info] => Array
         *         (
         *             [0] => Array
         *                 (
         *                     [id] => 1
         *                     [location_name] => Luxembourg
         *                     [location_name_en] => Luxembourg
         *                     [location_code] => LU
         *                 )
         *         )
         * )
         *
         * @param string $url Server URL
         * @return array
         */
        public function getServerWithCalculatedPrice($url, $in_configurator = false)
        {
            $server = $this->getServer($url);

            if ($server !== false) {
                $products = $this->getServerDefaultProducts($url);

                if (!empty($products)) {
                    $products_monthly_fee = 0;
                    $products_setup_fee = 0;

                    foreach ($products as $product) {
                        if (($in_configurator && (int)$product['in_configurator'] === 1) || !$in_configurator) {
                            $products_monthly_fee += $product['price'];
                            $products_setup_fee += $product['setup_fee'];
                        }
                    }
                }

                $server['products_monthly_fee'] = number_format($products_monthly_fee, 2, '.', '');
                $server['products_setup_fee'] = number_format($products_setup_fee, 2, '.', '');
                $server['complete_monthly_fee'] = number_format(($products_monthly_fee + $server['monthly_fee']), 2, '.', '');
                $server['complete_setup_fee'] = number_format(($products_setup_fee + $server['setup_fee']), 2, '.', '');
                $server['group_info'] = $this->getServerGroup($server['group_id']);
                $server['location_info'] = $this->getServerLocations($server['id']);
            }

            return $server;
        }

        /**
         * Returns default products with upgrade option
         *
         * result:
         *
         * Array
         * (
         *     [1] => Array
         *         (
         *             [id] => 2
         *             [in_configurator] => 1
         *             [in_overview] => 1
         *             [order_index] => 0
         *             [server_id] => 1
         *             [sub_category_id] => 1
         *             [product_id] => 1
         *             [is_default_product] => 1
         *             [product_custom_id] => 0
         *             [equivalent_quantity] => 1
         *             [name] => 500 GB - Enterprise SATA3 7200rpm (WD RE4)
         *             [short_name] => 500 GB SATA3 Enterprise 7200rpm
         *             [value] => 500 GB
         *             [info] =>
         *             [price] => 0.00
         *             [setup_fee] => 0.00
         *             [setup_id] => 0
         *             [minimal_period] => 1
         *             [technical_description] =>
         *             [upgrade] => 1
         *             [upgrade_products] => Array
         *                 (
         *                     [0] => Array
         *                         (
         *                             [id] => 787
         *                             [in_configurator] => 1
         *                             [in_overview] => 1
         *                             [order_index] => 10
         *                             [server_id] => 1
         *                             [sub_category_id] => 1
         *                             [product_id] => 3
         *                             [is_default_product] => 0
         *                             [product_custom_id] => 428
         *                             [equivalent_quantity] => 1
         *                             [name] => 120 GB - Solid State Drive
         *                             [short_name] => 1 x 120 GB SSD
         *                             [value] => 120 GB SSD
         *                             [info] => This is a 120 GB - Solid State Drive...
         *                             [price] => 20.00
         *                             [setup_fee] => 25.00
         *                             [setup_id] => 1
         *                             [minimal_period] => 3
         *                             [technical_description] =>
         *                         )
         *
         * @param string $url Server url
         * @return array
         */
        public function getServerDefaultProducts($url, $subcats = [])
        {
            $result = [];
            $hw_sub_categories = $this->getServerHardwareSubCategories($url);
            $products = $this->getServerHardwareProducts($url);

            if (!empty($products)) {
                if (!empty($hw_sub_categories)) {
                    foreach ($hw_sub_categories as $hw_sub_category) {
                        if ((!empty($subcats) && in_array($hw_sub_category['id'], $subcats)) || empty($subcats)) {
                            $upgrade = [];

                            foreach ($products as &$product) {
                                if ($product['sub_category_id'] == $hw_sub_category['id']) {
                                    $product['properties'] = (empty($product['properties']) ? [] : json_decode($product['properties'], true));

                                    if ((int)$product['is_default_product'] === 1) {
                                        $def = &$product;
                                    } else {
                                        $upgrade[] = &$product;
                                    }
                                }
                            }

                            $def['upgrade'] = empty($upgrade) ? 0 : 1;
                            $def['upgrade_products'] = $upgrade;
                            $result[] = $def;
                        }
                    }
                }
            }

            return $result;
        }

        /**
         * Get server products modified by selected products
         *
         * $upgrades => Array
         *     (
         *         [0] => Array
         *             (
         *                 [sub_cat_id] => 22
         *                 [id] => 1226
         *             )
         *         ...
         *
         *         [5] => Array
         *             (
         *                 [sub_cat_id] => 19
         *                 [id] => 752
         *             )
         *         [6] => Array
         *             (
         *                 [sub_cat_id] => 28
         *                 [id] => 1459
         *             )
         *     )
         *
         * @param string $url
         * @param array $upgraded
         * @return array
         */
        public function getServerDefaultProductsUpgraded($url, $upgraded)
        {
            $result = [];
            $hw_sub_categories = $this->getServerHardwareSubCategories($url);
            $products = $this->getServerHardwareProducts($url);

            if (!empty($products)) {
                if (!empty($hw_sub_categories)) {
                    foreach ($hw_sub_categories as $hw_sub_category) {
                        $upgrade = [];

                        foreach ($products as &$product) {
                            if ($product['sub_category_id'] == $hw_sub_category['id']) {
                                $product['properties'] = (empty($product['properties']) ? [] : json_decode($product['properties'], true));

                                if ((int)$product['is_default_product'] === 1) {
                                    $def = &$product;
                                } else {
                                    $upgrade[] = &$product;
                                }
                            }
                        }

                        if (!empty($upgraded)) {
                            foreach ($upgraded as $up) {
                                if ($up['sub_cat_id'] == $hw_sub_category['id']) {
                                    if ($up['id'] == $def['id']) {
                                        break;
                                    } else {
                                        foreach ($upgrade as $upp) {
                                            if ($up['id'] == $upp['id']) {
                                                $def = $upp;
                                                $def['upgraded'] = 1;
                                            }
                                        }

                                        break;
                                    }
                                }
                            }
                        }

                        $result[] = $def;
                    }
                }
            }

            return $result;
        }

        /**
         * Get products for the server assosiated with cart upgraded products
         *
         * result:
         *
         * Array
         * (
         *     [0] => Array
         *         (
         *             [id] => 1071
         *             [in_configurator] => 1
         *             [in_overview] => 1
         *             [order_index] => 0
         *             [server_id] => 15
         *             [sub_category_id] => 22
         *             [product_id] => 139
         *             [is_default_product] => 0
         *             [product_custom_id] => 494
         *             [name] => AMD Athlon 64 2.30Ghz DualCore 4450e
         *             [short_name] => AMD Athlon DualCore 2.30Ghz
         *             [value] =>
         *             [info] =>
         *             [price] => 1.00
         *             [setup_fee] => 0.00
         *             [setup_id] => 0
         *             [minimal_period] => 1
         *             [technical_description] => xs1m1
         *             [upgraded] => 1
         *         )
         *     [1] => Array
         *         (
         *             [id] => 1003
         *             [in_configurator] => 1
         *             [in_overview] => 1
         *             [order_index] => 0
         *             [server_id] => 15
         *             [sub_category_id] => 1
         *             [product_id] => 56
         *             [is_default_product] => 1
         *             [product_custom_id] => 0
         *             [name] => 320 GB - SATA2 7200rpm
         *             [short_name] => 320 GB SATA2 7200rpm
         *             [value] => 320 GB
         *             [info] =>
         *             [price] => 0.00
         *             [setup_fee] => 0.00
         *             [setup_id] => 0
         *             [minimal_period] => 1
         *             [technical_description] =>
         *         )
         *     [products_monthly_fee] => 1
         *     [products_setup_fee] => 0
         *     [products_minimal_period] => 1
         * )
         *
         * @param string $url
         * @param array $upgraded
         * @return type
         */
        public function getServerDefaultProductsUpgradedWithSum($url, $upgraded)
        {
            $products = $this->getServerDefaultProductsUpgraded($url, $upgraded);
            $monthly_fee = 0;
            $setup_fee = 0;
            $minimal_period = 1;

            foreach ($products as $product) {
                $monthly_fee += $product['price'];
                $setup_fee += $product['setup_fee'];

                if ($product['minimal_period'] > $minimal_period) {
                    $minimal_period = $product['minimal_period'];
                }
            }

            $products['products_monthly_fee'] = $monthly_fee;
            $products['products_setup_fee'] = $setup_fee;
            $products['products_minimal_period'] = $minimal_period;

            return $products;
        }

        /**
         * Filter products set by representation set
         *
         * @param type $products
         * @param type $representation
         * @return type
         */
        public function filterProductsByRepresentation($products, $representation)
        {
            $result = [];

            if (!empty($representation) && !empty($products)) {
                foreach ($products as $product) {
                    $represented = false;

                    foreach ($representation as $representation_item) {
                        if (isset($product['sub_category_id']) &&
                                $product['sub_category_id'] == $representation_item['subcategory_id']) {

                            if ($product['name'] != 'None' && $product['name'] != 'Нет') { // HC
                                $product['subcategory_name'] = $representation_item['subcategory_name'];
                                $result[$representation_item['subcategory_index']] = $product;
                                $represented = true;
                            }
                        }
                    }

                    if (!$represented) {
                        if (!empty($product['upgraded'])) {
                            $product['subcategory_name'] = $this->getServerHardwareSubCategory($product['sub_category_id'])['name'];
                            $result[] = $product;
                        }
                    }
                }
            }

            return $result;
        }

        /**
         * Returns default products with upgrade option, and reduced record of HDD products (2x..3x..)
         *
         * result:
         *
         *     [1] => Array
         *         (
         *             [id] => 883
         *             [in_configurator] => 1
         *             [in_overview] => 1
         *             [order_index] => 0
         *             [server_id] => 14
         *             [sub_category_id] => 1
         *             [product_id] => 131
         *             [is_default_product] => 1
         *             [product_custom_id] => 0
         *             [name] => 4 x 300GB - SAS 6G Enterprise Cheetah 15K.7
         *             [short_name] => 300GB SAS Enterprise 15000rpm
         *             [value] => 4 x 300GB
         *             [info] =>
         *             [price] => 0.00
         *             [setup_fee] => 0.00
         *             [setup_id] => 0
         *             [minimal_period] => 1
         *             [technical_description] => rx300s6
         *             [upgrade] => 0
         *             [upgrade_products] => Array
         *                 (
         *                 )
         *         )
         *
         * @param string $url Server url
         * @param array $subcats Hardware subcategories
         * @return array
         */
        public function getServerDefaultProductsShort($url, $subcats = [])
        {
            $hdd_subcats = [1, 2, 18, 23, 43]; // HC
            $hdd_count = [];
            $info = '';
            $hdd_first_key = null;

            $products = $this->getServerDefaultProducts($url, $subcats);

            if (!empty($products)) {
                foreach ($products as $key => &$product) {
                    if (!empty($product['sub_category_id'])) {
                        if (in_array($product['sub_category_id'], $hdd_subcats)) {
                            if ($product['name'] != 'None' && $product['name'] != 'Нет') { // HC
                                if (empty ($hdd_count[$product['product_id']])) {
                                    $hdd_count[$product['product_id']] = 0;
                                }

                                $hdd_count[$product['product_id']]++;

                                if (!empty($product['info'])) {
                                    $info .= (empty($info)) ? $product['info'] : ' : ' . $product['info'];
                                }
                            }

                            if ($hdd_first_key === null && ((int)$product['sub_category_id'] === 1 || (int)$product['sub_category_id'] === 43)) { // HC
                                $hdd_first_key = $key;
                            } else {
                                unset($products[$key]);
                            }
                        }
                    }
                }

                if (!empty($hdd_count) && $hdd_first_key !== null) {
                    $value = '';

                    foreach ($hdd_count as $key => $hdd) {
                        $value .= (empty($value) ? '' : ' + ') . ($hdd > 1 ? $hdd . 'x' : '')
                            . $this->getServerHardwareProduct($key)['value'];
                    }

                    $products[$hdd_first_key]['value'] = $value;
                    $products[$hdd_first_key]['name'] = $value;
                    $products[$hdd_first_key]['short_name'] = $value;
                    $products[$hdd_first_key]['info'] = $info;
                }
            }

            return $products;
        }

        public function getServerDefaultProductBySubcategory($url, $subcategory_id)
        {
            if ((int)$subcategory_id === 1 || (int)$subcategory_id === 43) { // HC
                $result = $this->getServerDefaultProductsShort($url, [1, 2, 18, 23, 43]); // HC
            } else {
                $result = $this->getServerDefaultProductsShort($url, [$subcategory_id]);
            }

            return $result;
        }

        private function checkPromoCode(&$server_data)
        {
            if (!empty($_SESSION['promocode'])) {
                $promocode = $_SESSION['promocode'];

                if ($promocode['type'] == PROMOCODE_TYPE_JUST_REGISTER) { return false; } // 4 type are w/o discounts

                // Total server discount (including default products)
                $server_data['discount'] = 0;

                // If promocode has connected products then we apply discount ONLY to THEESE products
                if (!empty($promocode['connected_products']) && $promocode['discount_value'] != 0) {
                    $this_server_discount = $this->getDiscountConnectedProductPromoCode(['price' => $server_data['monthly_fee'], 'product_custom_id' => $server_data['server_custom_id']]);

                    if ($this_server_discount > 0) {
                        $server_data['discount'] = $this_server_discount;
                    }

                    if (!empty($server_data['sub_categories'])) { // we came from getServerForConfigurator
                        foreach ($server_data['sub_categories'] as &$subcat) {
                            if (!empty($subcat['products'])) {
                                $this_server_products_discount = $this->checkConnectedProductsPromoCode($subcat['products']);

                                if ($this_server_products_discount > 0) {
                                    $server_data['discount'] += $this_server_products_discount;
                                }
                            }
                        }

                        unset($subcat);
                    } elseif (!empty($server_data['products'])) { // we came from getCartItems
                        $this_server_products_discount = $this->checkConnectedProductsPromoCode($server_data['products']);

                        if ($this_server_products_discount > 0) {
                            $server_data['discount'] += $this_server_products_discount;
                        }
                    }
                // If promocode has no connected products but has discount we apply this discount to ALL DEFAULT products
                } elseif (empty($promocode['connected_products']) && $promocode['discount_value'] != 0) {
                    if ($promocode['discount_type'] == PROMOCODE_DISCOUNT_TYPE_PERCENT) { // %%
                        $server_data['discount'] = round(($server_data['complete_monthly_fee'] * $promocode['discount_value'] / 100),2);
                    } else { // euro
                        $server_data['discount'] = round(abs($promocode['discount_value']),2);
                    }
                }

                if ($server_data['discount'] > 0) {
                    $server_data['discounts_info'] = [[
                        'discount_id' => 'promo_code',
                        'discount_caption' => $promocode['caption_'.$this->_lang],
                        'discount_short_caption' => $promocode['caption_'.$this->_lang],
                        'discount_custom_id' => 0,
                        'discount' => $server_data['discount'],
                        'quantity' => 1,
                        'promo_code' => $promocode['promo_code'],
                        'discount_type' => $promocode['discount_type']
                    ]];
                }

                return true;
            }

            return false;
        }

        private function getDiscountConnectedProductPromoCode($product) {
            $discount = 0;

            if (in_array($product['product_custom_id'], $_SESSION['promocode']['connected_products_ids'])) {
                if ($_SESSION['promocode']['discount_type'] == PROMOCODE_DISCOUNT_TYPE_PERCENT) { // %%
                    $discount = round(($product['price'] * $_SESSION['promocode']['discount_value'] / 100), 2);
                } elseif ($_SESSION['promocode']['discount_type'] == PROMOCODE_DISCOUNT_TYPE_EURO) { // Euro
                    $discount = round(abs($_SESSION['promocode']['discount_value']), 2);
                }
            }

            return $discount;
        }

        private function checkConnectedProductsPromoCode(&$products) {
            $discount = 0;

            if (!empty($products)) {
                foreach ($products as &$product) {
                    $this_product_discount = $this->getDiscountConnectedProductPromoCode($product);

                    if ($this_product_discount > 0) {
                        // Count discount only on default products
                        $discount += $this_product_discount;
                        $product['discount'] = $this_product_discount;
                    }

                    if (!empty($product['upgrade_products'])) {
                        foreach ($product['upgrade_products'] as &$upgrade_product) {
                            $this_product_discount = $this->getDiscountConnectedProductPromoCode($upgrade_product);

                            if ($this_product_discount > 0) {
                                $upgrade_product['discount'] = $this_product_discount;
                            }
                        }
                        unset($upgrade_product);
                    }
                }
                unset($product);
            }

            return $discount;
        }

        /**
         * Getting all server data need for configuration page.
         *
         * result:
         *
         * Array
         * (
         *     [id] => 1
         *     [server_custom_id] => 498
         *     [category_id] => 1
         *     [caption] => Intel Server 2m
         *     [description] => This affordable dedicated server configuration is perfect for running simple applic...
         *     [short_caption] => IS2M
         *     [url] => is2m
         *     [is_active] => 1
         *     [in_stock] => 1
         *     [monthly_fee] => 55.00
         *     [setup_id] => 0
         *     [setup_fee] => 0.00
         *     [server_model] => Fujitsu Primergy TX120 S3
         *     [brand] => Fujitsu
         *     [group_id] => 1
         *     [discount] => 0
         *     [products_monthly_fee] => 0.00
         *     [products_setup_fee] => 0.00
         *     [complete_monthly_fee] => 55
         *     [complete_setup_fee] => 0.00
         *     [group_info] => Array
         *         (
         *             [id] => 1
         *             [order_index] => 100
         *             [group_name] => Performance servers
         *         )
         *     [location_info] => Array
         *         (
         *             [0] => Array
         *                 (
         *                     [id] => 1
         *                     [location_name] => Luxembourg
         *                     [location_name_en] => Luxembourg
         *                     [location_code] => LU
         *                 )
         *         )
         *     [categories] => Array
         *         (
         *             [0] => Array
         *                 (
         *                     [id] => 8
         *                     [is_active] => 1
         *                     [order_index] => 10
         *                     [in_configurator] => 1
         *                     [name] => CPU Configuration
         *                     [short_name] => CPU
         *                     [as_header] => 0
         *                     [in_overview] => 1
         *                 )
         *         )
         *     [sub_categories] => Array
         *         (
         *             [0] => Array
         *                 (
         *                     [id] => 22
         *                     [server_id] => 1
         *                     [is_active] => 1
         *                     [is_required] => 1
         *                     [in_configurator] => 1
         *                     [in_overview] => 1
         *                     [category_id] => 8
         *                     [name] => CPU
         *                     [products] => Array
         *                         (
         *                             [0] => Array
         *                                 (
         *                                     [id] => 875
         *                                     [in_configurator] => 1
         *                                     [in_overview] => 1
         *                                     [order_index] => 0
         *                                     [server_id] => 1
         *                                     [sub_category_id] => 22
         *                                     [product_id] => 123
         *                                     [is_default_product] => 1
         *                                     [product_custom_id] => 498
         *                                     [equivalent_quantity] => 1
         *                                     [name] => Intel Core i3-2100 2x3.10GHz 3MB L3 Cache
         *                                     [short_name] => Intel Core i3 Dual-core
         *                                     [value] => Intel Core i3 Dual-core
         *                                     [info] =>
         *                                     [price] => 0.00
         *                                     [setup_fee] => 0.00
         *                                     [setup_id] => 0
         *                                     [minimal_period] => 1
         *                                     [technical_description] => is2k
         *                                     [upgrade] => 0
         *                                     [upgrade_products] => Array
         *                                         (
         *                                             [0] => Array
         *                                                 (
         *                                                     [id] => 787
         *                                                     [in_configurator] => 1
         *                                                     [in_overview] => 1
         *                                                     [order_index] => 10
         *                                                     [server_id] => 1
         *                                                     [sub_category_id] => 1
         *                                                     [product_id] => 3
         *                                                     [is_default_product] => 0
         *                                                     [product_custom_id] => 428
         *                                                     [equivalent_quantity] => 1
         *                                                     [name] => 120 GB - Solid State Drive
         *                                                     [short_name] => 1 x 120 GB SSD
         *                                                     [value] => 120 GB SSD
         *                                                     [info] => This is a 120 GB - Solid State Drive...
         *                                                     [price] => 20.00
         *                                                     [setup_fee] => 25.00
         *                                                     [setup_id] => 1
         *                                                     [minimal_period] => 3
         *                                                     [technical_description] =>
         *
         * @param string $url
         * @return array
         */
        public function getServerForConfigurator($url)
        {
            $server_data = $this->getServerWithCalculatedPrice($url);

            if ($server_data !== false) {
                if ($server_data['complete_monthly_fee'] !=
                        $this->getServerWithCalculatedPrice($url, true)['complete_monthly_fee'] ||
                        $server_data['complete_setup_fee'] !=
                        $this->getServerWithCalculatedPrice($url, true)['complete_setup_fee']) {
                    throw new \Exception('ProductModel: Different config prices, check server: ' . $url, 10010);
                }

                $products = $this->getServerDefaultProducts($url);
                $categories = $this->getServerHardwareCategories($url);
                $sub_categories = $this->getServerHardwareSubCategories($url);

                if (!empty($sub_categories)) {
                    foreach ($sub_categories as $key => $sub_category) {
                        $sub_categories[$key]['products'] = [];

                        if (!empty($products)) {
                            foreach ($products as $product) {
                                if (isset($product['sub_category_id'])) {
                                   if ($product['sub_category_id'] == $sub_category['id'] &&
                                        (int)$product['in_configurator'] === 1) {
                                        $sub_categories[$key]['products'][] = $product;
                                    }
                                }
                            }
                        }

                        if (empty($sub_categories[$key]['products'])) {
                            unset($sub_categories[$key]);
                        }
                    }
                }

                $server_data['categories'] = $categories;
                $server_data['sub_categories'] = $sub_categories;

                //
                $this->checkPromoCode($server_data);  // if promocode applicable to this server change discount
                $server_data['complete_monthly_fee'] = $server_data['complete_monthly_fee'] - $server_data['discount'];

                if ($server_data['complete_monthly_fee'] < 0) {
                    $server_data['complete_monthly_fee'] = 0;
                }
                //

                $server_data['total_price'] = $server_data['complete_monthly_fee'] +
                        $server_data['complete_setup_fee'];

                $server_data['cross_dep'] = $this->getConfiguratorProductCrossDependencies();
            }

            return $server_data;
        }

        /**
         * Getting server for the server detail page
         *
         * @param string $url Server url
         * @return array
         */
        public function getServerForDetails($url)
        {
            $server_data = $this->getServerForConfigurator($url);

            if (!empty($server_data)) {
                $products = $this->getServerDefaultProducts($url);
                $representation = $this->getCategoryRepresentation($server_data['category_id'], 'details');
                $server_data['products_reduced'] = $this->filterProductsByRepresentation($products, $representation);
            }

            return $server_data;
        }

        private function compareServers($a, $b)
        {
            $sa = $a['complete_monthly_fee'];
            $sb = $b['complete_monthly_fee'];

            if ($sa == $sb) {
                return 0;
            }

            return ($sa > $sb) ? 1 : -1;
        }

        /**
         * Getting servers by category id.
         *
         * @param int $category_id Category ID
         * @return array
         */
        public function getServersForServersPageByCategory($category_id, $page_name)
        {
            $servers = $this->getServers();
            $result = [];
            $representation = $this->getCategoryRepresentation($category_id, $page_name);

            if (!empty($representation)) {
                foreach ($servers as $server) {
                    if ($server['category_id'] == $category_id && $server['in_stock'] == 1) {
                        $server_info = $this->getServerForConfigurator($server['url']);

                        $comment = new \Models\Comment();
                        $rating = $comment->getServerRating($server["id"]);
                        $server_info['rating'] = $rating['server_rating'];

                        foreach ($representation as $field) {
                            $server_info[$field['subcategory_index']] = $this->getServerDefaultProductBySubcategory($server['url'], $field['subcategory_id']);
                        }

                        $index = (empty($server_info['group_info']) ? 0 : $server_info['group_info']['order_index']);
                        $result[$index][] = $server_info;
                    }
                }
            }

            if (!empty($result)) {
                foreach ($result as &$value) {
                    uasort($value, [$this, 'compareServers']);
                }
            }

            return $result;
        }

        /**
         * Add an item to the cart.
         *
         * @param array $data contains an item components ids and its quantity
         */
        public function addItemToCart($data)
        {
            $alreadyExist = false;

            if (!empty($data["server_url"]) && $data["quantity"] !== null) {
                if (!isset($_SESSION["cart"])) {
                    $_SESSION["cart"] = [];
                }

                if (count($_SESSION["cart"]) !== 0) {
                    foreach ($_SESSION["cart"] as $key => $item) {
                        if ($data["server_url"] == $item["server_url"] &&
                                $data["server_location"] == $item["server_location"]) {
                            if ($data["products"] == $item["products"]) {
                                $alreadyExist = $key;
                            }
                        }
                    }
                }

                if ($alreadyExist !== false) {
                    $_SESSION["cart"][$alreadyExist]["quantity"] += $data["quantity"];
                } else {
                    array_push($_SESSION["cart"], $data);
                }

                return true;
            }

            return false;
        }

        /**
         * Gets array of servers (items) placed in the cart
         *
         * result:
         *
         * Array
         * (
         *     [0] => Array
         *         (
         *             [id] => 1
         *             [server_custom_id] => 498
         *             [category_id] => 1
         *             [caption] => Intel Server 2m
         *             [description] => This affordable dedicated...
         *             [short_caption] => IS2M
         *             [url] => is2m
         *             [is_active] => 1
         *             [in_stock] => 1
         *             [monthly_fee] => 55.00
         *             [setup_id] => 0
         *             [setup_fee] => 0.00
         *             [server_model] => Fujitsu Primergy TX120 S3
         *             [brand] => Fujitsu
         *             [group_id] => 1
         *             [discount] => 10
         *             [discounts_info] => Array
         *                 (
         *                     [0] => Array
         *                         (
         *                             [server_product_id] => 498
         *                             [server_caption] => Intel Server 2m
         *                             [discount_id] => 1
         *                             [discount_caption] => Special "HOT SUMMER 2013" prices!
         *                             [discount_short_caption] => Special summer discount
         *                             [discount_custom_id] => 422
         *                             [discount] => 10.00
         *                             [quantity] => 1
         *                         )
         *                 )
         *             [complete_monthly_fee] => 45
         *             [complete_setup_fee] => 0
         *             [group_info] => Array
         *                 (
         *                     [id] => 1
         *                     [order_index] => 100
         *                     [group_name] => Performance servers
         *                 )
         *             [location_info] => Array
         *                 (
         *                     [0] => Array
         *                         (
         *                             [id] => 1
         *                             [location_name] => Luxembourg
         *                             [location_name_en] => Luxembourg
         *                             [location_code] => LU
         *                         )
         *                 )
         *             [server_location] => Array
         *                 (
         *                     [id] => 1
         *                     [location_name] => Luxembourg
         *                     [location_name_en] => Luxembourg
         *                     [location_code] => LU
         *                 )
         *             [products_monthly_fee] => 0
         *             [products_setup_fee] => 0
         *             [products_minimal_period] => 1
         *             [complete_server_price] => 45
         *             [quantity] => 1
         *             [comment] =>
         *             [products] => Array
         *                 (
         *                     [0] => Array
         *                         (
         *                             [id] => 875
         *                            [in_configurator] => 1
         *                             [in_overview] => 1
         *                             [order_index] => 0
         *                             [server_id] => 1
         *                             [sub_category_id] => 22
         *                             [product_id] => 123
         *                             [is_default_product] => 1
         *                             [product_custom_id] => 498
         *                             [equivalent_quantity] => 1
         *                             [name] => Intel Core i3-2100 2x3.10GHz 3MB L3 Cache
         *                             [short_name] => Intel Core i3 Dual-core
         *                             [value] => Intel Core i3 Dual-core
         *                             [info] =>
         *                             [price] => 0.00
         *                             [setup_fee] => 0.00
         *                             [setup_id] => 0
         *                             [minimal_period] => 1
         *                             [technical_description] => is2k
         *                         )
         * 			...
         *                 )
         *             [cart_item_id] => 0
         *         )
         * )
         *
         * @return array
         */
        public function getCartItems()
        {
            $result = [];
            $cart = empty($_SESSION['cart']) ? [] : $_SESSION['cart'];

            if (!empty($cart)) {
                foreach ($cart as $key => $item) {
                    $server = $this->getServerWithCalculatedPrice($item['server_url']);
                    $result_item = $server;

                    $result_item['server_location'] = $this->getServerLocationByName($server['id'], $item['server_location']);

                    $products = $this->getServerDefaultProductsUpgradedWithSum(
                        $server['url'],
                        (empty($item['products']) ? [] : $item['products'])
                    );

                    $result_item['products_monthly_fee'] = $products['products_monthly_fee'];
                    $result_item['products_setup_fee'] = $products['products_setup_fee'];

                    $result_item['products_minimal_period'] = $products['products_minimal_period'];

                    unset($products['products_monthly_fee']);
                    unset($products['products_setup_fee']);
                    unset($products['products_minimal_period']);

                    $result_item['products'] = $products;
                    //
                    $this->checkPromoCode($result_item);  // if promocode applicable to this server change discount
                    $result_item['complete_monthly_fee'] = $result_item['monthly_fee'] + $result_item['products_monthly_fee'] - $result_item['discount'];

                    if ($result_item['complete_monthly_fee'] < 0) {
                        $result_item['complete_monthly_fee'] = 0;
                    }
                    //

                    $result_item['complete_setup_fee'] = $server['setup_fee'] + $result_item['products_setup_fee'];

                    $result_item['complete_server_price'] = $result_item['complete_monthly_fee'] +
                            $result_item['complete_setup_fee'];

                    $result_item['quantity'] = $item['quantity'];
                    $result_item['comment'] = $item['comment'];

                    $result_item['cart_item_id'] = $key;
                    $result[$key] = $result_item;
                }
            }

            return $result;
        }

        public function getCartItemForConfigurator($cart_item_id)
        {
            $items = $this->getCartItems();

            if (!empty($items) && array_key_exists($cart_item_id, $items) && !empty($items[$cart_item_id]['products'])) {
                foreach ($items[$cart_item_id]['products'] as $key => $product) {
                    if ((int)$product['in_configurator'] !== 1) {
                        unset($items[$cart_item_id]['products'][$key]);
                    }
                }

                return $items[$cart_item_id];
            } else {
                return false;
            }
        }

        public function getCartItemsForPresentation()
        {
            $cart = $this->getCartItems();

            if (!empty($cart)) {
                foreach ($cart as &$item) {
                    $representation = $this->getCategoryRepresentation($item['category_id'], 'cart');
                    $item['products'] = $this->filterProductsByRepresentation($item['products'], $representation);
                }
            }

            return $cart;
        }

        /**
         * Gets converter items from the cart
         *
         * input cart format:
         *
         * Array
         * (
         *     [server_url] => vs1k
         *     [quantity] => 1
         *     [period] => 1
         *     [products] => Array
         *         (
         *             [0] => Array
         *                 (
         *                     [sub_cat_id] => 22
         *                     [id] => 1226
         *                 )
         *             ...
         *
         *             [5] => Array
         *                 (
         *                     [sub_cat_id] => 19
         *                     [id] => 752
         *                 )
         *             [6] => Array
         *                 (
         *                     [sub_cat_id] => 28
         *                     [id] => 1459
         *                 )
         *         )
         * )
         *
         * result:
         *
         * Array
         * (
         *     [0] => Array
         *         (
         *             [quantity] => 1
         *             [server] => 1
         *             [minimal_period] => 3
         *             [options] => Array
         *                 (
         *                     [0] => 431
         *                     [1] => 428
         *                     [2] => 430
         *                     [3] => 382
         *                     [4] => 43
         *                     [5] => 477
         *                 )
         *             [comment] => Release ASAP!
         *             [tech_comment] =>
         * Server: Intel Server 2m
         * First drive: 120 GB - Solid State Drive
         * Operational System: Debian (7.0)
         *             [id_product] => 498
         *         )
         * )
         *
         * @return array
         */
        public function getCartItemsForOrder()
        {
            // Set all data to English
            $api = \Models\Api::getInstance();
            $lang = $this->_lang;
            $this->_lang = 'en';

            $order = $this->getCartItems();
            $converted_order = [];
            $result = false;

            if (!empty($order)) {
                foreach ($order as $key => $item) {
                    if (($item['monthly_fee'] + $item['products_monthly_fee'] - $item['discount']) <= 0) {
                        $smarty_mail = new \System\SmartyMail();
                        $smarty_mail->From = EML_NOREPLY;
                        $smarty_mail->FromName = "company.com - Order";
                        $smarty_mail->AddAddress(EML_SUPPORT);
                        $smarty_mail->Subject = "company.com - Wrong configuration detected.";

                        $sent_status = $smarty_mail->SendSmartyLetter([
                            'server' => $item,
                            'user' => $_SESSION['user_logged_profile']
                                ], 'wrong_configuration.tpl', true);

                        $this->cartItemRemove($item['cart_item_id']);

                        $report = [
                            'type'        => 'email',
                            'timestamp'   => time(),
                            'sent_status' => $sent_status ? 1 : 0,
                            'data'        => [
                                'subject'      => $smarty_mail->Subject,
                                'to'           => EML_SUPPORT,
                                'from'         => $smarty_mail->From,
                                'content-type' => 'html',
                                'content'      => $smarty_mail->Body
                            ]
                        ];

                        $api->notification('insertNotificationEvent', ['data' => $report]);

                        continue; // We remove wrong item
                    }

                    $comment_pattern = [];
                    $comment_pattern['server'] = $item['caption'];
                    $comment_pattern['location'] = $item['server_location']['location_name_en'];

                    $converted_order[$key]['quantity'] = $item['quantity'];
                    $converted_order[$key]['server'] = true;
                    $converted_order[$key]['server_location'] = $item['server_location']['location_code'];
                    $converted_order[$key]['minimal_period'] = $item['products_minimal_period'];

                    $promocode = [];
                    $cpu_id = false;
                    $server_category_id = (int)$item['category_id'];

                    // Discounts & Promocode mechanism part 1
                    if ((float)$item['discount'] > 0) {
                        foreach ($item['discounts_info'] as $discount) {
                            if ($discount['discount_id'] != 'promo_code') {
                                $converted_order[$key]['options'][] = [
                                    'id_product' => $discount['discount_custom_id'],
                                    'quantity' => $discount['quantity']
                                    ];
                            } elseif ($discount['discount_id'] == 'promo_code') {
                                $promocode = $_SESSION['promocode'];
                                $converted_order[$key]['promocode'] = $promocode['id'];

                                if ($promocode['discount_type'] == PROMOCODE_DISCOUNT_TYPE_EURO) { // euro
                                    if (empty($promocode['connected_products_ids'])) { // If NOT connected
                                        foreach ($promocode['discount_products'] as $discount_product) {
                                            $converted_order[$key]['options'][] = [
                                                'id_product' => $discount_product['product_id'],
                                                'quantity' => $discount_product['quantity']
                                                ];
                                        }
                                    }
                                }
                            }
                        }
                    }

                    foreach ($item['products'] as $product) {
                        $current_product_custom_id = (int)$product['product_custom_id'];
                        $product_sub_category_id = (int)$product['sub_category_id'];

                        if ($product_sub_category_id === 22 && $server_category_id !== 2) { // subcat22=CPU,servercat2=Virtual servers
                            $cpu_id = $current_product_custom_id;
                        }

                        if ($product_sub_category_id === 22 && $server_category_id === 2) { // subcat22=CPU,servercat2=Virtual servers
                            $converted_order[$key]['options'][] = $this->getOrderOptionWithDiscount($product, $converted_order[$key]['options'], $promocode);
                        }

                        if ($current_product_custom_id !== 0) {
                            if ((int)$product['product_id'] !== 18 && $product_sub_category_id !== 22) { // id18=1xIPv4,subcat22=CPU
                                $converted_order[$key]['options'][] = $this->getOrderOptionWithDiscount($product, $converted_order[$key]['options'], $promocode);
                            }
                        }

                        if ((int)$product['setup_id'] !== 0) {
                            $current_product_setup = $this->getSetupFees($product['setup_id'])[0];
                            if (!empty($current_product_setup)) {
                                $converted_order[$key]['options'][] = $current_product_setup['setup_custom_id'];
                            }
                        }

                        if ($product_sub_category_id === 21) {
                            $bootable_drive = $product['name'];
                        }
                    }

                    $representation = $this->getCategoryRepresentation($item['category_id'], 'cart');
                    $item['comment_products'] = $this->filterProductsByRepresentation($item['products'], $representation);

                    if (!empty($item['comment_products'])) {
                        foreach ($item['comment_products'] as $comment_product) {
                            $comment_pattern[$comment_product['subcategory_name']] = $comment_product['name'];
                        }
                    }

                    if (isset($bootable_drive) && !empty($bootable_drive)) {
                        $comment_pattern['bootable drive'] = $bootable_drive;
                    }

                    $converted_order[$key]['comment']      = $item['comment'];
                    $converted_order[$key]['tech_comment'] = json_encode($comment_pattern);

                    $result_server = $this->getOrderOptionWithDiscount([
                                'is_default_product' => 1,
                                'product_custom_id' => $item['server_custom_id'],
                                'equivalent_quantity' => 1],
                            $converted_order[$key]['options'], $promocode);

                    if (!empty($result_server['discount'])) {
                        $converted_order[$key]['discount'] = $result_server['discount'];
                    }

                    $converted_order[$key]['id_product']   = (!empty($cpu_id)) ? $cpu_id : $item['server_custom_id'];
                }

                $result = $converted_order;
            }
            // Set language back
            $this->_lang = $lang;

            return $result;
        }

        private function getOrderOptionWithDiscount($current_item_product, &$options, $promocode) {
            $current_product_custom_id = (int)$current_item_product['product_custom_id'];
            $result = [
                'id_product' => $current_product_custom_id,
                'quantity' => (int)$current_item_product['equivalent_quantity']
            ];

            // Discounts & Promocode mechanism part 2
            if (!empty($promocode)) {
                if ($promocode['discount_type'] == PROMOCODE_DISCOUNT_TYPE_PERCENT) { // %%

                    if (!empty($promocode['connected_products_ids'])) { // If connected
                        if (in_array($current_product_custom_id, $promocode['connected_products_ids'])) {
                            $result['discount'] = $promocode['discount_value'];
                        }
                    } else { // To all order (only default products)
                        if ((int)$current_item_product['is_default_product'] === 1) {
                            $result['discount'] = $promocode['discount_value'];
                        }
                    }

                } elseif ($promocode['discount_type'] == PROMOCODE_DISCOUNT_TYPE_EURO) { // euro

                    if (!empty($promocode['connected_products_ids'])) { // If connected
                        if (in_array($current_product_custom_id, $promocode['connected_products_ids'])) {
                            foreach ($promocode['discount_products'] as $discount_product) {
                                $options[] = [
                                    'id_product' => $discount_product['product_id'],
                                    'quantity' => $discount_product['quantity']
                                    ];
                            }
                        }
                    }
                }
            }

            return $result;
        }

        /**
         * Remove selected item from cart.
         *
         * @param int $cart_item_id item id for removing
         * @return bool
         */
        public function saveCartItemComment($cart_item_id, $comment)
        {
            if (array_key_exists((int)$cart_item_id, $_SESSION["cart"])) {
                $_SESSION["cart"][$cart_item_id]['comment'] = $comment;

                return true;
            } else {
                return false;
            }
        }

        /**
         * Remove selected item from cart.
         *
         * @param int $cart_item_id item id for removing
         * @return bool
         */
        public function cartItemRemove($cart_item_id)
        {
            if (array_key_exists((int)$cart_item_id, $_SESSION["cart"])) {
                unset($_SESSION["cart"][$cart_item_id]);
                $_SESSION["cart"] = array_values($_SESSION["cart"]);
                //$user = new \Models\User();
                //$user->changeUserNotificationData();

                return true;
            } else {
                return false;
            }
        }

        /**
         * Change quantity of selected item.
         *
         * @param int $cart_item_id item id for quantity changing
         * @param int $number quantity value for selected item
         * @return bool
         */
        public function changeCartItemQuantity($cart_item_id, $number)
        {
            if (is_int($cart_item_id) && is_int($number)) {
                $_SESSION['cart'][$cart_item_id]['quantity'] = (int)$number;
                if ($_SESSION['cart'][$cart_item_id]['quantity'] <= 0) {
                    $this->cartItemRemove($cart_item_id);
                }
                //$user = new \Models\User();
                //$user->changeUserNotificationData();

                return true;
            } else {
                return false;
            }
        }

        /**
         * Total items amount in cart.
         *
         * @return array of items amount and "products" declension
         */
        public function getProductsCountInCart()
        {
            $count = 0;
            $products_declension = "";

            if (isset($_SESSION["cart"])) {
                foreach ($_SESSION["cart"] as $value) {
                    $count += $value["quantity"];
                }

                if ($this->_lang == "ru") {
                    $forms = array('Продукт', 'Продукта', 'Продуктов', 'Корзина пуста');
                    $products_declension = ($count == 0) ? $forms[3] : (($count % 10 == 1 && $count % 100 != 11) ?
                                    $forms[0] : ($count % 10 >= 2 && $count % 10 <= 4 &&
                                    ($count % 100 < 10 || $count % 100 >= 20 ) ? $forms[1] : $forms[2]));
                } else {
                    $forms = array('Item', 'Items', 'Cart is empty');
                    $products_declension = ($count == 0) ? $forms[2] : ($count > 1 ? $forms[1] : $forms[0]);
                }
            }

            return array("count" => $count, "products_declension" => $products_declension);
        }

        /**
         * Edit selected cart item.
         *
         * @param  int $id item id for editing
         * @return array|false
         */
        public function cartItemEdit($id)
        {
            if (array_key_exists($id, $_SESSION["cart"])) {
                if (!isset($_POST["data"])) {
                    $item = $_SESSION["cart"][$id];
                    $products = $this->getServerHardwareProducts();
                    $currentServer = $this->getServer($item["server_url"]);
                    $modifiedItem = array("server_url" => $item["server_url"],
                        "id" => $id,
                        "quantity" => $item["quantity"],
                        "products" => array()
                    );

                    foreach ($item["products"] as $server) {
                        foreach ($products as $product) {
                            if ($server["sub_cat_id"] == $product["sub_category_id"] &&
                                    $server["id"] == $product["id"] && $product["server_id"] == $currentServer["id"]
                            ) {
                                $product['properties'] = (empty($product['properties']) ? [] : json_decode($product['properties'], true));
                                $modifiedItem["products"][] = $product;
                            }
                        }
                    }

                    return $modifiedItem;
                } else {
                    if ($_POST["data"] !== '') {
                        $_SESSION["cart"][$id] = json_decode($_POST["data"], true);
                    }
                }
            } else {
                return false;
            }
        }

        private function comparePresets($a, $b)
        {
            $sa = $a['details']['complete_monthly_fee'];
            $sb = $b['details']['complete_monthly_fee'];

            if ($sa == $sb) {
                return 0;
            }

            return ($sa > $sb) ? 1 : -1;
        }

        /**
         * Get servers width presets categories.
         *
         * @return array|false
         */
        public function getPresets()
        {
            $sql = "SELECT * FROM `presets`";

            $presets = \Models\Db::getInstance()->getAll($sql, USE_CACHE);
            $servers = [];

            if (!empty($presets)) {
                foreach ($presets as $preset) {
                    $servers[] = [
                        "preset"  => $preset['preset_id'],
                        "details" => $this->getServerForConfigurator($this->getServerById($preset['server_id'])['url'])
                    ];
                }
            }

            $tmp = [];

            foreach ($servers as $server) {
                $tmp[] = $server['details']['category_id'];
            }

            $categories = array_unique($tmp);
            $result = [];

            foreach ($categories as $category) {
                $representation = [];
                $representation = $this->getCategoryRepresentation($category, 'details');

                if (!empty($representation)) {
                    foreach ($servers as $server) {
                        if ($server['details']['category_id'] === $category) {
                            $server_info = $this->getServerForConfigurator($server['details']['url']);

                            foreach ($representation as $field) {
                                $server_info[$field['subcategory_index']] = $this->getServerDefaultProductBySubcategory($server['details']['url'], $field['subcategory_id']);
                            }

                            $index = (empty($server_info['group_info']) ? 0 : $server_info['group_info']['order_index']);
                            $result[] = [
                                "preset_id" => $server['preset'],
                                "details"   => $server_info
                            ];
                        }
                    }
                }
            }

            uasort($result, [$this, 'comparePresets']);

            return $result;
        }

    }
}
