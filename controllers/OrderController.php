<?php

class OrderController
{
    private OrderService $orderService;
    private RestaurantRepository $restaurantRepo;
    private CustomerRepository $customerRepo;
    private ComboRepository $comboRepo;

    public function __construct()
    {
        $this->orderService   = new OrderService();
        $this->restaurantRepo = new RestaurantRepository();
        $this->customerRepo   = new CustomerRepository();
        $this->comboRepo      = new ComboRepository();
    }

    public function index(): void
    {
        $search = trim($_GET['search'] ?? '');
        $orders = $this->orderService->getAllScoped($search);

        $isAdmin     = userIsAdmin();
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

        $combos    = $this->comboRepo->findAllByRestaurant($restaurantId);
        // Admins may pick the customer; customers always order as themselves.
        $customers = userIsAdmin() ? $this->customerRepo->findAllActive() : [];

        $pageTitle   = 'Place Order';
        $currentPage = 'orders';
        view('orders/create', compact('restaurant', 'combos', 'customers', 'pageTitle', 'currentPage'));
    }

    public function store(): void
    {
        csrfCheck();

        // Customers can only place orders for themselves — ignore posted customer_id.
        $customerId = userIsAdmin()
            ? (int) ($_POST['customer_id'] ?? 0)
            : (int) (currentUserId() ?? 0);

        $data = [
            'restaurant_id' => (int) ($_POST['restaurant_id'] ?? 0),
            'customer_id'   => $customerId,
            'combo_id'      => (int) ($_POST['combo_id'] ?? 0),
            'quantity'      => trim($_POST['quantity'] ?? ''),
        ];

        $result = $this->orderService->place($data);
        if (is_array($result)) {
            setFlash('errors', $result);
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
        if (!$order || !$this->canViewOrder($order)) {
            // 404 instead of 403 to avoid leaking existence of foreign orders.
            http_response_code(404);
            require BASE_PATH . '/views/errors/404.php';
            exit;
        }
        $complaintRepo = new ComplaintRepository();
        $complaint     = $complaintRepo->findByOrderId($id);

        $isAdmin     = userIsAdmin();
        $pageTitle   = 'Order #' . $id;
        $currentPage = 'orders';
        view('orders/show', compact('order', 'complaint', 'isAdmin', 'pageTitle', 'currentPage'));
    }

    public function updateStatus(): void
    {
        csrfCheck();
        $id        = (int) ($_POST['id'] ?? 0);
        $newStatus = trim($_POST['status'] ?? '');

        if ($id <= 0) {
            if (isAjax()) { jsonResponse(['success' => false, 'message' => 'Invalid order ID.'], 400); }
            redirect('orders');
            return;
        }

        // Drivers may only update orders assigned to them. Admins always allowed.
        if (userIsDriver()) {
            $orderRepo = new OrderRepository();
            if (!$orderRepo->isOwnedByDriver($id, (int) currentUserId())) {
                if (isAjax()) { jsonResponse(['success' => false, 'message' => 'Forbidden.'], 403); }
                http_response_code(403);
                require BASE_PATH . '/views/errors/403.php';
                exit;
            }
        }

        $result = $this->orderService->updateStatus($id, $newStatus);
        if ($result !== true) {
            $msg = array_values($result['errors'])[0] ?? 'An error occurred.';
            if (isAjax()) { jsonResponse(['success' => false, 'message' => $msg], 422); }
            setFlash('error', $msg);
            redirect('orders/show?id=' . $id);
            return;
        }

        $order = $this->orderService->getById($id);
        $deliveredAt = $order['delivered_date'] ?? null;

        if (isAjax()) {
            jsonResponse(['success' => true, 'message' => 'Order status updated.', 'status' => $newStatus, 'delivered_at' => $deliveredAt]);
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
            if (isAjax()) { jsonResponse(['success' => false, 'message' => 'Invalid order ID.'], 400); }
            redirect('orders');
            return;
        }
        $this->orderService->delete($id);
        if (isAjax()) { jsonResponse(['success' => true, 'message' => 'Order deleted successfully.']); return; }
        setFlash('success', 'Order deleted successfully.');
        redirect('orders');
    }

    /**
     * Ownership gate for orders/show, by role.
     */
    private function canViewOrder(array $order): bool
    {
        if (userIsAdmin()) {
            return true;
        }
        $uid = (int) (currentUserId() ?? 0);
        if ($uid <= 0) {
            return false;
        }
        if (userIsCustomer()) {
            return (int) $order['customer_id'] === $uid;
        }
        if (userIsDriver()) {
            return (int) $order['driver_id'] === $uid;
        }
        if (userIsRestaurant()) {
            $orderRepo = new OrderRepository();
            return $orderRepo->isVisibleToRestaurant((int) $order['id'], $uid);
        }
        return false;
    }
}
