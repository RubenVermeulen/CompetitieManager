<?php

return [
    'app' => [
        'url' => 'http://www.competitiemanager.local/',
        'subUrl' => '',
        'hash' => [
            'algo' => PASSWORD_BCRYPT,
            'cost' => 10
        ]
    ],
    'db' => [
        'driver' => 'mysql',
        'host' => '127.0.0.1',
        'name' => 'competitie_manager',
        'username' => '',
        'password' => '',
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
        'prefix' => ''
    ],
    'auth' => [
        'session' => 'user_id',
        'remember' => 'user_r'
    ],
    'mail' => [
        'api_key' => '',
        'domain' => '',
        'from' => '',
    ],
    'twig' => [
        'debug' => true
    ],
    'csrf' => [
        'key' => 'csrf_token'
    ]
];
