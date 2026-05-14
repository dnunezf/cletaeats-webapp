<?php

/**
 * Restaurant DTO — user-rooted (users.id == restaurants.user_id).
 */
class Restaurant
{
    public int $userId;
    public string $category;
    public string $username;
    public string $email;
    public string $document;
    public string $status;
    public string $address;
    public string $city;
    public string $postalCode;

    public static function fromArray(array $data): self
    {
        $r = new self();
        $r->userId     = (int) ($data['user_id'] ?? 0);
        $r->category   = $data['category'] ?? 'typical';
        $r->username   = $data['username'] ?? '';
        $r->email      = $data['email'] ?? '';
        $r->document   = $data['document'] ?? '';
        $r->status     = $data['status'] ?? 'inactive';
        $r->address    = $data['address'] ?? '';
        $r->city       = $data['city'] ?? '';
        $r->postalCode = $data['postal_code'] ?? '';
        return $r;
    }

    public static function categories(): array
    {
        return ['typical', 'chinese', 'italian', 'healthy'];
    }
}
