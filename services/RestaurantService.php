<?php

/**
 * Restaurant business logic: CRUD orchestration and validation.
 */
class RestaurantService
{
    private RestaurantRepository $repo;

    public function __construct()
    {
        $this->repo = new RestaurantRepository();
    }

    public function getAll(): array
    {
        return $this->repo->findAllActive();
    }

    public function getById(int $id): ?array
    {
        return $this->repo->findById($id);
    }

    public function search(string $term): array
    {
        $term = trim($term);
        if ($term === '') {
            return $this->getAll();
        }
        return $this->repo->search($term);
    }

    /**
     * Create a restaurant. Returns the new ID on success or error array on failure.
     */
    public function create(array $data): int|array
    {
        $errors = $this->validate($data);
        if (!empty($errors)) {
            return $errors;
        }

        if ($this->repo->findByLegalId($data['legal_id'])) {
            return ['legal_id' => 'This legal ID is already registered to another restaurant.'];
        }

        return $this->repo->create($data);
    }

    /**
     * Update a restaurant. Returns true on success or error array on failure.
     */
    public function update(int $id, array $data): bool|array
    {
        $errors = $this->validate($data);
        if (!empty($errors)) {
            return $errors;
        }

        if ($this->repo->findByLegalIdExcluding($data['legal_id'], $id)) {
            return ['legal_id' => 'This legal ID is already registered to another restaurant.'];
        }

        return $this->repo->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->repo->delete($id);
    }

    private function validate(array $data): array
    {
        $validator = new Validator();
        $validator
            ->required($data['name'] ?? '', 'name')
            ->minLength($data['name'] ?? '', 2, 'name')
            ->maxLength($data['name'] ?? '', 100, 'name')
            ->required($data['legal_id'] ?? '', 'legal_id')
            ->minLength($data['legal_id'] ?? '', 5, 'legal_id')
            ->maxLength($data['legal_id'] ?? '', 30, 'legal_id')
            ->required($data['address'] ?? '', 'address')
            ->maxLength($data['address'] ?? '', 255, 'address')
            ->required($data['food_type'] ?? '', 'food_type')
            ->maxLength($data['food_type'] ?? '', 50, 'food_type')
            ->required($data['combo_name'] ?? '', 'combo_name')
            ->minLength($data['combo_name'] ?? '', 2, 'combo_name')
            ->maxLength($data['combo_name'] ?? '', 100, 'combo_name');

        if (!empty($data['combo_description'])) {
            $validator->maxLength($data['combo_description'], 255, 'combo_description');
        }

        $errors = $validator->isValid() ? [] : $validator->getFirstErrors();

        if (!isset($errors['food_type']) && !$this->isAllowedFoodType($data['food_type'] ?? '')) {
            $errors['food_type'] = 'Please select a valid food type.';
        }

        $priceError = $this->validateComboPrice($data['combo_price'] ?? '');
        if ($priceError !== null && !isset($errors['combo_price'])) {
            $errors['combo_price'] = $priceError;
        }

        return $errors;
    }

    private function isAllowedFoodType(string $value): bool
    {
        return in_array($value, Restaurant::foodTypes(), true);
    }

    private function validateComboPrice(string $value): ?string
    {
        if (trim($value) === '') {
            return 'Combo price is required.';
        }
        if (!is_numeric($value)) {
            return 'Combo price must be a valid number.';
        }
        $price = (float) $value;
        if ($price < 0) {
            return 'Combo price cannot be negative.';
        }
        if ($price > 999999.99) {
            return 'Combo price must not exceed 999999.99.';
        }
        return null;
    }
}
