<?php

class InvoiceLine
{
    public int $comboId;
    public int $orderId;
    public int $quantity;
    public float $comboPrice;

    public static function fromArray(array $data): self
    {
        $l = new self();
        $l->comboId    = (int) ($data['combo_id'] ?? 0);
        $l->orderId    = (int) ($data['order_id'] ?? 0);
        $l->quantity   = (int) ($data['quantity'] ?? 0);
        $l->comboPrice = (float) ($data['combo_price'] ?? 0);
        return $l;
    }
}
