<?php

/**
 * Route definitions.
 * Format: [method => [url => [controller, method, middleware[]]]]
 *
 * Middleware tokens:
 *   auth         - requires authenticated session
 *   guest        - requires NOT authenticated
 *   role:<csv>   - role allowlist (comma-separated), e.g. role:admin,customer
 *   admin        - back-compat alias for role:admin
 *
 * Role enum: customer | driver | restaurant | admin.
 */
return [
    'GET' => [
        'login'              => ['controller' => 'AuthController',           'method' => 'showLogin',         'middleware' => ['guest']],
        'create-account'     => ['controller' => 'AuthController',           'method' => 'showCreateAccount', 'middleware' => ['guest']],
        'register'           => ['controller' => 'AuthController',           'method' => 'showRegister',      'middleware' => ['auth', 'role:admin']],
        'logout'             => ['controller' => 'AuthController',           'method' => 'logout',            'middleware' => ['auth']],
        'dashboard'          => ['controller' => 'DashboardController',      'method' => 'index',             'middleware' => ['auth']],
        'profile'            => ['controller' => 'ProfileController',        'method' => 'show',              'middleware' => ['auth']],
        'customers'          => ['controller' => 'CustomerController',       'method' => 'index',             'middleware' => ['auth', 'role:admin']],
        'customers/create'   => ['controller' => 'CustomerController',       'method' => 'create',            'middleware' => ['auth', 'role:admin']],
        'customers/edit'     => ['controller' => 'CustomerController',       'method' => 'edit',              'middleware' => ['auth', 'role:admin']],
        'restaurants'        => ['controller' => 'RestaurantController',     'method' => 'index',             'middleware' => ['auth', 'role:admin']],
        'restaurants/create' => ['controller' => 'RestaurantController',     'method' => 'create',            'middleware' => ['auth', 'role:admin']],
        'restaurants/edit'   => ['controller' => 'RestaurantController',     'method' => 'edit',              'middleware' => ['auth', 'role:admin']],
        'drivers'            => ['controller' => 'DeliveryDriverController', 'method' => 'index',             'middleware' => ['auth', 'role:admin']],
        'drivers/create'     => ['controller' => 'DeliveryDriverController', 'method' => 'create',            'middleware' => ['auth', 'role:admin']],
        'drivers/edit'       => ['controller' => 'DeliveryDriverController', 'method' => 'edit',              'middleware' => ['auth', 'role:admin']],
        'combos'             => ['controller' => 'ComboController',          'method' => 'index',             'middleware' => ['auth', 'role:admin,restaurant']],
        'combos/create'      => ['controller' => 'ComboController',          'method' => 'create',            'middleware' => ['auth', 'role:admin,restaurant']],
        'combos/edit'        => ['controller' => 'ComboController',          'method' => 'edit',              'middleware' => ['auth', 'role:admin,restaurant']],
        'orders'             => ['controller' => 'OrderController',          'method' => 'index',             'middleware' => ['auth']],
        'orders/browse'      => ['controller' => 'OrderController',          'method' => 'browse',            'middleware' => ['auth', 'role:admin,customer']],
        'orders/create'      => ['controller' => 'OrderController',          'method' => 'create',            'middleware' => ['auth', 'role:admin,customer']],
        'orders/show'        => ['controller' => 'OrderController',          'method' => 'show',              'middleware' => ['auth']],
        'billing/show'       => ['controller' => 'BillingController',        'method' => 'show',              'middleware' => ['auth', 'role:admin,customer']],
        'complaints/create'  => ['controller' => 'ComplaintController',      'method' => 'create',            'middleware' => ['auth', 'role:admin,customer']],
        'reports'            => ['controller' => 'ReportsController',        'method' => 'index',             'middleware' => ['auth', 'role:admin']],
        'users'              => ['controller' => 'UserController',           'method' => 'index',             'middleware' => ['auth', 'role:admin']],
        'users/edit'         => ['controller' => 'UserController',           'method' => 'edit',              'middleware' => ['auth', 'role:admin']],
        'users/pending'      => ['controller' => 'UserController',           'method' => 'pending',           'middleware' => ['auth', 'role:admin']],
    ],
    'POST' => [
        'login'                => ['controller' => 'AuthController',           'method' => 'login',         'middleware' => ['guest']],
        'create-account'       => ['controller' => 'AuthController',           'method' => 'createAccount', 'middleware' => ['guest']],
        'register'             => ['controller' => 'AuthController',           'method' => 'register',      'middleware' => ['auth', 'role:admin']],
        'customers/store'      => ['controller' => 'CustomerController',       'method' => 'store',         'middleware' => ['auth', 'role:admin']],
        'customers/update'     => ['controller' => 'CustomerController',       'method' => 'update',        'middleware' => ['auth', 'role:admin']],
        'customers/delete'     => ['controller' => 'CustomerController',       'method' => 'delete',        'middleware' => ['auth', 'role:admin']],
        'restaurants/store'    => ['controller' => 'RestaurantController',     'method' => 'store',         'middleware' => ['auth', 'role:admin']],
        'restaurants/update'   => ['controller' => 'RestaurantController',     'method' => 'update',        'middleware' => ['auth', 'role:admin']],
        'restaurants/delete'   => ['controller' => 'RestaurantController',     'method' => 'delete',        'middleware' => ['auth', 'role:admin']],
        'drivers/store'        => ['controller' => 'DeliveryDriverController', 'method' => 'store',         'middleware' => ['auth', 'role:admin']],
        'drivers/update'       => ['controller' => 'DeliveryDriverController', 'method' => 'update',        'middleware' => ['auth', 'role:admin']],
        'drivers/delete'       => ['controller' => 'DeliveryDriverController', 'method' => 'delete',        'middleware' => ['auth', 'role:admin']],
        'combos/store'         => ['controller' => 'ComboController',          'method' => 'store',         'middleware' => ['auth', 'role:admin,restaurant']],
        'combos/update'        => ['controller' => 'ComboController',          'method' => 'update',        'middleware' => ['auth', 'role:admin,restaurant']],
        'combos/delete'        => ['controller' => 'ComboController',          'method' => 'delete',        'middleware' => ['auth', 'role:admin,restaurant']],
        'orders/store'         => ['controller' => 'OrderController',          'method' => 'store',         'middleware' => ['auth', 'role:admin,customer']],
        'orders/update-status' => ['controller' => 'OrderController',          'method' => 'updateStatus',  'middleware' => ['auth', 'role:admin,driver']],
        'orders/delete'        => ['controller' => 'OrderController',          'method' => 'delete',        'middleware' => ['auth', 'role:admin']],
        'complaints/store'     => ['controller' => 'ComplaintController',      'method' => 'store',         'middleware' => ['auth', 'role:admin,customer']],
        'complaints/delete'    => ['controller' => 'ComplaintController',      'method' => 'delete',        'middleware' => ['auth', 'role:admin']],
        'users/update'         => ['controller' => 'UserController',           'method' => 'update',        'middleware' => ['auth', 'role:admin']],
        'users/delete'         => ['controller' => 'UserController',           'method' => 'delete',        'middleware' => ['auth', 'role:admin']],
        'users/approve'        => ['controller' => 'UserController',           'method' => 'approve',       'middleware' => ['auth', 'role:admin']],
        'profile/update'       => ['controller' => 'ProfileController',        'method' => 'update',        'middleware' => ['auth']],
    ],
];
