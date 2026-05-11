<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/../config/env.php';
loadEnv(__DIR__ . '/../.env');
require_once __DIR__ . '/../config/database.php';

$json = file_get_contents('php://input');
$data = json_decode($json, true);

$userId = $data['userId'] ?? null;
$products = $data['products'] ?? [];

if (!$userId || empty($products)) {
    http_response_code(400);
    echo json_encode(['message' => 'userId and products are required']);
    exit;
}

// For simplicity, create an order using the first product
// Map the cart product to an order
$product = $products[0];
$productId = $product['id'];
$quantity = $product['quantity'];

// Get restaurant info - using product ID as restaurant ID for mapping
$stmt = $pdo->prepare("SELECT id, name, combo_name, combo_price, combo_description FROM restaurants WHERE id = ?");
$stmt->execute([$productId]);
$restaurant = $stmt->fetch();

if (!$restaurant) {
    // If no restaurant found with that ID, get the first available
    $stmt = $pdo->query("SELECT id, name, combo_name, combo_price, combo_description FROM restaurants LIMIT 1");
    $restaurant = $stmt->fetch();
}

if (!$restaurant) {
    http_response_code(400);
    echo json_encode(['message' => 'No restaurants available']);
    exit;
}

$total = $restaurant['combo_price'] * $quantity;

// Insert order
$stmt = $pdo->prepare("
    INSERT INTO orders (customer_id, restaurant_id, combo_name, combo_price, quantity, total, status)
    VALUES (?, ?, ?, ?, ?, ?, 'preparing')
");
$stmt->execute([
    $userId,
    $restaurant['id'],
    $restaurant['combo_name'],
    $restaurant['combo_price'],
    $quantity,
    $total
]);

$orderId = $pdo->lastInsertId();

echo json_encode([
    'id' => (int)$orderId,
    'products' => [
        [
            'id' => (int)$restaurant['id'],
            'title' => $restaurant['combo_name'],
            'price' => (float)$restaurant['combo_price'],
            'quantity' => $quantity,
            'total' => (float)$total,
            'discountPercentage' => 0.0,
            'discountedPrice' => (float)$total,
            'thumbnail' => ''
        ]
    ],
    'total' => (float)$total,
    'discountedTotal' => (float)$total,
    'userId' => (int)$userId,
    'totalProducts' => 1,
    'totalQuantities' => $quantity,
    'isDeleted' => false
]);
?>
