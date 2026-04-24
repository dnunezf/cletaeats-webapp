<?php

/**
 * Delivery driver data transfer object.
 */
class DeliveryDriver
{
    public int $id;
    public string $fullName;
    public string $idNumber;
    public string $email;
    public string $address;
    public string $phone;
    public string $cardNumber;
    public string $status;
    public float $orderDistance;
    public float $dailyKilometers;
    public float $weekdayCostPerKm;
    public float $holidayCostPerKm;
    public int $warningCount;
    public ?string $complaints;
    public bool $isActive;
    public string $createdAt;
    public string $updatedAt;

    public static function fromArray(array $data): self
    {
        $driver = new self();
        $driver->id               = (int) ($data['id'] ?? 0);
        $driver->fullName         = $data['full_name'] ?? '';
        $driver->idNumber         = $data['id_number'] ?? '';
        $driver->email            = $data['email'] ?? '';
        $driver->address          = $data['address'] ?? '';
        $driver->phone            = $data['phone'] ?? '';
        $driver->cardNumber       = $data['card_number'] ?? '';
        $driver->status           = $data['status'] ?? 'available';
        $driver->orderDistance    = (float) ($data['order_distance'] ?? 0);
        $driver->dailyKilometers  = (float) ($data['daily_kilometers'] ?? 0);
        $driver->weekdayCostPerKm = (float) ($data['weekday_cost_per_km'] ?? 0);
        $driver->holidayCostPerKm = (float) ($data['holiday_cost_per_km'] ?? 0);
        $driver->warningCount     = (int) ($data['warning_count'] ?? 0);
        $driver->complaints       = $data['complaints'] ?? null;
        $driver->isActive         = (bool) ($data['is_active'] ?? true);
        $driver->createdAt        = $data['created_at'] ?? '';
        $driver->updatedAt        = $data['updated_at'] ?? '';
        return $driver;
    }

    /**
     * Allowed availability statuses.
     */
    public static function statuses(): array
    {
        return ['available', 'busy'];
    }
}
