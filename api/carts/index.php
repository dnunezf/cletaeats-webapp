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

// Get pagination parameters
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 50;
$skip = isset($_GET['skip']) ? (int)$_GET['skip'] : 0;

// Get all orders and format as carts
$stmt = $pdo->prepare("CALL sp_order_get_all(?, ?)");
$stmt->execute([$limit, $skip]);
$orders = $stmt->fetchAll();
$stmt->closeCursor();

// Get total count
$countStmt = $pdo->query("CALL sp_order_count()");
$total = $countStmt->fetchColumn();
$countStmt->closeCursor();

$carts = [];
foreach ($orders as $order) {
    $carts[] = [
        'id' => (int)$order['id'],
        'products' => [
            [
                'id' => (int)$order['id'],
                'title' => $order['title'],
                'price' => (float)$order['price'],
                'quantity' => (int)$order['quantity'],
                'total' => (float)$order['total'],
                'discountPercentage' => 0.0,
                'discountedPrice' => (float)$order['total'],
                'thumbnail' => ''
            ]
        ],
        'total' => (float)$order['total'],
        'discountedTotal' => (float)$order['total'],
        'userId' => (int)$order['userId'],
        'totalProducts' => 1,
        'totalQuantities' => (int)$order['quantity'],
        'isDeleted' => false
    ];
}

echo json_encode([
    'carts' => $carts,
    'total' => (int)$total,
    'skip' => $skip,
    'limit' => $limit
]);
?>
