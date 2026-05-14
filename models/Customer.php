<?php

/**
 * Customer DTO — user-rooted (users.id == customers.user_id).
 */
class Customer
{
    public int $userId;
    public string $cardNumber;
    public string $username;
    public string $email;
    public string $document;
    public string $status;
    public string $address;
    public string $city;
    public string $postalCode;

    public static function fromArray(array $data): self
    {
        $c = new self();
        $c->userId     = (int) ($data['user_id'] ?? 0);
        $c->cardNumber = $data['card_number'] ?? '';
        $c->username   = $data['username'] ?? '';
        $c->email      = $data['email'] ?? '';
        $c->document   = $data['document'] ?? '';
        $c->status     = $data['status'] ?? 'inactive';
        $c->address    = $data['address'] ?? '';
        $c->city       = $data['city'] ?? '';
        $c->postalCode = $data['postal_code'] ?? '';
        return $c;
    }

    public function getFullName(): string
    {
        return $this->username;
    }
}
