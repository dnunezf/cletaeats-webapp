<?php

class Combo
{
    public int $id;
    public int $restaurantId;
    public string $name;
    public string $description;
    public float $price;

    public static function fromArray(array $data): self
    {
        $c = new self();
        $c->id           = (int) ($data['id'] ?? 0);
        $c->restaurantId = (int) ($data['restaurant_id'] ?? 0);
        $c->name         = $data['name'] ?? '';
        $c->description  = $data['description'] ?? '';
        $c->price        = (float) ($data['price'] ?? 0);
        return $c;
    }
}
