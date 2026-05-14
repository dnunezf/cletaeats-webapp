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
        'User'                      => 'models/User.php',
        'Customer'                  => 'models/Customer.php',
        'Restaurant'                => 'models/Restaurant.php',
        'DeliveryDriver'            => 'models/DeliveryDriver.php',
        'Location'                  => 'models/Location.php',
        'Combo'                     => 'models/Combo.php',
        'InvoiceLine'               => 'models/InvoiceLine.php',
        'Complaint'                 => 'models/Complaint.php',
        'Order'                     => 'models/Order.php',
        'UserRepository'            => 'repositories/UserRepository.php',
        'CustomerRepository'        => 'repositories/CustomerRepository.php',
        'RestaurantRepository'      => 'repositories/RestaurantRepository.php',
        'DeliveryDriverRepository'  => 'repositories/DeliveryDriverRepository.php',
        'LocationRepository'        => 'repositories/LocationRepository.php',
        'ComboRepository'           => 'repositories/ComboRepository.php',
        'ComplaintRepository'       => 'repositories/ComplaintRepository.php',
        'OrderRepository'           => 'repositories/OrderRepository.php',
        'ReportsRepository'         => 'repositories/ReportsRepository.php',
        'AuthService'               => 'services/AuthService.php',
        'CustomerService'           => 'services/CustomerService.php',
        'RestaurantService'         => 'services/RestaurantService.php',
        'UserService'               => 'services/UserService.php',
        'DeliveryDriverService'     => 'services/DeliveryDriverService.php',
        'ComboService'              => 'services/ComboService.php',
        'ComplaintService'          => 'services/ComplaintService.php',
        'OrderService'              => 'services/OrderService.php',
        'BillingService'            => 'services/BillingService.php',
        'ReportsService'            => 'services/ReportsService.php',
        'AuthController'            => 'controllers/AuthController.php',
        'CustomerController'        => 'controllers/CustomerController.php',
        'RestaurantController'      => 'controllers/RestaurantController.php',
        'DashboardController'       => 'controllers/DashboardController.php',
        'UserController'            => 'controllers/UserController.php',
        'DeliveryDriverController'  => 'controllers/DeliveryDriverController.php',
        'ComboController'           => 'controllers/ComboController.php',
        'ComplaintController'       => 'controllers/ComplaintController.php',
        'OrderController'           => 'controllers/OrderController.php',
        'BillingController'         => 'controllers/BillingController.php',
        'ReportsController'         => 'controllers/ReportsController.php',
        'AuthMiddleware'            => 'middleware/AuthMiddleware.php',
        'AdminMiddleware'           => 'middleware/AdminMiddleware.php',
        'GuestMiddleware'           => 'middleware/GuestMiddleware.php',
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

// Handle API routes
$apiRoutes = [
    // Auth API
    'auth/login' => ['file' => 'api/login.php', 'methods' => ['POST', 'OPTIONS']],
    'auth/me' => ['file' => 'api/auth/me.php', 'methods' => ['GET', 'OPTIONS']],

    // Users API
    'users/add' => ['file' => 'api/users/add.php', 'methods' => ['POST', 'OPTIONS']],

    // Carts/Orders API
    'carts' => ['file' => 'api/carts/index.php', 'methods' => ['GET', 'OPTIONS']],
    'carts/add' => ['file' => 'api/carts/add.php', 'methods' => ['POST', 'OPTIONS']],

    // Posts/Addresses API
    'posts/add' => ['file' => 'api/posts/add.php', 'methods' => ['POST', 'OPTIONS']],

    // Products API
    'products' => ['file' => 'api/products/index.php', 'methods' => ['GET', 'OPTIONS']],
    'products/search' => ['file' => 'api/products/search.php', 'methods' => ['GET', 'OPTIONS']],
];

$httpMethod = $_SERVER['REQUEST_METHOD'];

// Check for exact API match first
if (isset($apiRoutes[$url]) && in_array($httpMethod, $apiRoutes[$url]['methods'])) {
    require BASE_PATH . '/' . $apiRoutes[$url]['file'];
    exit;
}

// Check for dynamic API routes
// Pattern: carts/user/{userId}, carts/{id}, posts/user/{userId}, posts/{id}, products/{id}
$patterns = [
    '#^carts/user/(\d+)$#' => ['file' => 'api/carts/user.php', 'methods' => ['GET', 'OPTIONS']],
    '#^carts/(\d+)$#' => ['file' => 'api/carts/single.php', 'methods' => ['GET', 'PUT', 'DELETE', 'OPTIONS']],
    '#^posts/user/(\d+)$#' => ['file' => 'api/posts/user.php', 'methods' => ['GET', 'OPTIONS']],
    '#^posts/(\d+)$#' => ['file' => 'api/posts/single.php', 'methods' => ['GET', 'PUT', 'DELETE', 'OPTIONS']],
    '#^products/(\d+)$#' => ['file' => 'api/products/single.php', 'methods' => ['GET', 'OPTIONS']],
];

foreach ($patterns as $pattern => $route) {
    if (preg_match($pattern, $url) && in_array($httpMethod, $route['methods'])) {
        require BASE_PATH . '/' . $route['file'];
        exit;
    }
}

// Load web routes
$routes = require BASE_PATH . '/routes/web.php';

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
