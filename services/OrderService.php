<?php

/**
 * Order placement + lifecycle. Uses invoice_lines for items, auto-assigns first available driver.
 */
class OrderService
{
    private OrderRepository $repo;
    private CustomerRepository $customerRepo;
    private ComboRepository $comboRepo;
    private DeliveryDriverRepository $driverRepo;

    public function __construct()
    {
        $this->repo         = new OrderRepository();
        $this->customerRepo = new CustomerRepository();
        $this->comboRepo    = new ComboRepository();
        $this->driverRepo   = new DeliveryDriverRepository();
    }

    public function getAll(): array
    {
        return $this->repo->findAll();
    }

    public function getById(int $id): ?array
    {
        $order = $this->repo->findById($id);
        if (!$order) {
            return null;
        }
        $order['items'] = $this->repo->findInvoiceLines($id);
        return $order;
    }

    public function getItems(int $orderId): array
    {
        return $this->repo->findInvoiceLines($orderId);
    }

    public function search(string $term): array
    {
        $term = trim($term);
        return $term === '' ? $this->getAll() : $this->repo->search($term);
    }

    public function place(array $data): int|array
    {
        $errors = $this->validate($data);
        if (!empty($errors)) {
            return $errors;
        }

        $driverId = $this->driverRepo->findFirstAvailableDriverId();
        if ($driverId === null) {
            return ['general' => 'No delivery driver is currently available. Please try again later.'];
        }

        $pdo = Database::getConnection();
        $pdo->beginTransaction();
        try {
            $orderId = $this->repo->create((int) $data['customer_id'], $driverId);
            $this->repo->addInvoiceLine((int) $data['combo_id'], $orderId, (int) $data['quantity']);
            $this->driverRepo->updateAvailability($driverId, 'occupied');
            $pdo->commit();
            return $orderId;
        } catch (Throwable $e) {
            $pdo->rollBack();
            return ['general' => 'Unable to place order: ' . $e->getMessage()];
        }
    }

    public function updateStatus(int $id, string $newStatus): array|true
    {
        $order = $this->repo->findById($id);
        if (!$order) {
            return ['errors' => ['general' => 'Order not found.']];
        }
        $current = $order['status'];
        $allowed = Order::transitions()[$current] ?? [];

        if (!in_array($newStatus, Order::statuses(), true)) {
            return ['errors' => ['status' => 'Invalid status value.']];
        }
        if (!in_array($newStatus, $allowed, true)) {
            return ['errors' => ['status' => 'Invalid status transition from "' . Order::displayStatus($current) . '" to "' . Order::displayStatus($newStatus) . '".']];
        }

        $deliveredDate = $newStatus === 'delivered' ? date('Y-m-d') : null;

        $pdo = Database::getConnection();
        $pdo->beginTransaction();
        try {
            $this->repo->updateStatus($id, $newStatus, $deliveredDate);

            if (in_array($newStatus, ['delivered', 'cancelled'], true)) {
                $driverId = (int) $order['driver_id'];
                if (!$this->driverRepo->hasOngoingOrders($driverId)) {
                    $this->driverRepo->updateAvailability($driverId, 'available');
                }
            }
            $pdo->commit();
            return true;
        } catch (Throwable $e) {
            $pdo->rollBack();
            return ['errors' => ['general' => 'Unable to update status: ' . $e->getMessage()]];
        }
    }

    public function delete(int $id): bool
    {
        $pdo = Database::getConnection();
        $pdo->beginTransaction();
        try {
            $items = $this->repo->findInvoiceLines($id);
            foreach ($items as $line) {
                $this->repo->deleteInvoiceLine((int) $line['combo_id'], $id);
            }
            // Remove any complaint linked to the order (foreign key constraint).
            $complaintRepo = new ComplaintRepository();
            if ($complaintRepo->findByOrderId($id)) {
                $complaintRepo->delete($id);
            }
            $this->repo->delete($id);
            $pdo->commit();
            return true;
        } catch (Throwable $e) {
            $pdo->rollBack();
            return false;
        }
    }

    private function validate(array $data): array
    {
        $errors = [];
        $customerId = (int) ($data['customer_id'] ?? 0);
        if ($customerId <= 0) {
            $errors['customer_id'] = 'Please select a customer.';
        } else {
            $customer = $this->customerRepo->findById($customerId);
            if (!$customer) {
                $errors['customer_id'] = 'Selected customer does not exist.';
            } elseif (($customer['status'] ?? '') !== 'active') {
                $errors['customer_id'] = 'This customer is not active.';
            }
        }

        $comboId = (int) ($data['combo_id'] ?? 0);
        if ($comboId <= 0) {
            $errors['combo_id'] = 'Please select a combo.';
        } elseif (!$this->comboRepo->findById($comboId)) {
            $errors['combo_id'] = 'Selected combo does not exist.';
        }

        $qty = trim($data['quantity'] ?? '');
        if ($qty === '' || !preg_match('/^\d+$/', $qty)) {
            $errors['quantity'] = 'Quantity must be a positive integer.';
        } elseif ((int) $qty < 1 || (int) $qty > 99) {
            $errors['quantity'] = 'Quantity must be between 1 and 99.';
        }
        return $errors;
    }
}
