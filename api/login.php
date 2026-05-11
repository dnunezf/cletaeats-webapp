<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/../config/env.php';
loadEnv(__DIR__ . '/../.env');
require_once __DIR__ . '/../config/database.php';

$json = file_get_contents('php://input');
$data = json_decode($json, true);

$inputUser = $data['username'] ?? '';
$inputPass = $data['password'] ?? '';

// Test users
$testUsers = [
    'emilys' => ['username' => 'emilys', 'password' => 'emilyspass', 'id' => 1, 'email' => 'emily.johnson@x.dummyjson.com', 'firstName' => 'Emily', 'lastName' => 'Johnson', 'image' => 'https://dummyjson.com/icon/emilys/128', 'role' => 'user'],
    'atuny0' => ['username' => 'atuny0', 'password' => '9uQFF1Lh', 'id' => 2, 'email' => 'atuny0@sohu.com', 'firstName' => 'Terry', 'lastName' => 'Medhurst', 'image' => 'https://dummyjson.com/icon/atuny0/128', 'role' => 'user'],
    'michaelw' => ['username' => 'michaelw', 'password' => 'michaelwpass', 'id' => 3, 'email' => 'michael.williams@x.dummyjson.com', 'firstName' => 'Michael', 'lastName' => 'Williams', 'image' => 'https://dummyjson.com/icon/michaelw/128', 'role' => 'moderator'],
    'admin' => ['username' => 'admin', 'password' => 'admin', 'id' => 4, 'email' => 'admin@mail.com', 'firstName' => 'Admin', 'lastName' => 'User', 'image' => 'https://dummyjson.com/icon/emilys/128', 'role' => 'admin']
];

// Check test users first
if (isset($testUsers[$inputUser]) && $testUsers[$inputUser]['password'] === $inputPass) {
    $user = $testUsers[$inputUser];
    $token = base64_encode(json_encode(['id' => $user['id'], 'username' => $user['username'], 'exp' => time() + 86400]));
    
    echo json_encode([
        'id' => $user['id'],
        'username' => $user['username'],
        'email' => $user['email'],
        'firstName' => $user['firstName'],
        'lastName' => $user['lastName'],
        'image' => $user['image'],
        'accessToken' => $token,
        'refreshToken' => $token,
        'role' => $user['role']
    ]);
    exit;
}

// Check database using stored procedure
$stmt = $pdo->prepare("CALL sp_user_get_by_username(?)");
$stmt->execute([$inputUser]);
$user = $stmt->fetch();

if ($user && password_verify($inputPass, $user['password'])) {
    $token = base64_encode(json_encode(['id' => $user['id'], 'username' => $user['username'], 'exp' => time() + 86400]));
    
    echo json_encode([
        'id' => (int)$user['id'],
        'username' => $user['username'],
        'email' => $user['email'],
        'firstName' => $user['first_name'],
        'lastName' => $user['last_name'],
        'image' => $user['image'],
        'accessToken' => $token,
        'refreshToken' => $token,
        'role' => $user['role']
    ]);
} else {
    http_response_code(400);
    echo json_encode(['message' => 'Invalid credentials']);
}
?>