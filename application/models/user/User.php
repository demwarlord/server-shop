<?php

/**
 * User model
 *
 * @author dev. Dmitry Kamyshov <dk@company.com>
 * @package Model
 * @version 2.0.0
 */

namespace Models {

    class User
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
         * Get user temporary profile by id
         *
         * @param int $provider_id
         * @param int $last_id
         * @return array|bool
         */
        public function getUserSocialProfileById($provider_id, $last_id)
        {
            $db = \Models\Db::getInstance();
            $sql = "SELECT * "
                    . " FROM `user_authentications` "
                    . " WHERE `provider_id` = '" . $db->escapeData($provider_id) . "' "
                    . " AND `user_id` = " . $db->escapeData($last_id);

            return $db->getOneRowAssoc($sql);
        }

        /**
         * TODO add description
         *
         * @param int $provider_id
         * @param int $last_id
         * @return array|bool
         */
        public function getUserSocialProfileForUserProfile($provider_id, $last_id)
        {
            $api = \Models\Api::getInstance();
            $user_social_profile = $this->getUserSocialProfileById($provider_id, $last_id);

            // Try to get country
            if (!empty($user_social_profile['country'])) {
                $result = $api->user('getCountryISOByName', ['country' => $user_social_profile['country']]);
                if ($result['errorcode'] == 0) {
                    $user_social_profile['country'] = $result['result'];
                }
            }

            // Gender
            switch ($user_social_profile['gender']) {
                case 'male':
                    $user_social_profile['gender'] = 1;
                    break;
                case 'female':
                    $user_social_profile['gender'] = 2;
                    break;
                default:
                    $user_social_profile['gender'] = 0;
                    break;
            }

            // City region
            if (empty($user_social_profile['city']) && !empty($user_social_profile['region'])) {
                $user_social_profile['city'] = $user_social_profile['region'];
            }
            return $user_social_profile;
        }

        public static function clearValidatedProfile()
        {
            $_SESSION['user_validated'] = false;
            $_SESSION['email_validated'] = false;
            $_SESSION['email'] = '';
            $_SESSION['validation_code'] = '';
            $_SESSION['user_validated_profile'] = [];
        }

        public static function clearSocialProfile()
        {
            $_SESSION['user_social_profile'] = [];
            $_SESSION['user_social_logged'] = false;
        }

        public static function clearLoggedProfile()
        {
            $_SESSION['user_logged'] = false;
            $_SESSION['user_logged_profile'] = [];
            \Models\User::clearPromoCode();
        }

        public static function clearPaymentSession($cart = true)
        {
            $_SESSION['order_pay_hash'] = '';
            $_SESSION['order_payment_status'] = 0;
            $_SESSION['order_pay_expire'] = 0;
            if ($cart) {
                $_SESSION['cart'] = [];
            }
        }

        static public function clearPromoCode()
        {
            if (isset($_SESSION['promocode'])) {
                unset($_SESSION['promocode']);
            }
        }

        public static function setUserInfo()
        {
            $api = \Models\Api::getInstance();
            $product = new \Models\Product();
            $result = $api->user('getUser', ['user_id' => $_SESSION['user_logged_profile']['id']]);
            if (!empty($result) && $result['errorcode'] == 0) {
                $_SESSION['user_logged_profile']['info'] = $result['result'];
                $result = $api->user('getUserDiscounts', ['user_id' => $_SESSION['user_logged_profile']['id']]);
                if (!empty($result) && $result['errorcode'] == 0) {
                    if (!empty($result['result'])) {
                        foreach ($result['result'] as $discount_item) {
                            $discount_info = $product->getDiscountByCustomId($discount_item['discount_product_id']);
                            if (!empty($discount_info)) {
                                $_SESSION['user_logged_profile']['discount_info'][] = [
                                    'server_product_id' => $discount_item['server_product_id'],
                                    'server_caption' => $product->getServerCaptionByCustomId($discount_item['server_product_id']),
                                    'discount_id' => $discount_info['id'],
                                    'discount_caption' => $discount_info['discount_caption'],
                                    'discount_short_caption' => $discount_info['discount_short_caption'],
                                    'discount_custom_id' => $discount_info['discount_custom_id'],
                                    'discount' => $discount_info['discount'],
                                    'quantity' => $discount_item['quantity']
                                ];
                            }
                        }
                    }
                } else {
                    // TODO: what if error of getting user info, we can just ingnore it?
                }
            } else {
                // TODO: what if error of getting user info, we can just ingnore it?
            }
        }

        public function sendValidationEmail($email)
        {
            $smarty_mail = new \System\SmartyMail();
            $api = \Models\Api::getInstance();
            $validation_code = rand(1000000000, 9999999999);

            $smarty_mail->From = EML_NOREPLY;
            $smarty_mail->FromName = "company.com - Security";
            $smarty_mail->AddAddress($email);

            if ($this->_lang === 'en') {
                $smarty_mail->Subject = "company.com E-mail Validation. Do Not Reply.";
                $sent_status = $smarty_mail->SendSmartyLetter(['validation_code' => $validation_code], 'validation_email.tpl', true, 'en');
            } elseif ($this->_lang === 'ru') {
                $smarty_mail->Subject = "company.com Валидация E-mail.";
                $sent_status = $smarty_mail->SendSmartyLetter(['validation_code' => $validation_code], 'validation_email.tpl', true, 'ru');
            }

            $report = [
                'type'        => 'email',
                'timestamp'   => time(),
                'sent_status' => $sent_status ? 1 : 0,
                'data'        => [
                    'subject'      => $smarty_mail->Subject,
                    'to'           => $email,
                    'from'         => $smarty_mail->From,
                    'content-type' => 'html',
                    'content'      => $smarty_mail->Body
                ]
            ];

            $api->notification('insertNotificationEvent', ['data' => $report]);
            return $validation_code;
        }


    }
}
