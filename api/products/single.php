<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/../config/env.php';
loadEnv(__DIR__ . '/../.env');
require_once __DIR__ . '/../config/database.php';

// Get product ID from URL path
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$segments = explode('/', trim($uri, '/'));
$productId = end($segments);

// Get single restaurant formatted as product
$stmt = $pdo->prepare("
    SELECT id, name, combo_name, combo_description, combo_price, food_type, is_active
    FROM restaurants
    WHERE id = ? AND is_active = 1
");
$stmt->execute([$productId]);
$restaurant = $stmt->fetch();

if (!$restaurant) {
    http_response_code(404);
    echo json_encode(['message' => 'Product not found']);
    exit;
}

echo json_encode([
    'id' => (int)$restaurant['id'],
    'title' => $restaurant['combo_name'],
    'description' => $restaurant['combo_description'] ?? 'Delicious combo from ' . $restaurant['name'],
    'price' => (float)$restaurant['combo_price'],
    'thumbnail' => '',
    'category' => $restaurant['food_type'],
    'brand' => $restaurant['name'],
    'stock' => (int)$restaurant['is_active'] * 100
]);
?>
