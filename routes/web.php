<?php

/**
 * Route definitions.
 * Format: [method => [url => [controller, method, middleware[]]]]
 */
return [
    'GET' => [
        'login'            => ['controller' => 'AuthController',      'method' => 'showLogin',         'middleware' => ['guest']],
        'create-account'   => ['controller' => 'AuthController',      'method' => 'showCreateAccount', 'middleware' => ['guest']],
        'register'         => ['controller' => 'AuthController',      'method' => 'showRegister',      'middleware' => ['auth', 'admin']],
        'logout'           => ['controller' => 'AuthController',      'method' => 'logout',            'middleware' => ['auth']],
        'dashboard'        => ['controller' => 'DashboardController', 'method' => 'index',             'middleware' => ['auth']],
        'customers'        => ['controller' => 'CustomerController',  'method' => 'index',             'middleware' => ['auth']],
        'customers/create' => ['controller' => 'CustomerController',  'method' => 'create',            'middleware' => ['auth', 'admin']],
        'customers/edit'   => ['controller' => 'CustomerController',  'method' => 'edit',              'middleware' => ['auth']],
        'users/pending'    => ['controller' => 'UserController',      'method' => 'pending',           'middleware' => ['auth', 'admin']],
    ],
    'POST' => [
        'login'            => ['controller' => 'AuthController',      'method' => 'login',         'middleware' => ['guest']],
        'create-account'   => ['controller' => 'AuthController',      'method' => 'createAccount', 'middleware' => ['guest']],
        'register'         => ['controller' => 'AuthController',      'method' => 'register',      'middleware' => ['auth', 'admin']],
        'customers/store'  => ['controller' => 'CustomerController',  'method' => 'store',         'middleware' => ['auth', 'admin']],
        'customers/update' => ['controller' => 'CustomerController',  'method' => 'update',        'middleware' => ['auth']],
        'customers/delete' => ['controller' => 'CustomerController',  'method' => 'delete',        'middleware' => ['auth']],
        'users/approve'    => ['controller' => 'UserController',      'method' => 'approve',       'middleware' => ['auth', 'admin']],
    ],
];
