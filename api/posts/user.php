<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/../../helpers/error_handler.php';
require_once __DIR__ . '/../config/env.php';
loadEnv(__DIR__ . '/../.env');
require_once __DIR__ . '/../config/database.php';

// Get userId from URL path
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$segments = explode('/', trim($uri, '/'));
$userId = end($segments);

// Get limit parameter
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 50;

// Use customers table as addresses
// Map customer data to post format
$stmt = $pdo->prepare("CALL sp_customer_get_by_id(?)");
$stmt->execute([$userId]);
$customer = $stmt->fetch();
$stmt->closeCursor();

$customers = [];
if ($customer) {
    $customers[] = $customer;
}

$posts = [];
foreach ($customers as $customer) {
    $posts[] = [
        'id' => (int)$customer['id'],
        'title' => $customer['first_name'] . ' ' . $customer['last_name'],
        'body' => $customer['address'] ?? 'No address provided',
        'userId' => (int)$customer['id'],
        'tags' => array_filter([$customer['city'], $customer['postal_code'], $customer['phone_number']]),
        'isDeleted' => false
    ];
}

echo json_encode([
    'posts' => $posts,
    'total' => count($posts),
    'skip' => 0,
    'limit' => $limit
]);
?>
