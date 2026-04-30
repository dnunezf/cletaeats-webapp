<?php

/**
 * Order data transfer object.
 */
class Order
{
    public int $id;
    public int $customerId;
    public int $restaurantId;
    public ?int $assignedDriverId;
    public string $comboName;
    public float $comboPrice;
    public int $quantity;
    public float $total;
    public string $status;
    public ?string $notes;
    public string $createdAt;
    public ?string $deliveredAt;
    public ?string $updatedAt;

    public static function fromArray(array $data): self
    {
        $order = new self();
        $order->id               = (int) ($data['id'] ?? 0);
        $order->customerId       = (int) ($data['customer_id'] ?? 0);
        $order->restaurantId     = (int) ($data['restaurant_id'] ?? 0);
        $order->assignedDriverId = isset($data['assigned_driver_id']) ? (int) $data['assigned_driver_id'] : null;
        $order->comboName        = $data['combo_name'] ?? '';
        $order->comboPrice       = (float) ($data['combo_price'] ?? 0);
        $order->quantity         = (int) ($data['quantity'] ?? 1);
        $order->total            = (float) ($data['total'] ?? 0);
        $order->status           = $data['status'] ?? 'preparing';
        $order->notes            = $data['notes'] ?? null;
        $order->createdAt        = $data['created_at'] ?? '';
        $order->deliveredAt      = $data['delivered_at'] ?? null;
        $order->updatedAt        = $data['updated_at'] ?? null;
        return $order;
    }

    public static function statuses(): array
    {
        return ['preparing', 'on_the_way', 'suspended', 'delivered'];
    }

    /**
     * Valid next states for each status.
     */
    public static function transitions(): array
    {
        return [
            'preparing'  => ['on_the_way', 'suspended'],
            'on_the_way' => ['delivered', 'suspended'],
            'suspended'  => ['preparing'],
            'delivered'  => [],
        ];
    }

    public static function displayStatus(string $status): string
    {
        return ucfirst(str_replace('_', ' ', $status));
    }
}
