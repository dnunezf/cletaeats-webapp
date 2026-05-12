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
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 30;
$skip = isset($_GET['skip']) ? (int)$_GET['skip'] : 0;

// Get restaurants formatted as products
$stmt = $pdo->prepare("CALL sp_restaurant_get_all(?, ?)");
$stmt->execute([$limit, $skip]);
$restaurants = $stmt->fetchAll();
$stmt->closeCursor();

// Get total count
$countStmt = $pdo->query("CALL sp_restaurant_count_active()");
$total = $countStmt->fetchColumn();
$countStmt->closeCursor();

$products = [];
foreach ($restaurants as $restaurant) {
    $products[] = [
        'id' => (int)$restaurant['id'],
        'title' => $restaurant['combo_name'],
        'description' => $restaurant['combo_description'] ?? 'Delicious combo from ' . $restaurant['name'],
        'price' => (float)$restaurant['combo_price'],
        'thumbnail' => '',
        'category' => $restaurant['food_type'],
        'brand' => $restaurant['name'],
        'stock' => (int)$restaurant['is_active'] * 100
    ];
}

echo json_encode([
    'products' => $products,
    'total' => (int)$total,
    'skip' => $skip,
    'limit' => $limit
]);
?>
