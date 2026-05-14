<?php

/**
 * Delivery driver DTO — user-rooted (users.id == drivers.user_id).
 * Class name kept as DeliveryDriver to minimise touch surface in controllers/services.
 */
class DeliveryDriver
{
    public int $userId;
    public string $status;
    public int $penalties;
    public string $cardNumber;
    public float $kmCostRegular;
    public float $kmCostHolidays;
    public string $username;
    public string $email;
    public string $document;
    public string $userStatus;
    public string $address;
    public string $city;
    public string $postalCode;

    public static function fromArray(array $data): self
    {
        $d = new self();
        $d->userId         = (int) ($data['user_id'] ?? 0);
        $d->status         = $data['status'] ?? 'available';
        $d->penalties      = (int) ($data['penalties'] ?? 0);
        $d->cardNumber     = $data['card_number'] ?? '';
        $d->kmCostRegular  = (float) ($data['km_cost_regular'] ?? 0);
        $d->kmCostHolidays = (float) ($data['km_cost_holidays'] ?? 0);
        $d->username       = $data['username'] ?? '';
        $d->email          = $data['email'] ?? '';
        $d->document       = $data['document'] ?? '';
        $d->userStatus     = $data['user_status'] ?? ($data['status'] ?? '');
        $d->address        = $data['address'] ?? '';
        $d->city           = $data['city'] ?? '';
        $d->postalCode     = $data['postal_code'] ?? '';
        return $d;
    }

    public static function statuses(): array
    {
        return ['available', 'occupied'];
    }
}
