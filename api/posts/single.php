<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/../../helpers/error_handler.php';
require_once __DIR__ . '/../config/env.php';
loadEnv(__DIR__ . '/../.env');
require_once __DIR__ . '/../config/database.php';

// Get post/address ID from URL path
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$segments = explode('/', trim($uri, '/'));
$postId = end($segments);

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Get single address formatted as post
    $stmt = $pdo->prepare("CALL sp_customer_get_by_id(?)");
    $stmt->execute([$postId]);
    $customer = $stmt->fetch();
    $stmt->closeCursor();

    if (!$customer) {
        http_response_code(404);
        echo json_encode(['message' => 'Address not found']);
        exit;
    }

    echo json_encode([
        'id' => (int)$customer['id'],
        'title' => $customer['first_name'] . ' ' . $customer['last_name'],
        'body' => $customer['address'] ?? 'No address provided',
        'userId' => (int)$customer['id'],
        'tags' => array_filter([$customer['city'], $customer['postal_code'], $customer['phone_number']]),
        'isDeleted' => false
    ]);
}

if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    $title = $data['title'] ?? '';
    $body = $data['body'] ?? '';
    $tags = $data['tags'] ?? [];

    // Parse title into first/last name
    $nameParts = explode(' ', $title, 2);
    $firstName = $nameParts[0] ?? '';
    $lastName = $nameParts[1] ?? '';

    // Parse tags for city, postal code
    $city = $tags[0] ?? '';
    $postalCode = $tags[1] ?? '';

    $stmt = $pdo->prepare("CALL sp_customer_update(?, ?, ?, ?, ?, ?)");
    $stmt->execute([$postId, $firstName, $lastName, $body, $city, $postalCode]);
    $stmt->closeCursor();

    echo json_encode([
        'id' => (int)$postId,
        'title' => $title,
        'body' => $body,
        'userId' => (int)$postId,
        'tags' => $tags,
        'isDeleted' => false
    ]);
}

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    // Note: Actually deleting a customer might have foreign key constraints
    // So we'll just mark it or return success
    $stmt = $pdo->prepare("CALL sp_customer_delete(?)");
    
    try {
        $stmt->execute([$postId]);
        $stmt->closeCursor();
        echo json_encode([
            'id' => (int)$postId,
            'title' => '',
            'body' => '',
            'isDeleted' => true,
            'deletedOn' => date('c')
        ]);
    } catch (PDOException $e) {
        http_response_code(400);
        echo json_encode(['message' => 'Cannot delete: ' . $e->getMessage()]);
    }
}
?>
