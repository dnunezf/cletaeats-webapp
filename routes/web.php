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
        'customers'          => ['controller' => 'CustomerController',   'method' => 'index',             'middleware' => ['auth']],
        'customers/create'   => ['controller' => 'CustomerController',   'method' => 'create',            'middleware' => ['auth', 'admin']],
        'customers/edit'     => ['controller' => 'CustomerController',   'method' => 'edit',              'middleware' => ['auth', 'admin']],
        'restaurants'        => ['controller' => 'RestaurantController', 'method' => 'index',             'middleware' => ['auth']],
        'restaurants/create' => ['controller' => 'RestaurantController', 'method' => 'create',            'middleware' => ['auth', 'admin']],
        'restaurants/edit'   => ['controller' => 'RestaurantController', 'method' => 'edit',              'middleware' => ['auth', 'admin']],
        'drivers'            => ['controller' => 'DeliveryDriverController', 'method' => 'index',         'middleware' => ['auth']],
        'drivers/create'     => ['controller' => 'DeliveryDriverController', 'method' => 'create',        'middleware' => ['auth', 'admin']],
        'drivers/edit'       => ['controller' => 'DeliveryDriverController', 'method' => 'edit',          'middleware' => ['auth', 'admin']],
        'orders'             => ['controller' => 'OrderController',      'method' => 'index',             'middleware' => ['auth']],
        'orders/browse'      => ['controller' => 'OrderController',      'method' => 'browse',            'middleware' => ['auth']],
        'orders/create'      => ['controller' => 'OrderController',      'method' => 'create',            'middleware' => ['auth']],
        'orders/show'        => ['controller' => 'OrderController',      'method' => 'show',              'middleware' => ['auth']],
        'users'              => ['controller' => 'UserController',       'method' => 'index',             'middleware' => ['auth', 'admin']],
        'users/edit'         => ['controller' => 'UserController',       'method' => 'edit',              'middleware' => ['auth', 'admin']],
        'users/pending'      => ['controller' => 'UserController',       'method' => 'pending',           'middleware' => ['auth', 'admin']],
    ],
    'POST' => [
        'login'              => ['controller' => 'AuthController',       'method' => 'login',         'middleware' => ['guest']],
        'create-account'     => ['controller' => 'AuthController',       'method' => 'createAccount', 'middleware' => ['guest']],
        'register'           => ['controller' => 'AuthController',       'method' => 'register',      'middleware' => ['auth', 'admin']],
        'customers/store'    => ['controller' => 'CustomerController',   'method' => 'store',         'middleware' => ['auth', 'admin']],
        'customers/update'   => ['controller' => 'CustomerController',   'method' => 'update',        'middleware' => ['auth', 'admin']],
        'customers/delete'   => ['controller' => 'CustomerController',   'method' => 'delete',        'middleware' => ['auth', 'admin']],
        'restaurants/store'  => ['controller' => 'RestaurantController', 'method' => 'store',         'middleware' => ['auth', 'admin']],
        'restaurants/update' => ['controller' => 'RestaurantController', 'method' => 'update',        'middleware' => ['auth', 'admin']],
        'restaurants/delete' => ['controller' => 'RestaurantController', 'method' => 'delete',        'middleware' => ['auth', 'admin']],
        'drivers/store'      => ['controller' => 'DeliveryDriverController', 'method' => 'store',     'middleware' => ['auth', 'admin']],
        'drivers/update'     => ['controller' => 'DeliveryDriverController', 'method' => 'update',    'middleware' => ['auth', 'admin']],
        'drivers/delete'     => ['controller' => 'DeliveryDriverController', 'method' => 'delete',    'middleware' => ['auth', 'admin']],
        'orders/store'       => ['controller' => 'OrderController',      'method' => 'store',         'middleware' => ['auth']],
        'orders/delete'      => ['controller' => 'OrderController',      'method' => 'delete',        'middleware' => ['auth', 'admin']],
        'users/update'       => ['controller' => 'UserController',       'method' => 'update',        'middleware' => ['auth', 'admin']],
        'users/delete'       => ['controller' => 'UserController',       'method' => 'delete',        'middleware' => ['auth', 'admin']],
        'users/approve'      => ['controller' => 'UserController',       'method' => 'approve',       'middleware' => ['auth', 'admin']],
    ],
];
