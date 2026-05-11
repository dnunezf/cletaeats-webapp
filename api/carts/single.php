<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/../config/env.php';
loadEnv(__DIR__ . '/../.env');
require_once __DIR__ . '/../config/database.php';

// Get cart/order ID from URL path
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$segments = explode('/', trim($uri, '/'));
$cartId = end($segments);

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Get single order formatted as cart
    $stmt = $pdo->prepare("
        SELECT o.id, o.customer_id as userId, o.combo_name as title, o.combo_price as price, 
               o.quantity, o.total, o.status, o.notes, o.created_at,
               r.name as restaurant_name, r.combo_description
        FROM orders o
        JOIN restaurants r ON o.restaurant_id = r.id
        WHERE o.id = ?
    ");
    $stmt->execute([$cartId]);
    $order = $stmt->fetch();

    if (!$order) {
        http_response_code(404);
        echo json_encode(['message' => 'Cart not found']);
        exit;
    }

    $cart = [
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

    echo json_encode($cart);
}

if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    // Get the first product from the cart update
    $product = $data['products'][0] ?? null;
    if (!$product) {
        http_response_code(400);
        echo json_encode(['message' => 'No products provided']);
        exit;
    }

    // Update order
    $quantity = $product['quantity'];
    $total = $product['quantity'] * $product['price'];

    $stmt = $pdo->prepare("
        UPDATE orders 
        SET quantity = ?, total = ?, updated_at = NOW()
        WHERE id = ?
    ");
    $stmt->execute([$quantity, $total, $cartId]);

    // Return updated cart
    $stmt = $pdo->prepare("
        SELECT o.id, o.customer_id as userId, o.combo_name as title, o.combo_price as price, 
               o.quantity, o.total, o.status
        FROM orders o
        WHERE o.id = ?
    ");
    $stmt->execute([$cartId]);
    $order = $stmt->fetch();

    echo json_encode([
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
    ]);
}

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    // Soft delete - mark as deleted or remove
    $stmt = $pdo->prepare("DELETE FROM orders WHERE id = ?");
    $stmt->execute([$cartId]);

    echo json_encode([
        'id' => (int)$cartId,
        'isDeleted' => true,
        'deletedOn' => date('c')
    ]);
}
?>
