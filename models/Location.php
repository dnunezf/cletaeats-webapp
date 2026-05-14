<?php

class Location
{
    public int $id;
    public string $address;
    public string $city;
    public string $postalCode;

    public static function fromArray(array $data): self
    {
        $l = new self();
        $l->id         = (int) ($data['id'] ?? 0);
        $l->address    = $data['address'] ?? '';
        $l->city       = $data['city'] ?? '';
        $l->postalCode = $data['postal_code'] ?? '';
        return $l;
    }
}
