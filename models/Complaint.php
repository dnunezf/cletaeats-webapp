<?php

class Complaint
{
    public int $orderId;
    public string $content;
    public int $rating;

    public static function fromArray(array $data): self
    {
        $c = new self();
        $c->orderId = (int) ($data['order_id'] ?? 0);
        $c->content = $data['content'] ?? '';
        $c->rating  = (int) ($data['rating'] ?? 0);
        return $c;
    }
}
