<?php

/**
 * Customer data transfer object.
 */
class Customer
{
    public int $id;
    public string $firstName;
    public string $lastName;
    public string $email;
    public ?string $phoneNumber;
    public ?string $address;
    public ?string $city;
    public ?string $postalCode;
    public bool $isActive;
    public string $createdAt;
    public string $updatedAt;

    public static function fromArray(array $data): self
    {
        $customer = new self();
        $customer->id          = (int) ($data['id'] ?? 0);
        $customer->firstName   = $data['first_name'] ?? '';
        $customer->lastName    = $data['last_name'] ?? '';
        $customer->email       = $data['email'] ?? '';
        $customer->phoneNumber = $data['phone_number'] ?? null;
        $customer->address     = $data['address'] ?? null;
        $customer->city        = $data['city'] ?? null;
        $customer->postalCode  = $data['postal_code'] ?? null;
        $customer->isActive    = (bool) ($data['is_active'] ?? true);
        $customer->createdAt   = $data['created_at'] ?? '';
        $customer->updatedAt   = $data['updated_at'] ?? '';
        return $customer;
    }

    public function getFullName(): string
    {
        return $this->firstName . ' ' . $this->lastName;
    }
}
