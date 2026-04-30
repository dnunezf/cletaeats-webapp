<?php

/**
 * Handles restaurant browsing, order placement, and order management.
 */
class OrderController
{
    private OrderService $orderService;
    private RestaurantRepository $restaurantRepo;
    private CustomerRepository $customerRepo;

    public function __construct()
    {
        $this->orderService   = new OrderService();
        $this->restaurantRepo = new RestaurantRepository();
        $this->customerRepo   = new CustomerRepository();
    }

    public function index(): void
    {
        $search = trim($_GET['search'] ?? '');
        $orders = $search !== ''
            ? $this->orderService->search($search)
            : $this->orderService->getAll();

        $isAdmin     = ($_SESSION['role'] ?? '') === 'admin';
        $pageTitle   = 'Orders';
        $currentPage = 'orders';
        view('orders/index', compact('orders', 'isAdmin', 'pageTitle', 'currentPage', 'search'));
    }

    public function browse(): void
    {
        $restaurants = $this->restaurantRepo->findAllActive();

        $pageTitle   = 'Browse Restaurants';
        $currentPage = 'orders';
        view('orders/browse', compact('restaurants', 'pageTitle', 'currentPage'));
    }

    public function create(): void
    {
        $restaurantId = (int) ($_GET['restaurant_id'] ?? 0);
        $restaurant   = $this->restaurantRepo->findById($restaurantId);

        if (!$restaurant) {
            setFlash('error', 'Restaurant not found or is not available.');
            redirect('orders/browse');
            return;
        }

        $customers = $this->customerRepo->findAllActive();

        $pageTitle   = 'Place Order';
        $currentPage = 'orders';
        view('orders/create', compact('restaurant', 'customers', 'pageTitle', 'currentPage'));
    }

    public function store(): void
    {
        csrfCheck();

        $data = [
            'restaurant_id' => trim($_POST['restaurant_id'] ?? ''),
            'customer_id'   => trim($_POST['customer_id'] ?? ''),
            'quantity'      => trim($_POST['quantity'] ?? ''),
            'notes'         => trim($_POST['notes'] ?? ''),
        ];

        $result = $this->orderService->place($data);

        if (is_array($result)) {
            $errors = $result['errors'] ?? $result;
            setFlash('errors', $errors);
            setOldInput($data);
            redirect('orders/create?restaurant_id=' . (int) $data['restaurant_id']);
            return;
        }

        setFlash('success', 'Order placed successfully.');
        redirect('orders/show?id=' . $result);
    }

    public function show(): void
    {
        $id    = (int) ($_GET['id'] ?? 0);
        $order = $this->orderService->getById($id);

        if (!$order) {
            http_response_code(404);
            require BASE_PATH . '/views/errors/404.php';
            exit;
        }

        $isAdmin     = ($_SESSION['role'] ?? '') === 'admin';
        $pageTitle   = 'Order #' . $id;
        $currentPage = 'orders';
        view('orders/show', compact('order', 'isAdmin', 'pageTitle', 'currentPage'));
    }

    public function updateStatus(): void
    {
        csrfCheck();

        $id        = (int) ($_POST['id'] ?? 0);
        $newStatus = trim($_POST['status'] ?? '');

        if ($id <= 0) {
            if (isAjax()) {
                jsonResponse(['success' => false, 'message' => 'Invalid order ID.'], 400);
            }
            redirect('orders');
            return;
        }

        $result = $this->orderService->updateStatus($id, $newStatus);

        if ($result !== true) {
            $msg = array_values($result['errors'])[0] ?? 'An error occurred.';
            if (isAjax()) {
                jsonResponse(['success' => false, 'message' => $msg], 422);
            }
            setFlash('error', $msg);
            redirect('orders/show?id=' . $id);
            return;
        }

        $order = $this->orderService->getById($id);
        $deliveredAt = $order['delivered_at'] ?? null;

        if (isAjax()) {
            jsonResponse([
                'success'      => true,
                'message'      => 'Order status updated.',
                'status'       => $newStatus,
                'delivered_at' => $deliveredAt,
            ]);
            return;
        }

        setFlash('success', 'Order status updated.');
        redirect('orders/show?id=' . $id);
    }

    public function delete(): void
    {
        csrfCheck();

        $id = (int) ($_POST['id'] ?? 0);
        if ($id <= 0) {
            if (isAjax()) {
                jsonResponse(['success' => false, 'message' => 'Invalid order ID.'], 400);
            }
            redirect('orders');
            return;
        }

        $this->orderService->delete($id);

        if (isAjax()) {
            jsonResponse(['success' => true, 'message' => 'Order deleted successfully.']);
            return;
        }

        setFlash('success', 'Order deleted successfully.');
        redirect('orders');
    }
}
