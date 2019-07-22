<?php

return [
    'about' => [
            'controller'    =>  'page',
            'action'        =>  'about',
            'params'        =>  []
    ],
    'services' => [
            'controller'    =>  'page',
            'action'        =>  'services',
            'params'        =>  []
    ],
    'technology' => [
            'controller'    =>  'page',
            'action'        =>  'technology',
            'params'        =>  []
    ],
    'products' => [
            'controller'    =>  'page',
            'action'        =>  'products',
            'params'        =>  []
    ],
    'contact' => [
            'controller'    =>  'page',
            'action'        =>  'contact',
            'params'        =>  []
    ],
    'blog/article'       => [
            'controller' =>  'blog',
            'action'     =>  'article',
            'params'     =>  []
    ],
    'blog/page'          => [
            'controller' =>  'blog',
            'action'     =>  'main',
            'params'     =>  []
    ],
    'blog'               => [
            'controller' =>  'blog',
            'action'     =>  'main',
            'params'     =>  []
    ],
    'faq/question'        => [
            'controller' =>  'faq',
            'action'     =>  'article',
            'params'     =>  []
    ],
    'faq/category'       => [
            'controller' =>  'faq',
            'action'     =>  'category',
            'params'     =>  []
    ],
    'faq/page'           => [
            'controller' =>  'faq',
            'action'     =>  'main',
            'params'     =>  []
    ],
    'faq'                => [
            'controller' =>  'faq',
            'action'     =>  'main',
            'params'     =>  []
    ],
    'configure'          => [
            'controller' =>  'order',
            'action'     =>  'configure',
            'params'     =>  [
                'edit'   => false
            ]
    ],
    'edit'          => [
            'controller' =>  'order',
            'action'     =>  'configure',
            'params'     =>  [
                'edit'   => true
            ]
    ],
    'cart'               => [
            'controller' =>  'order',
            'action'     =>  'cart',
            'params'     =>  []
    ],
    'order'              => [
            'controller' =>  'order',
            'action'     =>  'order',
            'params'     =>  []
    ],
    'cancel-payment'     => [
            'controller' =>  'order',
            'action'     =>  'cancelPayment',
            'params'     =>  []
    ],
    'login'              => [
            'controller' =>  'user',
            'action'     =>  'login',
            'params'     =>  []
    ],
    'logout'             => [
            'controller' =>  'user',
            'action'     =>  'logout',
            'params'     =>  [],
            'language_redirect' => false
    ],
    'forgot-password'    => [
            'controller' =>  'user',
            'action'     =>  'forgotpassword',
            'params'     =>  []
    ],
    'clear-cache' => [
        'controller'    => 'index',
        'action'        => 'clearRedisCache',
        'params'        => [],
        'language_redirect' => false
    ],
    'lang'           => [
        'controller' =>  'index',
        'action'     =>  'userLanguage',
        'params'     =>  [],
        'language_redirect' => false
    ],
    'get-captcha'        => [
        'controller' =>  'index',
        'action'     =>  'getCaptcha',
        'params'     =>  [],
        'language_redirect' => false
    ],

];
