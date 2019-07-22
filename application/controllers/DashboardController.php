<?php

/**
 * DashboardController
 *
 * @author dev. Dmitry Kamyshov <dk@company.com>
 * @package Controller
 * @version 2.0.0
 */

namespace Controllers {

    class DashboardController
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
                self::$instance = new DashboardController();
            }
            return self::$instance;
        }

        /**
         * Constructor
         */
        private function __construct()
        {
            if (!empty($_SESSION['user_logged'])) {
                $this->_tpl = \Models\Registry::get('tpl');
                $this->_api = \Models\Api::getInstance();
            } else {
                if (!AJAX) {
                    \System\Helper::redirect(SITE_URL_ROOT);
                }
            }
        }

        /**
        * Main action. Called when another method is not specified.
        */
        public function mainAction()
        {
            throw new \Exception('DashboardController: No main action is set', 404);
        }

        public function overviewAction()
        {
            if (!empty($_SESSION['user_logged'])) {

                $params_out['user_id'] = $_SESSION['user_logged_profile']['id'];
                $result = $this->_api->user('getUserOverview', $params_out);

                if (!empty($result) && $result['errorcode'] == 0) {
                    if (!empty($result['result'])) {
                        $this->_tpl->assign("dash_overview", $result['result']);

                        return;
                    }
                }
            }
            \System\Helper::redirect(SITE_URL_ROOT);
        }

        public function serversAction()
        {
            if (!empty($_SESSION['user_logged'])) {

                $params_out['user_id'] = $_SESSION['user_logged_profile']['id'];
                $result = $this->_api->server('getServersOverview', $params_out);

                if (!empty($result) && $result['errorcode'] == 0) {
                    $_SESSION['dash_servers'] = $result['result'];
                    $this->_tpl->assign("dash_servers", $result['result']);

                    $result = $this->_api->server('getOSList');

                    if (!empty($result) && $result['errorcode'] == 0) {
                        $this->_tpl->assign("os_list", $result['result']);
                    }

                    return;
                }
            }
            \System\Helper::redirect(SITE_URL_ROOT);
        }

        //public function servicesAction()
        //{

        //}

        //public function webspaceAction()
        //{

        //}

        public function billingAction()
        {

        }

        public function settingsAction()
        {
            $params_out['user_id'] = $_SESSION['user_logged_profile']['id'];
            $result = $this->_api->user('getUserInfo', $params_out);

            if (!empty($result) && $result['errorcode'] == 0) {
                if (!empty($result['result'])) {
                    $this->_tpl->assign("dash_settings", $result['result']);

                    return;
                }
            }
        }

        public function securityAction()
        {

        }

        public function supportAction()
        {

        }


        /**
         *
         *
         * AJAX SECTION OF THE DASHBOARD CONTROLLER
         *
         *
         */
        public function ajaxChangeUserInfoAction()
        {
            if (AJAX && (($data = \System\Helper::getJSONParameters()) !== false)) {
                if (isset($data['user_info']) && !empty($data['user_info']) && !empty($_SESSION['user_logged'])) {
                    $params_out['user_id'] = $_SESSION['user_logged_profile']['id'];
                    $params_out['info'] = $data['user_info'];
                    $params_out['http_user_agent'] = $_SERVER["HTTP_USER_AGENT"];
                    $params_out['remote_addr'] = $_SERVER["REMOTE_ADDR"];

                    $result = $this->_api->user('changeUserInfo', $params_out);

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

        public function ajaxGetUnpaidDocumentsAction()
        {
            if (AJAX) {
                if (!empty($_SESSION['user_logged'])) {
                    $params_out['user_id'] = $_SESSION['user_logged_profile']['id'];

                    $result = $this->_api->document('getUserUnpaidDocuments', $params_out);

                    if (!empty($result) && $result['errorcode'] == 0) {
                        if (!empty($result['result'])) {
                            echo json_encode($result['result']); // ok

                            return;
                        }
                    }
                }
            }
            echo 0; // error
        }

        public function ajaxGetBillingHistoryAction()
        {
            if (AJAX && (($data = \System\Helper::getJSONParameters()) !== false)) {
                if (!empty($data['from']) && !empty($data['to']) && !empty($_SESSION['user_logged'])) {
                    $params_out['user_id'] = $_SESSION['user_logged_profile']['id'];
                    $params_out['from'] = strtotime($data['from']);
                    $params_out['to'] = strtotime($data['to']);

                    $result = $this->_api->document('getBillingHistory', $params_out);

                    if (!empty($result) && $result['errorcode'] == 0) {
                        if (!empty($result['result'])) {
                            echo json_encode($result['result']); // ok

                            return;
                        }
                    }
                }
            }
            echo 0; // error
        }

        /**
         * (
         *     [id] => ea779edec36e23e8b63dd2790e46a43fd3bc48fc
         *     [pay302815] => true
         *     [type302815] => Invoice
         *     [pay302816] => true
         *     [type302816] => Invoice
         *     [pay1002728] => true
         *     [type1002728] => Proforma
         *     [pay1003143] => true
         *     [type1003143] => Proforma
         *     [pay7454] => true
         *     [type7454] => Order Confirmation
         *     [dtAction] => paypal
         *     [dtAmount] => 869.00
         *     [dtSurplus] => 0
         *     [payType] => paypal
         *     [dtInformation] =>
         * )
         */
        public function ajaxGetPaymentFormAction()
        {
            if (AJAX && (($data = \System\Helper::getJSONParameters()) !== false)) {
                if (isset($data['payment_data']) && !empty($data['payment_data']) && !empty($_SESSION['user_logged'])) {

                    $result['id'] = sha1($_SESSION['user_logged_profile']['id'] . USER_SALT);
                    $orderid = [];

                    if (!empty($data['payment_data']['selected_documents'])) {
                        foreach ($data['payment_data']['selected_documents'] as $document) {
                            switch ($document['type']) {
                                case 'Invoice':
                                    $result['inputs']['pay'.$document['document_id']] = 'true';
                                    $result['inputs']['type'.$document['document_id']] = 'Invoice';
                                    $orderid[] = 'INV.'.$document['document_id'];
                                    break;
                                case 'Proforma':
                                    $result['inputs']['pay'.$document['document_id']] = 'true';
                                    $result['inputs']['type'.$document['document_id']] = 'Proforma';
                                    $orderid[] = 'PRF.'.$document['document_id'];
                                    break;
                                case 'Order Confirmation':
                                    $result['inputs']['pay'.$document['document_id']] = 'true';
                                    $result['inputs']['type'.$document['document_id']] = 'Order Confirmation';
                                    $orderid[] = 'OC.'.$document['document_id'];
                                    break;

                                default:
                                    break;
                            }
                        }
                    }

                    switch ($data['payment_data']['payment_method']) {
                        case 'visa':
                        case 'mastercard':
                            echo json_encode([
                                'url_string' =>
                                    URL_PAY . "/saferpayinit?amount=" . urlencode(number_format($data['payment_data']['amount'],2,'.',''))
                                    . "&orderid=" . urlencode(implode(',', $orderid)) . "&userid=" . $_SESSION['user_logged_profile']['id']
                                    . "&urlback=" . urlencode(URL_WWW . "/dashboard/billing/")
                                    . "&urlcancel=" . urlencode(URL_WWW . "/dashboard/billing/")
                                    . "&urlsuccess=" . urlencode(URL_WWW . "/dashboard/billing/")
                                ]);

                            return;
                        case 'paypal':
                        case 'alertpay':
                        case 'webmoney':

                            $form = "<form id=\"payform\" target=\"_self\" method=\"POST\" action=\"" . URL_PAY . "/transaction\">";
                            $form .= "<input type=\"hidden\" name=\"id\" value=\"" . $result['id'] . "\" />";
                            $form .= "<input type=\"hidden\" name=\"dtAction\" value=\"pay\" />";
                            $form .= "<input type=\"hidden\" name=\"dtAmount\" value=\"" . number_format($data['payment_data']['amount'],2,'.','') . "\" />";
                            $form .= "<input type=\"hidden\" name=\"payType\" value=\"" . $data['payment_data']['payment_method'] . "\" />";
                            $form .= "<input type=\"hidden\" name=\"urlback\" value=\"" . URL_WWW . "/dashboard/billing/\" />";
                            $form .= "<input type=\"hidden\" name=\"urlcancel\" value=\"" . URL_WWW . "/dashboard/billing/\" />";
                            $form .= "<input type=\"hidden\" name=\"urlsuccess\" value=\"" . URL_WWW . "/dashboard/billing/\" />";

                            if (!empty($result['inputs'])) {
                                foreach ($result['inputs'] as $key => $value) {
                                    $form .= "<input type=\"hidden\" name=\"" . $key . "\" value=\"" . $value . "\" />";
                                }
                            }

                            $form .= "</form>";

                            echo json_encode([
                                'form' => $form
                                ]);

                            return;
                        default:
                            break;
                    }

                }
            }
            echo 0; // error
        }

        public function ajaxSupportAction()
        {
            if (!empty($_SESSION['user_logged'])) {

                $params_out['user_id'] = $_SESSION['user_logged_profile']['id'];
                $result = $this->_api->user('getUserSupport', $params_out);

                if (!empty($result) && $result['errorcode'] == 0) {
                    if (!empty($result['result'])) {
                        echo json_encode($result['result']);
                        return;
                    } else {
                        echo 1;
                        return;
                    }
                } else {
                    echo 1;
                    return;
                }
            }
            \System\Helper::redirect(SITE_URL_ROOT);
        }

        public function ajaxGetServerInfoAction()
        {
            if (AJAX && (($data = \System\Helper::getJSONParameters()) !== false)) {
                if (!empty($data['selected_server']) && !empty($_SESSION['user_logged'])) {
                    $params_out['user_id'] = $_SESSION['user_logged_profile']['id'];
                    $params_out['server_id'] = (int)$data['selected_server'];

                    $result = $this->_api->server('getServerInfo', $params_out);

                    if (!empty($result) && $result['errorcode'] == 0) {
                        if (!empty($result['result'])) {
                            $result_html = '';

                            if (!empty($result['result']['graphs'])) {
                                foreach ($result['result']['graphs'] as $image) {
                                    $result_html .= '<img src="data:image/gif;base64,'.$image.'"/>';
                                }
                            }

                            echo json_encode([
                                'data' => $result['result']['data'],
                                'tickets_count' => $result['result']['tickets_count'],
                                'due' => $result['result']['due'],
                                'overdue' => $result['result']['overdue'],
                                'servers_count' => $result['result']['servers_count'],
                                'graph_html' => $result_html
                                ]);

                            return;

                        }
                    }
                }
            }
            echo 0; // error
        }

        public function ajaxServerReinstallAction()
        {
            if (AJAX && (($data = \System\Helper::getJSONParameters()) !== false)) {
                if (!empty($data['selected_server']) &&
                        !empty($data['root_password']) &&
                        !empty($data['current_password']) &&
                        !empty($data['selected_os']) &&
                        !empty($data['selected_os_arch']) &&
                        !empty($_SESSION['user_logged'])) {

                    $params_out['user_id'] = $_SESSION['user_logged_profile']['id'];
                    $params_out['server_id'] = (int)$data['selected_server'];
                    $params_out['root_password'] = $data['root_password'];
                    $params_out['current_password'] = $data['current_password'];
                    $params_out['selected_os'] = (int)$data['selected_os'];
                    $params_out['selected_os_arch'] = (int)$data['selected_os_arch'];

                    $result = $this->_api->server('reinstallServer', $params_out);

                    if (!empty($result) && $result['errorcode'] == 0) {
                        if (!empty($result['result'])) {
                            echo 1;

                            return;
                        }
                    }
                }
            }
            echo 0; // error
        }

        public function ajaxServerRebootAction()
        {
            if (AJAX && (($data = \System\Helper::getJSONParameters()) !== false)) {
                if (!empty($data['selected_server']) &&
                        !empty($_SESSION['user_logged'])) {

                    $params_out['user_id'] = $_SESSION['user_logged_profile']['id'];
                    $params_out['server_id'] = (int)$data['selected_server'];

                    $result = $this->_api->server('rebootServer', $params_out);

                    if (!empty($result) && $result['errorcode'] == 0) {
                        if (!empty($result['result'])) {
                            echo 1;

                            return;
                        }
                    }
                }
            }
            echo 0; // error
        }

        public function ajaxGetUserLoginsAction()
        {
            if (AJAX && (($data = \System\Helper::getJSONParameters()) !== false)) {
                if (isset($data['page']) && !empty($data['count']) && !empty($_SESSION['user_logged'])) {
                    $params_out['user_id'] = $_SESSION['user_logged_profile']['id'];
                    $params_out['page'] = (int)$data['page'];
                    $params_out['count'] = (int)$data['count'];

                    $result = $this->_api->user('getUserLogins', $params_out);

                    if (!empty($result) && $result['errorcode'] == 0) {
                        echo json_encode($result['result']); // ok

                        return;
                    }
                }
            }
            echo 0; // error
        }
    }
}
