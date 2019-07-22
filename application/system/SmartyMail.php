<?php

/**
 * SmartyMail
 *
 * @author dev. Dmitry Kamyshov <dk@company.com>
 * @package Controller
 * @subpackage System
 * @version 2.0.0
 */

namespace System {

    require_once(LIBS_DIR . 'PHPMailer/PHPMailerAutoload.php');

    class SmartyMail extends \PHPMailer
    {

        public function __construct()
        {
            parent::__construct();

            //default parameters

            /*
            $this->Mailer = 'sendmail';
            */

            $this->IsHTML(true);
            $this->WordWrap = 100;
            $this->CharSet = 'UTF-8';
        }

        /**
         * Main function is for sending mail with a smarty template.
         *
         * @param array $data An array of data for passing to template.
         * @param string $tpl Filename of the smarty template.
         * @param bool $autoinclude Include header and footer automatically.
         * @return bool Returns false if mail was not been sent. True if succesful.
         */
        public function SendSmartyLetter($data, $tpl, $autoinclude = false, $lang = 'en')
        {
            $smarty = new \System\Template();
            $smarty->setCaching(false);

            if (!isset($data['email_subj'])) {
                $data['email_subj'] = $this->Subject;
            }
            $smarty->assign($data);
            $this->Body = '';

            switch ($lang) {
                case 'en':
                    if (file_exists(EMAIL_TEMPLATE_DIR_EN . $tpl)) {
                        $tpl_path = EMAIL_TEMPLATE_DIR_EN;
                    } else {
                        error_log("SmartyMail::SendSmartyLetter: The template is not exists: " . EMAIL_TEMPLATE_DIR_EN . $tpl);
                    }
                    break;
                case 'ru':
                    if (file_exists(EMAIL_TEMPLATE_DIR_RU . $tpl)) {
                        $tpl_path = EMAIL_TEMPLATE_DIR_RU;
                    } else {
                        error_log("SmartyMail::SendSmartyLetter: The template is not exists: " . EMAIL_TEMPLATE_DIR_RU . $tpl);
                    }
                    break;
                default:
                    if (file_exists(EMAIL_TEMPLATE_DIR_EN . $tpl)) {
                        $tpl_path = EMAIL_TEMPLATE_DIR_EN;
                    } else {
                        error_log("SmartyMail::SendSmartyLetter: The template is not exists: " . EMAIL_TEMPLATE_DIR_EN . $tpl);
                    }
                    break;
            }

            if ($autoinclude) {
                $this->AddEmbeddedImage(SITE_EMAIL_IMG_ROOT . "facebook.jpg", "facebook", "facebook.jpg");
                $this->AddEmbeddedImage(SITE_EMAIL_IMG_ROOT . "twitter.jpg", "twitter", "twitter.jpg");
                $this->AddEmbeddedImage(SITE_EMAIL_IMG_ROOT . "linkedin.jpg", "linkedin", "linkedin.jpg");
                $this->AddEmbeddedImage(SITE_EMAIL_IMG_ROOT . "googleplus.jpg", "googleplus", "googleplus.jpg");
                $this->AddEmbeddedImage(SITE_EMAIL_IMG_ROOT . "vkontakte.png", "vkontakte", "vkontakte.png");

                if (file_exists($tpl_path . 'email_header.tpl')) {
                    $this->Body .= $smarty->fetch($tpl_path . 'email_header.tpl');
                } else {
                    error_log("SmartyMail::SendSmartyLetter: The template is not exists: email_header.tpl");
                }
            }

            if (isset($tpl_path) && !empty($tpl_path)) {
                $this->Body .= $smarty->fetch($tpl_path . $tpl);
            }

            if ($autoinclude) {
                if (file_exists($tpl_path . 'email_footer.tpl')) {
                    $this->Body .= $smarty->fetch($tpl_path . 'email_footer.tpl');
                } else {
                    error_log("SmartyMail::SendSmartyLetter: The template is not exists: email_footer.tpl");
                }
            }

            return $this->Send();
        }
    }
}
