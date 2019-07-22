<?php

/**
 * AjaxController
 *
 * @author dev. Dmitry Kamyshov <dk@company.com>
 * @package Controller
 * @version 2.0.0
 */

namespace Controllers {

    class AjaxController
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
                self::$instance = new AjaxController();
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

        public function mainAction()
        {
            throw new \Exception('AjaxController: No main action is set', 404);
        }

        public function addNewFaqCommentAction()
        {
            if (($data = \System\Helper::getJSONParameters()) !== false) {
                $faq = new \Models\Faq();
                $comment = new \Models\Comment();

                if (isset($data['comment']) && !empty($data['comment'])) {
                    $comment_json = $data['comment'];
                    $comment->notifyOnComment($comment_json, 'FAQ article Id:' . $comment_json['articleId']);

                    echo $faq->addComment($comment_json);

                    return;
                }
            }

            echo 0;
        }

        public function addFaqReplyAction()
        {
            if (($data = \System\Helper::getJSONParameters()) !== false) {
                $faq = new \Models\Faq();
                $comment = new \Models\Comment();

                if (isset($data['reply']) && !empty($data['reply'])) {
                    $reply_json = $data['reply'];
                    $comment->notifyOnComment($reply_json, 'FAQ article Id:' . $reply_json['articleId']);

                    echo $faq->addReply($reply_json);

                    return;
                }
            }

            echo 0;
        }

