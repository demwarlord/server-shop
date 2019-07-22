<?php

/**
* Get unique rows from array
*
* @param array
*/

function smarty_modifier_array_unique($array, $key) {

    $temp_array = array();

    foreach ($array as &$v) {
        if (!isset($temp_array[$v[$key]])) {
            $temp_array[$v[$key]] =& $v;
        }
    }

    return array_values($temp_array);
}

?>