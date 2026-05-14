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
        if ($orderId <= 0) {
            redirect('orders');
            return;
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
}
