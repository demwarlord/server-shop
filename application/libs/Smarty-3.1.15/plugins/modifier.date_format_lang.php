<?php
/**
 * Smarty plugin
 *
 * @package Smarty
 * @subpackage PluginsModifier
 */

/**
 * Smarty date_format_lang modifier plugin
 *
 * Type:     modifier<br>
 * Name:     date_format<br>
 * Purpose:  format datestamps via strftime<br>
 * Input:<br>
 *          - string: input date string
 *          - lang: language
 *
 * @author Dmitry Kamyshov <dk at root dot lu>
 * @param string $string       input date string
 * @param string $lang         language
 * @return string |void
 */
function smarty_modifier_date_format_lang($string, $lang = 'en', $format = '')
{
    require_once(SMARTY_PLUGINS_DIR . 'shared.make_timestamp.php');
    $timestamp = smarty_make_timestamp($string);
    if ($lang == 'ru') {
        $months = [
           1 => 'Января',
           2 => 'Февраля',
           3 => 'Марта',
           4 => 'Апреля',
           5 => 'Мая',
           6 => 'Июня',
           7 => 'Июля',
           8 => 'Августа',
           9 => 'Сентября',
          10 => 'Октября',
          11 => 'Ноября',
          12 => 'Декабря'
            ];
        if (!empty($format)) {
            $format = str_replace('%B', $months[(int)date('m',$timestamp)], $format);
        } else {
            $format = "%e ". $months[(int)date('m',$timestamp)] . " %Y";
        }
    } else {
        if (empty($format)) {
            $format = "%B %e, %Y";
        }
    }
    return strftime($format, $timestamp);
}
