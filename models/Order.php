<?php

/**
 * Order data transfer object.
 */
class Order
{
    public int $id;
    public int $customerId;
    public int $restaurantId;
    public string $comboName;
    public float $comboPrice;
    public int $quantity;
    public float $total;
    public string $status;
    public ?string $notes;
    public string $createdAt;
    public ?string $updatedAt;

    public static function fromArray(array $data): self
    {
        $order = new self();
        $order->id           = (int) ($data['id'] ?? 0);
        $order->customerId   = (int) ($data['customer_id'] ?? 0);
        $order->restaurantId = (int) ($data['restaurant_id'] ?? 0);
        $order->comboName    = $data['combo_name'] ?? '';
        $order->comboPrice   = (float) ($data['combo_price'] ?? 0);
        $order->quantity     = (int) ($data['quantity'] ?? 1);
        $order->total        = (float) ($data['total'] ?? 0);
        $order->status       = $data['status'] ?? 'pending';
        $order->notes        = $data['notes'] ?? null;
        $order->createdAt    = $data['created_at'] ?? '';
        $order->updatedAt    = $data['updated_at'] ?? null;
        return $order;
    }

    public static function statuses(): array
    {
        return ['pending', 'confirmed', 'delivered', 'cancelled'];
    }
}
