<?php

/**
 * Custom global parameters. Theese parameters act as a global switchers.
 * If 'nameOfTheMethod' is omited then the parameter just being removed
 * 
 * Structure:
 * 
 * 'parameter' => [
 *      'action' => 'nameOfTheMethod'
 * ]
 * 
 */

return [
    
    'fb_locale' => [
        'action' => 'setLanguage',
    ],
    
    'custom_param_test' => [
        'action' =>  '',
    ],
    
];
