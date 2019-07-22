<?php

/**
 * OrderController
 *
 * @author dev. Dmitry Kamyshov <dk@company.com>
 * @package Controller
 * @version 2.0.0
 */

namespace Controllers {

    class OrderController
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
                self::$instance = new OrderController();
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
            throw new \Exception('OrderController: No main action is set', 404);
        }

        public function configureAction()
        {
            // custom URL: /configure/xs1m/
            //             /edit/0/
            if (!isset($_REQUEST[0]) || count($_REQUEST) != 2) {
                // if there is more or less than 2 parameters
                throw new \Exception('OrderController: Wrong parameters', 404);
            }

            if (!empty($_REQUEST['edit'])) {
                if (empty($_SESSION['cart'])) {
                    \System\Helper::redirect(SITE_URL_ROOT);
                }

                $cart_item_id = (int)$_REQUEST[0];

                if (key_exists($cart_item_id, $_SESSION['cart'])) {
                    $this->_tpl->assign("server_configured", 1);
                    $this->_tpl->assign("cart_item_id", $cart_item_id);
                    $this->_tpl->assign("server_url", $_SESSION['cart'][$cart_item_id]['server_url']);
                } else {
                    throw new \Exception('OrderController: No such item', 404);
                }
            } else {
                $server_url = $_REQUEST[0];
                $product = new \Models\Product();
                $server_data = $product->getServer($server_url);

                if (!empty($server_data) && !empty($server_data['in_stock'])) {
                    $this->_tpl->assign("server_configured", 0);
                    $this->_tpl->assign("server_url", $server_url);
                } else {
                    throw new \Exception('OrderController: No such server', 404);
                }

            }
        }

        public function cartAction()
        {
            if (empty($_SESSION['cart'])) {
                \System\Helper::redirect(SITE_URL_ROOT);
            }
        }

        public function orderAction()
        {
            if (empty($_SESSION['cart']) && empty($_SESSION['order_payment_finalize'])) {
                \System\Helper::redirect(SITE_URL_ROOT);
            }

            if (!empty($_SESSION['user_logged'])) {
               $this->_tpl->assign('user_profile', $_SESSION['user_logged_profile']);
            }

            if (empty($_SESSION['order_payment_finalize'])) {
                // cart data and all vars we already have from loadAllPagesCommonConfiguration()
                // but we have to checkout either user VAT payer or not
                $this->_tpl->assign('order_payment_finalize', 0);
            } else {
                $this->_tpl->assign('order_payment_finalize', 1);
                unset($_SESSION['order_payment_finalize']);
            }

            $this->_tpl->assign('page_title', ($_SESSION['user']['lang'] == 'en' ? 'Checkout' : 'Оплата'));
        }

        public function cancelPaymentAction()
        {
            if ($_SESSION['order_payment_status'] > 0) {
                $this->_api->order('setPaymentStatus', [
                    'order_pay_hash' => $_SESSION['order_pay_hash'],
                    'status' => 0 // Cancelled
                ]);
                \Models\User::clearPaymentSession(false);
                \System\Helper::redirect('/order/');
            }
        }

    }
}
