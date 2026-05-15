<?php

/**
 * Handles invoice display for a given order.
 */
class BillingController
{
    private BillingService $billingService;

    public function __construct()
    {
        $this->billingService = new BillingService();
    }

    public function show(): void
    {
        $id      = (int) ($_GET['id'] ?? 0);
        $invoice = $this->billingService->getInvoiceByOrderId($id);

        if (!$invoice) {
            http_response_code(404);
            require BASE_PATH . '/views/errors/404.php';
            exit;
        }

        // Customers may only see invoices for their own orders. (Admins always.)
        if (userIsCustomer()) {
            $orderCustomerId = (int) ($invoice['order']['customer_id'] ?? 0);
            if ($orderCustomerId !== (int) (currentUserId() ?? 0)) {
                http_response_code(404);
                require BASE_PATH . '/views/errors/404.php';
                exit;
            }
        }

        $pageTitle   = 'Invoice — Order #' . $id;
        $currentPage = 'orders';
        view('billing/show', compact('invoice', 'pageTitle', 'currentPage'));
    }
}
