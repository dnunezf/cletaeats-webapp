<?php

/**
 * Restaurant data transfer object.
 */
class Restaurant
{
    public int $id;
    public string $name;
    public string $legalId;
    public string $address;
    public string $foodType;
    public string $comboName;
    public ?string $comboDescription;
    public float $comboPrice;
    public bool $isActive;
    public string $createdAt;
    public string $updatedAt;

    public static function fromArray(array $data): self
    {
        $restaurant = new self();
        $restaurant->id               = (int) ($data['id'] ?? 0);
        $restaurant->name             = $data['name'] ?? '';
        $restaurant->legalId          = $data['legal_id'] ?? '';
        $restaurant->address          = $data['address'] ?? '';
        $restaurant->foodType         = $data['food_type'] ?? '';
        $restaurant->comboName        = $data['combo_name'] ?? '';
        $restaurant->comboDescription = $data['combo_description'] ?? null;
        $restaurant->comboPrice       = (float) ($data['combo_price'] ?? 0);
        $restaurant->isActive         = (bool) ($data['is_active'] ?? true);
        $restaurant->createdAt        = $data['created_at'] ?? '';
        $restaurant->updatedAt        = $data['updated_at'] ?? '';
        return $restaurant;
    }

    /**
     * Allowed cuisine categories for the food_type field.
     */
    public static function foodTypes(): array
    {
        return [
            'Italian',
            'Mexican',
            'Chinese',
            'Japanese',
            'Indian',
            'Mediterranean',
            'American',
            'Fast Food',
            'Vegetarian',
            'Other',
        ];
    }
}
