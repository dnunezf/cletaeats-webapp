<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}
require_once __DIR__ . '/../../helpers/error_handler.php';
require_once __DIR__ . '/../../config/env.php';
loadEnv(__DIR__ . '/../../.env');
require_once __DIR__ . '/../../config/database.php';

$json = file_get_contents('php://input');
$data = json_decode($json, true);

// Validate required fields
$required = ['username', 'firstName', 'lastName', 'email', 'password'];
$missing = [];
foreach ($required as $field) {
    if (empty($data[$field])) {
        $missing[] = $field;
    }
}

if (!empty($missing)) {
    http_response_code(400);
    echo json_encode(['message' => 'Missing required fields: ' . implode(', ', $missing)]);
    exit;
}

$username = $data['username'];
$firstName = $data['firstName'];
$lastName = $data['lastName'];
$email = $data['email'];
$password = $data['password'];
$age = $data['age'] ?? 25;
$gender = $data['gender'] ?? 'other';

// Check if username already exists
$stmt = $pdo->prepare("CALL sp_user_check_username(?)");
$stmt->execute([$username]);
if ($stmt->fetch()) {
    $stmt->closeCursor();
    http_response_code(400);
    echo json_encode(['message' => 'Username already exists']);
    exit;
}
$stmt->closeCursor();

// Check if email already exists
$stmt = $pdo->prepare("CALL sp_user_check_email(?)");
$stmt->execute([$email]);
if ($stmt->fetch()) {
    $stmt->closeCursor();
    http_response_code(400);
    echo json_encode(['message' => 'Email already exists']);
    exit;
}
$stmt->closeCursor();

// Hash password
$passwordHash = password_hash($password, PASSWORD_DEFAULT);

// Insert new user
$stmt = $pdo->prepare("CALL sp_user_create(?, ?, ?, 'user', 'pending')");
$stmt->execute([$username, $email, $passwordHash]);

$result = $stmt->fetch();
$userId = $result['user_id'];

echo json_encode([
    'id' => (int)$userId,
    'username' => $username,
    'email' => $email,
    'firstName' => $firstName,
    'lastName' => $lastName,
    'image' => '',
    'role' => 'user'
]);
?>