        /**
         * Add an item to the cart
         *
         * input data:
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
         * @return int 0|1 Success
         */
        public function addCartItemAction()
        {
            if (($data = \System\Helper::getJSONParameters()) !== false) {
                $product = new \Models\Product();

                if (!empty($data['order_data']) && !empty($data['server_url']) && !empty($data['quantity'])) {
                    $default_products = $product->getServerDefaultProducts($data['server_url']);
                    $min_period = 1;

                    if (!empty($default_products)) {
                        $item = ['products' => []];
                        $selected_products = [];

                        foreach ($default_products as $default_product) {
                            foreach ($data['order_data'] as $ordered_product) {
                                if ($default_product['id'] == $ordered_product['id']) { // Default product ordered
                                    $item['products'][] = [
                                        'sub_cat_id' => $default_product['sub_category_id'],
                                        'id' => $default_product['id']
                                            ];
                                    $selected_products[] = $default_product;
                                } else {
                                    if (!empty($default_product['upgrade_products'])) {
                                        foreach ($default_product['upgrade_products'] as $upgrade_product) {
                                            if ($upgrade_product['id'] == $ordered_product['id']) { // Upgraded product ordered
                                                $item['products'][] = [
                                                    'sub_cat_id' => $upgrade_product['sub_category_id'],
                                                    'id' => $upgrade_product['id']
                                                        ];
                                                $selected_products[] = $upgrade_product;
                                                if ($upgrade_product['minimal_period'] > $min_period) {
                                                    $min_period = (int)$upgrade_product['minimal_period'];
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }

                        if (!empty($item['products'])) {
                            if ($min_period > $_SESSION['min_period']) {
                                $_SESSION['min_period'] = $min_period;
                            }

                            if ($data['quantity'] > 0 && $data['quantity'] < 11) {
                                $item['quantity'] = $data['quantity'];
                            } else {
                                $item['quantity'] = 1;
                            }

                            $item['server_url'] = $data['server_url'];
                            $item['server_location'] = $data['location'];
                            $item['comment'] = isset($data['comment']) ? $data['comment'] : '';

                            if (!empty($data['edit']) && isset($data['cart_item_id'])) {
                                $_SESSION['cart'][$data['cart_item_id']] = $item;
                                echo 1;

                                return;
                            } else {
                                echo (int)$product->addItemToCart($item);

                                return;
                            }


                        }
                    }
                }
            }
            echo 0;
        }

        public function getCartDataAction()
        {
            $cart_items = (new \Models\Product())->getCartItemsForPresentation();

            if (!empty($cart_items)) {
                $result['cart'] = $cart_items;
                $result['min_period'] = $_SESSION['min_period'];

                echo json_encode($result);
            } else {
                echo 0;
            }
        }

        /**
         * Get cart items for mini cart in the header
         *
         * Uses template getCartItems.tpl for output
         *
         * @return html
         */
        public function getCartItemsAction()
        {
            $product = new \Models\Product();
            $cart_items = $product->getCartItemsForPresentation();

            $discount = 0;
            $complete_monthly = 0;
            $monthly = 0;
            $setup = 0;
            $min_period = 1;
            $discounts = [];

            if (!empty($cart_items)) {
                foreach ($cart_items as $item) {
                    $discount += $item['discount'] * $item['quantity'];
                    $monthly += ($item['monthly_fee'] + $item['products_monthly_fee']) * $item['quantity'];
                    $complete_monthly += $item['complete_monthly_fee'] * $item['quantity'];
                    $setup += $item['complete_setup_fee'] * $item['quantity'];

                    if ($item['products_minimal_period'] > $min_period) {
                        $min_period = $item['products_minimal_period'];
                    }

                    if ($item['discount'] > 0) {
                        foreach ($item['discounts_info'] as $discount_item) {
                            if (array_key_exists($discount_item['discount_id'], $discounts)) {
                                $discounts[$discount_item['discount_id']]['discount'] += ($discount_item['discount'] * $item['quantity']);
                            } else {
                                $discounts[$discount_item['discount_id']] = [
                                    'discount_caption'       => $discount_item['discount_caption'],
                                    'discount_short_caption' => $discount_item['discount_short_caption'],
                                    'discount_custom_id'     => $discount_item['discount_custom_id'],
                                    'discount'               => ($discount_item['discount'] * $item['quantity']),
                                    'quantity'               => $discount_item['quantity']
                                ];
                            }
                        }
                    }
                }
            }

            $total = ($complete_monthly * $min_period) + $setup;

            $count = $product->getProductsCountInCart();
            $this->_tpl->assign('cart_items', $cart_items);
            $this->_tpl->assign('cart_total', $total);
            $this->_tpl->assign('cart_count', $count);
            $this->_tpl->assign('discount', $discount);
            $this->_tpl->assign('discounts', $discounts);
            $this->_tpl->assign('complete_monthly', $complete_monthly);
            $this->_tpl->assign('monthly', $monthly);
            $this->_tpl->assign('setup', $setup);
            $this->_tpl->assign('min_period', $min_period);

            $_SESSION['min_period'] = $min_period;
        }

        /**
         * Change quantity of items in the cart
         * @return (0|1)
         */
        public function changeCartItemQuantityAction()
        {
            if (($data = \System\Helper::getJSONParameters()) !== false && $_SESSION['order_payment_status'] == 0) {
                $product = new \Models\Product();
                $result['errorcode'] = $product->changeCartItemQuantity($data['cart_item_id'], $data['number']); // ok
                $result['cart_count'] = $product->getProductsCountInCart();
                echo json_encode($result);
            } else {
                echo 0; // error
            }
        }

        /**
         * Remove an item in the cart
         * @return (0|1)
         */
        public function removeCartItemAction()
        {
            if (($data = \System\Helper::getJSONParameters()) !== false && $_SESSION['order_payment_status'] == 0) {
                $product = new \Models\Product();
                echo (int)$product->cartItemRemove($data['cart_item_id']); // ok
            } else {
                echo 0; // error
            }
        }

        /**
         * Saves comment on item in the cart
         * @return (0|1)
         */
        public function saveCommentAction()
        {
            if (($data = \System\Helper::getJSONParameters()) !== false && $_SESSION['order_payment_status'] == 0) {
                $product = new \Models\Product();
                echo (int)$product->saveCartItemComment($data['cart_item_id'], $data['comment']); // ok
            } else {
                echo 0; // error
            }
        }

        /**
         *
         * @return (JSON array) new core object [errorcode]
         */
        public function payOrderAction()
        {
            if (($data = \System\Helper::getJSONParameters()) !== false &&
                    !empty($_SESSION['cart'])) {

                if ((!empty($data['reg']) &&
                        !empty($data['payment_method']) &&
                        !empty($data['payment_period']) &&
                        !empty($_SESSION['email_validated'])) ||
                        (!empty($_SESSION['user_logged']) &&
                                !empty($data['payment_period']) &&
                                !empty($data['payment_method']))) {

                        $product = new \Models\Product();

                        if (empty($_SESSION['user_logged'])) {
                            if (!empty($data['reg']['status'])) { // Business
                                unset($data['reg']['personal']);

                                $user_data = [
                                    'private'         => 0,
                                    'gender'          => $data['reg']['gender'],
                                    'first_name'      => $data['reg']['first_name'],
                                    'last_name'       => $data['reg']['last_name'],
                                    'country'         => $data['reg']['country']['value'],
                                    'city'            => $data['reg']['city'],
                                    'language'        => $data['reg']['language'],
                                    'zip'             => $data['reg']['post_code'],
                                    'email'           => $_SESSION['email'],
                                    'phone_prefix'    => $data['reg']['phone_prefix']['value'],
                                    'phone'           => $data['reg']['phone'],
                                    'company_name'    => $data['reg']['business']['company_name'],
                                    'position'        => $data['reg']['business']['position'],
                                    'company_address' => $data['reg']['address'],
                                    'vat'             => $data['reg']['business']['vat']
                                ];

                            } else {
                                unset($data['reg']['business']);

                                $user_data = [
                                    'private'         => 1,
                                    'gender'          => $data['reg']['gender'],
                                    'first_name'      => $data['reg']['first_name'],
                                    'last_name'       => $data['reg']['last_name'],
                                    'country'         => $data['reg']['country']['value'],
                                    'city'            => $data['reg']['city'],
                                    'language'        => $data['reg']['language'],
                                    'zip'             => $data['reg']['post_code'],
                                    'email'           => $_SESSION['email'],
                                    'phone_prefix'    => $data['reg']['phone_prefix']['value'],
                                    'phone'           => $data['reg']['phone'],
                                    'address'         => $data['reg']['address'],
                                ];
                            }

                            $_SESSION['user_validated_profile'] = $user_data;
                            $_SESSION['user_validated'] = 1;
                        }

                    if (in_array($data['payment_method'], ['mastercard', 'visa', 'payza', 'webmoney', 'paypal']) &&
                            in_array($data['payment_period'], [1, 3, 6, 12])) {

                        if ($data['payment_method'] == 'visa' || $data['payment_method'] == 'mastercard') {
                            $params_out['cart']['payment_method'] = 'credit-card';
                        } else {
                            $params_out['cart']['payment_method'] = $data['payment_method'];
                        }

                        $cart_items = $product->getCartItemsForOrder();

                        if (empty($cart_items)) {
                            echo json_encode(['errorcode' => 1, 'error' => 'Empty cart']); // error in configuration
                            return;
                        }

                        $shop_period_discounts = unserialize(SHOP_PERIOD_DISCOUNTS);

                        foreach ($cart_items as &$item) {
                            if ($item['minimal_period'] > $data['payment_period']) {
                                echo json_encode(['errorcode' => 1, 'error' => 'Error in period']); // error in period
                                return;
                            }

                            // Discounts based on period
                            if (empty($item['discount'])) {
                                if ($data['payment_period'] > 1) {
                                    $item['discount'] = $shop_period_discounts[(int)$data['payment_period']];
                                }
                            }

                            if (!empty($item['options'])) {
                                foreach ($item['options'] as &$op) {
                                    if (empty($op['discount'])) {
                                        if ($data['payment_period'] > 1) {
                                            if (empty($op['id_product'])) {
                                                $id_product = $op;
                                                $op = [
                                                    'id_product' => $id_product,
                                                    'discount' => $shop_period_discounts[(int)$data['payment_period']],
                                                    'quantity' => 1
                                                ];
                                            } else {
                                                $op['discount'] = $shop_period_discounts[(int)$data['payment_period']];
                                            }
                                        }
                                    }
                                }
                            }
                        }

                        if (!empty($_SESSION['promocode'])) {
                            $params_out['cart']['promocode'] = $_SESSION['promocode']['id'];
                        }

                        $params_out['cart']['items'] = $cart_items;
                        $params_out['cart']['payment_period'] = $data['payment_period'];
                        $params_out['cart']['http_user_agent'] = $_SERVER["HTTP_USER_AGENT"];
                        $params_out['cart']['remote_addr'] = $_SERVER["REMOTE_ADDR"];
                        $params_out['cart']['site'] = 'company.com';

                        if (!empty($_SESSION['user_logged'])) {
                            $params_out['user_id'] = $_SESSION['user_logged_profile']['id'];
                        } elseif (!empty($_SESSION['user_validated'])) {
                            $params_out['user_id'] = 0;
                            $params_out['user_data'] = $_SESSION['user_validated_profile'];
                        }

                        $result = $this->_api->order('generateLinkToPay', $params_out);

                        if (!empty($result) && $result['errorcode'] == 0) {
                            // set up session
                            $_SESSION['order_pay_hash'] = $result['result']['hash'];
                            $_SESSION['order_payment_status'] = 0; // we just get the link
                            $_SESSION['order_pay_expire'] = time() + 120; // set 2min expire

                            // we do not clean cart before order has been paid actually
                            // we give client another attempt
                            // $_SESSION['cart'] = [];

                            // we assume we used promocode
                            \Models\User::clearPromoCode();

                            echo json_encode($result); // ok
                            return;
                        }
                    }
                }
            }
            echo json_encode(['errorcode' => 1, 'error' => 'Error in parameters']); // error
        }

        /**
         * Submit the order. i.e. w/o payment (with promocode)
         *
         * @return (JSON array) new core object [errorcode]
         */
        public function submitOrderAction()
        {
            if (isset($_POST['data']) && !empty($_POST['data']) &&
                    !empty($_SESSION['promocode']) && $_SESSION['promocode']['type'] == 3) {

                $product = new \Models\Product();
                //$user = new \Models\User();
                $params = json_decode($_POST['data'], true);

                if (in_array($params['payment_method'], ['credit-card', 'payza', 'webmoney', 'paypal']) &&
                        in_array($params['payment_period'], [1, 3, 6, 12])) {

                    $cart_items = $product->getCartItemsForOrder();
                    if (empty($cart_items)) {
                        echo json_encode(['errorcode' => 1, 'error' => 'Empty cart']); // error in configuration
                        return;
                    }
                    foreach ($cart_items as $item) {
                        if ($item['minimal_period'] > $params['payment_period']) {
                            echo json_encode(['errorcode' => 1, 'error' => 'Error in period']); // error in period
                            return;
                        }
                    }

                    $params_out['cart']['promocode'] = $_SESSION['promocode']['id'];
                    $params_out['cart']['items'] = $cart_items;
                    $params_out['cart']['payment_period'] = $params['payment_period'];
                    $params_out['cart']['payment_method'] = $params['payment_method'];
                    $params_out['cart']['http_user_agent'] = $_SERVER["HTTP_USER_AGENT"];
                    $params_out['cart']['remote_addr'] = $_SERVER["REMOTE_ADDR"];
                    $params_out['cart']['site'] = 'server.lu';

                    if (!empty($_SESSION['user_logged'])) {
                        $params_out['user_id'] = $_SESSION['user_logged_profile']['id'];

                    } else { // we do not allow anonymous to order without pay
                        echo json_encode(['errorcode' => 1, 'error' => 'Error in parameters']); // error
                        return;
                    }
                    $result = $this->_api->order('orderWithoutPay', $params_out);

                    if (!empty($result) && $result['errorcode'] == 0) {

                        \Models\User::clearPaymentSession(true);
                        \Models\User::clearPromoCode();
                        //$user->changeUserNotificationData();

                        $_SESSION['order_payment_finalize'] = 1;

                        echo json_encode($result); // ok
                        return;
                    }
                }
            }
            echo json_encode(['errorcode' => 1, 'error' => 'Error in parameters']); // error
        }

        /**
         *
         * @return (JSON array) new core object [errorcode]
         */
        public function checkPaymentCompleteAction()
        {
            if ($_SESSION['order_payment_status'] > 0) {

                $params_out['order_pay_hash'] = $_SESSION['order_pay_hash'];
                $result = $this->_api->order('getPaymentStatus', $params_out);

                if (!empty($result) && $result['errorcode'] == 0) {
                    if ($result['result'] > 1) {

                        $this->checkCompletePaymentAndNewUserLogin();
                        // we get successful payment clear cart data
                        // set up session
                        \Models\User::clearPaymentSession();
                        // finalize payment
                        $_SESSION['order_payment_finalize'] = 1;
                    } else {
                        if ($_SESSION['order_pay_expire'] < time()) { // expired so reset payment session
                            \Models\User::clearPaymentSession();
                            $result = ['errorcode' => 1, 'error' => 'Time expired'];
                        }
                    }
                    echo json_encode($result); // ok
                    return;
                }
            }
            echo json_encode(['errorcode' => 1, 'error' => 'Not in payment session']); // error
        }

        /**
         *
         * @return NONE
         */
        public function checkCompletePaymentAndNewUserLogin()
        {
            $params_out['order_pay_hash'] = $_SESSION['order_pay_hash'];
            $params_out['ip'] = $_SERVER['REMOTE_ADDR'];
            $params_out['agent'] = $_SERVER['HTTP_USER_AGENT'];
            $params_out['site'] = 'company.com';
            $pc = $this->_api->order('checkPaymentComplete', $params_out);

            if (!empty($pc) && $pc['errorcode'] == 0) {

                if (!empty($pc['result']['complete']) &&
                        !empty($_SESSION['user_logged']) &&
                        !empty($pc['result']['user_id']) &&
                        $pc['result']['user_id'] == $_SESSION['user_logged_profile']['id']) {
                    // we assume logged user do not need login
                    // ok
                } elseif (!empty($pc['result']['complete']) &&
                        empty($_SESSION['user_logged']) &&
                        !empty($pc['result']['new_user_login'])) {
                    // we assume new user & log in him
                    // set up session
                    \Models\User::clearValidatedProfile();
                    \Models\User::clearSocialProfile();
                    \Models\User::clearPromoCode();
                    $_SESSION['user_logged'] = true;
                    $_SESSION['user_logged_profile'] = $pc['result']['new_user_login']['data'];
                    \Models\User::setUserInfo();

                } else {
                    // something wrong
                    // not ok
                }
            }
        }

        /**
         *
         * @return (0|1)
         */
        public function loginUserAction()
        {
            if (($data = \System\Helper::getJSONParameters()) !== false) {
                if (!empty($data['login']) && !empty($data['password'])) {
                    $params = [
                        'login' => $data['login'],
                        'password' => $data['password']
                    ];

                    if (empty($_SESSION['user_logged']) &&
                            $params['login'] != '' && $params['password'] != '') {

                        $params['ip'] = $_SERVER['REMOTE_ADDR'];
                        $params['agent'] = $_SERVER['HTTP_USER_AGENT'];
                        $params['site'] = 'company.com';
                        $result = $this->_api->user('loginUser', $params);

                        if (!empty($result) && $result['errorcode'] == 0) {
                            if (!empty($result['result']['data']) && !isset($result['result']['data']['auth_error'])) {

                                // set up session
                                \Models\User::clearValidatedProfile();
                                \Models\User::clearSocialProfile();
                                $_SESSION['user_logged'] = true;
                                $_SESSION['user_logged_profile'] = $result['result']['data'];
                                \Models\User::setUserInfo();

                                if (!empty($_SESSION['cart'])) {
                                    echo json_encode(['redirect' => '/order/']);
                                } else {
                                    echo json_encode(['redirect' => '/dashboard/overview/']);
                                }

                                return;
                            }
                        }
                    }
                }
            }
            echo 0; // error
        }

        /**
         *
         * @return (0|1)
         */
        public function logoutUserAction()
        {
            if (isset($_POST['data']) && !empty($_POST['data'])) {
                $params = json_decode($_POST['data'], true);

                if (!empty($_SESSION['user_logged']) &&
                        !empty($params['logout'])) {

                    $params['hash'] = $_SESSION['user_logged_profile']['hash'];
                    $result = $this->_api->user('logoutUser', $params);

                    if (!empty($result) && $result['errorcode'] == 0 &&
                            empty($result['result']['data']['status']) &&
                            $_SESSION['user_logged_profile']['hash'] == $result['result']['data']['hash']) {

                        // set up session
                        \Models\User::clearLoggedProfile();
                        \Models\User::clearPaymentSession();

                        // TODO: think about this
                        if (SetCookie('login', '', time('now') - 60, "/")) {
                            echo 1; // ok
                            return;
                        }
                    }
                }
            }
            echo 0; // error
        }

        /**
         *
         * @return (0|1)
         */
        public function validateEmailAction()
        {
            if (($data = \System\Helper::getJSONParameters()) !== false) {
                if (isset($data['email']) && !empty($data['email'])) {
                    $result = $this->_api->user('checkEmail', ['email' => $data['email']]);

                    if (!empty($result) && $result['errorcode'] == 0) { // if no error while check
                        if ($result['result'] > 0) {
                            echo 0; // error if email exists
                            return;
                        } else {
                            $user = new \Models\User();
                            // set up session
                            $_SESSION['email'] = $data['email'];
                            $_SESSION['validation_code'] = $user->sendValidationEmail($data['email']);
                            $_SESSION['email_validated'] = false;

                            echo 1; // ok
                            return;
                        }
                    }
                }
            }
            echo 0; // error
        }

        /**
         *
         * @return (0|1)
         */
        public function validateCodeAction()
        {
            if (($data = \System\Helper::getJSONParameters()) !== false) {
                if (isset($data['validation_code']) && !empty($data['validation_code'])) {
                    if ($_SESSION['validation_code'] == $data['validation_code']) { // if codes are identical
                        // set up session
                        $_SESSION['email_validated'] = true;

                        echo 1; // ok
                        return;
                    }
                }
            }
            echo 0; // error
        }

        public function getServersAction()
        {
            $product = new \Models\Product();
            $page = new \Models\Page();
            $servers = [];

            $categories = $product->getServerCategories();
            $groups = $product->getServerGroups();

            if (!empty($categories)) {
                foreach ($categories as $key => $category) {
                    $servers_tmp = $product->getServersForServersPageByCategory($category['id'], $category['slug']);

                    if (!empty($servers_tmp)) {
                        $servers[$category['slug']] = $servers_tmp;
                        $description = $page->getPageBySlug($category['slug'] . '_description');

                        if (!empty($description)) {
                            $categories[$key]['description'] = $description;
                        }
                    }
                }

                if (!empty($servers)) {
                    echo json_encode([
                        'servers' => $servers,
                        'categories' => $categories,
                        'groups' => $groups
                            ]);
                    return;
                }
            }

            echo 0;
        }

        public function getServerDataAction()
        {
            if (($data = \System\Helper::getJSONParameters()) !== false) {
                $product = new \Models\Product();

                if (!empty($data['edit']) && isset($data['cart_item_id']) && array_key_exists($data['cart_item_id'], $_SESSION['cart']) && !empty($data['server_url'])) {
                    $server_data = $product->getCartItemForConfigurator($data['cart_item_id']);

                    echo json_encode([
                        'server_data' => $product->getServerForConfigurator($data['server_url']),
                        'selected_products' => $server_data['products'],
                        'min_period' => (!empty($_SESSION['min_period']) ? $_SESSION['min_period'] : $server_data['products_minimal_period']),
                        'quantity' => $server_data['quantity'],
                        'location' => $server_data['server_location'],
                        'comment' => $server_data['comment'],
                        ]);

                } elseif (empty($data['edit']) && !empty($data['server_url'])) {

                    echo json_encode([
                        'server_data' => $product->getServerForConfigurator($data['server_url']),
                        ]);

                } else {
                    echo 0;
                }
            }
        }

        public function checkUserTaxStatusAction()
        {
            if (($data = \System\Helper::getJSONParameters()) !== false) {
                if (!empty($data['reg'])) {
                    if (!empty($data['reg']['status'])) {
                        $user_data = [
                            'private'         => 0,
                            'country'         => $data['reg']['country']['value'],
                            'vat'             => $data['reg']['business']['vat']
                        ];
                    } else {
                        $user_data = [
                            'private'         => 1,
                            'country'         => $data['reg']['country']['value'],
                        ];
                    }

                    if (empty($_SESSION['user_logged'])) {
                        $params_out = [
                            'logged_in' => 0,
                            'private' => $user_data['private'],
                            'vat' => (empty($user_data['vat']) ? '' : $user_data['vat']),
                            'country' => $user_data['country']
                        ];
                    }

                } else {
                    if ($_SESSION['user_logged'] && !empty($data['user_id']) && $data['user_id'] == $_SESSION['user_logged_profile']['id']) {
                        $params_out = [
                            'logged_in' => 1,
                            'user_id' => $_SESSION['user_logged_profile']['id']
                        ];
                    }
                }

                if (!empty($params_out)) {
                    $result = $this->_api->user('checkUserTaxStatus', $params_out);

                    if (!empty($result) && $result['errorcode'] == 0) {
                        echo json_encode($result);

                        return;
                    }
                }
            }
            echo 0;
        }

        /**
         *
         * @return (0|1)
         */
        public function submitSubscribeAction()
        {
            if (empty($_SESSION['news_subscribe_email']) && ($data = \System\Helper::getJSONParameters()) !== false) {
                if (isset($data['email']) && !empty($data['email'])) {
                    $params_out['email'] = $data['email'];
                    $result = $this->_api->user('submitNewsSubscribe', $params_out);

                    if (!empty($result) && $result['errorcode'] == 0) {
                        if (!empty($result['result'])) {
                            $_SESSION['news_subscribe_email'] = $data['email'];
                            echo 1; // ok

                            return;
                        }
                    }
                }
            }
            echo 0; // error
        }

        /**
         *
         * @return (0|1)
         */
        public function forgotPasswordAction()
        {
            if (($data = \System\Helper::getJSONParameters()) !== false) {
                if (isset($data['email']) && !empty($data['email'])) {
                    $params_out['http_user_agent'] = $_SERVER["HTTP_USER_AGENT"];
                    $params_out['remote_addr'] = $_SERVER["REMOTE_ADDR"];
                    $params_out['email'] = $data['email'];

                    $result = $this->_api->user('forgotPassword', $params_out);

                    if (!empty($result) && $result['errorcode'] == 0) {
                        if (!empty($result['result'])) {
                            echo 1; // ok

                            return;
                        }
                    }
                }
            }
            echo 0; // error
        }

        //Ticket System
        public function submitTicketAction()
        {
            if (!empty($_SESSION['user_logged'])) {
                if (($data = \System\Helper::getJSONParameters()) !== false) {
                    $data['user_id'] = (int)$_SESSION['user_logged_profile']['id'];
                    $result = $this->_api->ticket('createNewTicket', ['data' => $data]);

                    if ($result) {
                        echo $result;
                    } else {
                        echo 1;
                    }
                }
            }
        }

        public function closeTicketAction()
        {
            if (!empty($_SESSION['user_logged'])) {
                if (($data = \System\Helper::getJSONParameters()) !== false) {
                    $result = $this->_api->ticket('closeTicket', ['ticket_id' => $data['ticket_id']]);

                    if ($result) {
                        echo $result;
                    } else {
                        echo 1;
                    }
                }
            }
        }

        public function answerTicketAction()
        {
            if (!empty($_SESSION['user_logged'])) {
                if (($data = \System\Helper::getJSONParameters()) !== false) {
                    $result = $this->_api->ticket('addTicketResponse', ['data' => $data]);

                    if ($result) {
                        echo $result;
                    } else {
                        echo 1;
                    }
                }
            }
        }

        public function uploadFileAction()
        {

        }

        public function reopenTicketAction()
        {
            if (!empty($_SESSION['user_logged'])) {
                if (($data = \System\Helper::getJSONParameters()) !== false) {
                    $result = $this->_api->ticket('reopenTicket', ['ticket_id' => $data['ticket_id']]);

                    if ($result) {
                        echo $result;
                    } else {
                        echo 1;
                    }
                }
            }
        }

        public function deleteTicketAction()
        {
            if (!empty($_SESSION['user_logged'])) {
                if (($data = \System\Helper::getJSONParameters()) !== false) {
                    $result = $this->_api->ticket('deleteTicket', ['ticket_id' => $data['ticket_id']]);

                    if ($result) {
                        echo $result;
                    } else {
                        echo 1;
                    }
                }
            }
        }

        public function getFileAction()
        {

        }

        public function submitQuestionAction()
        {
            require_once (LIBS_DIR . 'securimage/securimage.php');

            if (($data = \System\Helper::getJSONParameters()) !== false && !empty($data)) {
                $img = new \Securimage();

                if ($img->check($data['check_img'])) {
                    // TODO: save question to the database
                    echo 1;
                } else {
                    echo 2;
                }

            } else {
                echo 0;
            }
        }
    }
}
