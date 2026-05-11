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

$query = $_GET['q'] ?? '';

if (empty($query)) {
    echo json_encode([
        'products' => [],
        'total' => 0,
        'skip' => 0,
        'limit' => 30
    ]);
    exit;
}

$searchTerm = '%' . $query . '%';

// Search restaurants by name, combo_name, or food_type
$stmt = $pdo->prepare("
    SELECT id, name, combo_name, combo_description, combo_price, food_type, is_active
    FROM restaurants
    WHERE is_active = 1 
    AND (name LIKE ? OR combo_name LIKE ? OR food_type LIKE ? OR combo_description LIKE ?)
");
$stmt->execute([$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
$restaurants = $stmt->fetchAll();

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
    'total' => count($products),
    'skip' => 0,
    'limit' => 30
]);
?>
