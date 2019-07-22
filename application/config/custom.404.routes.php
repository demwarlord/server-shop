<?php

/**
 * Custom 404 redirect rules
 *
 * Structure:
 *
 * 'where_we_redirect_url_1' => [
 *      'incorrect_or_obsolete_url_1',
 *      'incorrect_or_obsolete_url_2',
 *      'incorrect_or_obsolete_url_3',
 *      'incorrect_or_obsolete_url_4',
 *      ...
 * ]
 *
 */

return [

    '/' => [
        '/stats',
        '/logs',
        '/login',
        '/register',
        '/login.shtml',
        '/index.shtml',
    ],

];
