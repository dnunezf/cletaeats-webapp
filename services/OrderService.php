<?php

/**
 * Order business logic: placement, status management, and validation.
 */
class OrderService
{
    private OrderRepository $repo;
    private RestaurantRepository $restaurantRepo;

    public function __construct()
    {
        $this->repo           = new OrderRepository();
        $this->restaurantRepo = new RestaurantRepository();
    }

    public function getAll(): array
    {
        return $this->repo->findAll();
    }

    public function getById(int $id): ?array
    {
        return $this->repo->findById($id);
    }

    public function search(string $term): array
    {
        $term = trim($term);
        if ($term === '') {
            return $this->getAll();
        }
        return $this->repo->search($term);
    }

    /**
     * Place a new order. Returns the new order ID on success or error array on failure.
     * Combo snapshot (name + price) is fetched server-side — not trusted from POST.
     * Automatically assigns the first available driver with warning_count < 4.
     */
    public function place(array $data): int|array
    {
        $errors = $this->validate($data);
        if (!empty($errors)) {
            return $errors;
        }

        $driverId = $this->repo->findFirstAvailableDriverId();
        if ($driverId === null) {
            return ['errors' => ['general' => 'No delivery driver is currently available. Please try again later.']];
        }

        $restaurant = $this->restaurantRepo->findById((int) $data['restaurant_id']);
        $data['combo_name']         = $restaurant['combo_name'];
        $data['combo_price']        = (float) $restaurant['combo_price'];
        $data['total']              = $data['combo_price'] * (int) $data['quantity'];
        $data['assigned_driver_id'] = $driverId;
        $data['status']             = 'preparing';

        return $this->repo->create($data);
    }

    /**
     * Update order status, enforcing the valid state machine.
     * Sets delivered_at when transitioning to 'delivered', clears it otherwise.
     */
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

        $deliveredAt = $newStatus === 'delivered' ? date('Y-m-d H:i:s') : null;
        $this->repo->updateStatus($id, $newStatus, $deliveredAt);

        return true;
    }

    public function delete(int $id): bool
    {
        return $this->repo->delete($id);
    }

    private function validate(array $data): array
    {
        $errors = [];

        $restaurantId = (int) ($data['restaurant_id'] ?? 0);
        if ($restaurantId <= 0) {
            $errors['restaurant_id'] = 'Please select a restaurant.';
        } else {
            $restaurant = $this->restaurantRepo->findById($restaurantId);
            if (!$restaurant) {
                $errors['restaurant_id'] = 'Selected restaurant is not available.';
            }
        }

        $customerId = (int) ($data['customer_id'] ?? 0);
        if ($customerId <= 0) {
            $errors['customer_id'] = 'Please select a customer.';
        } else {
            $stmt = Database::getConnection()->prepare(
                'SELECT id, is_active FROM customers WHERE id = ? LIMIT 1'
            );
            $stmt->execute([$customerId]);
            $customer = $stmt->fetch();
            if (!$customer) {
                $errors['customer_id'] = 'Selected customer does not exist.';
            } elseif (!(bool) $customer['is_active']) {
                $errors['customer_id'] = 'This customer is currently suspended and cannot place orders.';
            }
        }

        $quantity = trim($data['quantity'] ?? '');
        if ($quantity === '') {
            $errors['quantity'] = 'Quantity is required.';
        } elseif (!preg_match('/^\d+$/', $quantity)) {
            $errors['quantity'] = 'Quantity must be a positive integer.';
        } else {
            $qty = (int) $quantity;
            if ($qty < 1 || $qty > 99) {
                $errors['quantity'] = 'Quantity must be between 1 and 99.';
            }
        }

        $notes = trim($data['notes'] ?? '');
        if (mb_strlen($notes) > 500) {
            $errors['notes'] = 'Notes must not exceed 500 characters.';
        }

        return $errors;
    }
}
