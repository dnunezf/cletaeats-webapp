<?php

/**
 * Front Controller - Single entry point for all requests.
 */

define('BASE_PATH', dirname(__DIR__));

// Load environment variables
require_once BASE_PATH . '/config/env.php';
loadEnv(BASE_PATH . '/.env');

// Start session
require_once BASE_PATH . '/config/session.php';

// Load app constants
require_once BASE_PATH . '/config/app.php';

// Load helpers (function files)
require_once BASE_PATH . '/helpers/response.php';
require_once BASE_PATH . '/helpers/csrf.php';
require_once BASE_PATH . '/helpers/validation.php';

// Load database
require_once BASE_PATH . '/config/database.php';

// Autoload classes
spl_autoload_register(function (string $class): void {
    $classMap = [
        'User'                => 'models/User.php',
        'Customer'            => 'models/Customer.php',
        'UserRepository'      => 'repositories/UserRepository.php',
        'CustomerRepository'  => 'repositories/CustomerRepository.php',
        'AuthService'         => 'services/AuthService.php',
        'CustomerService'     => 'services/CustomerService.php',
        'UserService'         => 'services/UserService.php',
        'AuthController'      => 'controllers/AuthController.php',
        'CustomerController'  => 'controllers/CustomerController.php',
        'DashboardController' => 'controllers/DashboardController.php',
        'UserController'      => 'controllers/UserController.php',
        'AuthMiddleware'      => 'middleware/AuthMiddleware.php',
        'AdminMiddleware'     => 'middleware/AdminMiddleware.php',
        'GuestMiddleware'     => 'middleware/GuestMiddleware.php',
    ];

    if (isset($classMap[$class])) {
        require_once BASE_PATH . '/' . $classMap[$class];
    }
});

// Parse the URL
$url = isset($_GET['url']) ? trim($_GET['url'], '/') : '';

// Default route
if ($url === '') {
    redirect('login');
}

// Load routes
$routes = require BASE_PATH . '/routes/web.php';
$httpMethod = $_SERVER['REQUEST_METHOD'];

// Match route
if (!isset($routes[$httpMethod][$url])) {
    http_response_code(404);
    require BASE_PATH . '/views/errors/404.php';
    exit;
}

$route = $routes[$httpMethod][$url];

// Run middleware
$middlewareMap = [
    'auth'  => 'AuthMiddleware',
    'admin' => 'AdminMiddleware',
    'guest' => 'GuestMiddleware',
];

foreach ($route['middleware'] as $mw) {
    if (isset($middlewareMap[$mw])) {
        $middlewareClass = $middlewareMap[$mw];
        $middleware = new $middlewareClass();
        $middleware->handle();
    }
}

// Dispatch to controller
$controllerClass = $route['controller'];
$method = $route['method'];

$controller = new $controllerClass();
$controller->$method();
