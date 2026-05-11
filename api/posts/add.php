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

$title = $data['title'] ?? '';
$body = $data['body'] ?? '';
$userId = $data['userId'] ?? 0;
$tags = $data['tags'] ?? [];

if (empty($title) || empty($body)) {
    http_response_code(400);
    echo json_encode(['message' => 'Title and body are required']);
    exit;
}

// Parse title into first/last name
$nameParts = explode(' ', $title, 2);
$firstName = $nameParts[0] ?? '';
$lastName = $nameParts[1] ?? '';

// Parse tags for city, postal code, phone
$city = $tags[0] ?? '';
$postalCode = $tags[1] ?? '';
$phone = $tags[2] ?? '';

// Generate unique email
$email = strtolower(str_replace(' ', '.', $title)) . '.' . time() . '@example.com';

// Insert as customer
$stmt = $pdo->prepare("
    INSERT INTO customers (first_name, last_name, email, address, city, postal_code, phone_number)
    VALUES (?, ?, ?, ?, ?, ?, ?)
");
$stmt->execute([$firstName, $lastName, $email, $body, $city, $postalCode, $phone]);

$customerId = $pdo->lastInsertId();

echo json_encode([
    'id' => (int)$customerId,
    'title' => $title,
    'body' => $body,
    'userId' => (int)$userId,
    'tags' => $tags,
    'isDeleted' => false
]);
?>
