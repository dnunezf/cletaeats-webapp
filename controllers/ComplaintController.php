<?php

class ComplaintController
{
    private ComplaintService $complaintService;

    public function __construct()
    {
        $this->complaintService = new ComplaintService();
    }

    public function create(): void
    {
        $orderId = (int) ($_GET['order_id'] ?? 0);
        if ($orderId <= 0 || !$this->canFileComplaint($orderId)) {
            http_response_code(404);
            require BASE_PATH . '/views/errors/404.php';
            exit;
        }
        $pageTitle = 'File a Complaint';
        $currentPage = 'orders';
        view('complaints/create', compact('orderId', 'pageTitle', 'currentPage'));
    }

    public function store(): void
    {
        csrfCheck();
        $orderId = (int) ($_POST['order_id'] ?? 0);
        $content = trim($_POST['content'] ?? '');
        $rating  = (int) ($_POST['rating'] ?? 0);

        if ($orderId <= 0) { redirect('orders'); return; }

        if (!$this->canFileComplaint($orderId)) {
            http_response_code(403);
            require BASE_PATH . '/views/errors/403.php';
            exit;
        }

        $result = $this->complaintService->create($orderId, $content, $rating);
        if (is_array($result)) {
            setFlash('errors', $result);
            setOldInput(['content' => $content, 'rating' => $rating]);
            redirect('complaints/create?order_id=' . $orderId);
            return;
        }
        setFlash('success', 'Complaint filed successfully.');
        redirect('orders/show?id=' . $orderId);
    }

    public function delete(): void
    {
        csrfCheck();
        $orderId = (int) ($_POST['order_id'] ?? 0);
        if ($orderId <= 0) {
            if (isAjax()) { jsonResponse(['success' => false, 'message' => 'Invalid order ID.'], 400); }
            redirect('orders');
            return;
        }
        $this->complaintService->delete($orderId);
        if (isAjax()) { jsonResponse(['success' => true, 'message' => 'Complaint deleted.']); return; }
        setFlash('success', 'Complaint deleted.');
        redirect('orders/show?id=' . $orderId);
    }

    /**
     * Admin can always file/edit a complaint. Customers can only file complaints on
     * their own delivered orders.
     */
    private function canFileComplaint(int $orderId): bool
    {
        $orderRepo = new OrderRepository();
        $order = $orderRepo->findById($orderId);
        if (!$order) {
            return false;
        }
        if (userIsAdmin()) {
            return true;
        }
        if (!userIsCustomer()) {
            return false;
        }
        if (($order['status'] ?? '') !== 'delivered') {
            return false;
        }
        return (int) $order['customer_id'] === (int) (currentUserId() ?? 0);
    }
}
