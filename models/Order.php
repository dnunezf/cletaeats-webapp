<?php

/**
 * Order DTO — new schema. Items live in invoice_lines.
 */
class Order
{
    public int $id;
    public int $customerId;
    public int $driverId;
    public string $status;
    public string $creationDate;
    public ?string $deliveredDate;
    public string $costumerCardNumber;
    public string $driverCardNumber;

    public static function fromArray(array $data): self
    {
        $o = new self();
        $o->id                  = (int) ($data['id'] ?? 0);
        $o->customerId          = (int) ($data['customer_id'] ?? 0);
        $o->driverId            = (int) ($data['driver_id'] ?? 0);
        $o->status              = $data['status'] ?? 'pending';
        $o->creationDate        = $data['creation_date'] ?? '';
        $o->deliveredDate       = $data['delivered_date'] ?? null;
        $o->costumerCardNumber  = $data['costumer_card_number'] ?? '';
        $o->driverCardNumber    = $data['driver_card_number'] ?? '';
        return $o;
    }

    public static function statuses(): array
    {
        return ['pending', 'ongoing', 'delivered', 'cancelled'];
    }

    public static function transitions(): array
    {
        return [
            'pending'   => ['ongoing', 'cancelled'],
            'ongoing'   => ['delivered', 'cancelled'],
            'delivered' => [],
            'cancelled' => [],
        ];
    }

    public static function displayStatus(string $status): string
    {
        return ucfirst($status);
    }
}
