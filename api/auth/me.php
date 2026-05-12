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

// Get token from Authorization header
$headers = getallheaders();
$authHeader = $headers['Authorization'] ?? '';

if (!preg_match('/Bearer\s+(\S+)/', $authHeader, $matches)) {
    http_response_code(401);
    echo json_encode(['message' => 'Unauthorized']);
    exit;
}

$token = $matches[1];
$tokenData = json_decode(base64_decode($token), true);

if (!$tokenData || !isset($tokenData['id']) || !isset($tokenData['exp']) || $tokenData['exp'] < time()) {
    http_response_code(401);
    echo json_encode(['message' => 'Invalid or expired token']);
    exit;
}

$userId = $tokenData['id'];

// Get user from database
$stmt = $pdo->prepare("CALL sp_user_get_by_id(?)");
$stmt->execute([$userId]);
$user = $stmt->fetch();
$stmt->closeCursor();

if (!$user) {
    http_response_code(404);
    echo json_encode(['message' => 'User not found']);
    exit;
}

echo json_encode([
    'id' => (int)$user['id'],
    'username' => $user['username'],
    'email' => $user['email'],
    'firstName' => $user['first_name'] ?? '',
    'lastName' => $user['last_name'] ?? '',
    'image' => $user['image'] ?? '',
    'role' => $user['role']
]);
?>
