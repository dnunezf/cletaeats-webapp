<?php

/**
 * Delivery driver business logic: CRUD orchestration and validation.
 */
class DeliveryDriverService
{
    private DeliveryDriverRepository $repo;

    public function __construct()
    {
        $this->repo = new DeliveryDriverRepository();
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
     * Create a delivery driver. Returns the new ID on success or error array on failure.
     */
    public function create(array $data): int|array
    {
        $errors = $this->validate($data);
        if (!empty($errors)) {
            return $errors;
        }

        if ($this->repo->findByIdNumber($data['id_number'])) {
            return ['id_number' => 'This ID number is already registered to another driver.'];
        }

        return $this->repo->create($data);
    }

    /**
     * Update a delivery driver. Returns true on success or error array on failure.
     */
    public function update(int $id, array $data): bool|array
    {
        $errors = $this->validate($data);
        if (!empty($errors)) {
            return $errors;
        }

        if ($this->repo->findByIdNumberExcluding($data['id_number'], $id)) {
            return ['id_number' => 'This ID number is already registered to another driver.'];
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
            ->required($data['full_name'] ?? '', 'full_name')
            ->minLength($data['full_name'] ?? '', 2, 'full_name')
            ->maxLength($data['full_name'] ?? '', 100, 'full_name')
            ->required($data['id_number'] ?? '', 'id_number')
            ->minLength($data['id_number'] ?? '', 3, 'id_number')
            ->maxLength($data['id_number'] ?? '', 30, 'id_number')
            ->required($data['email'] ?? '', 'email')
            ->email($data['email'] ?? '', 'email')
            ->maxLength($data['email'] ?? '', 100, 'email')
            ->required($data['address'] ?? '', 'address')
            ->maxLength($data['address'] ?? '', 255, 'address')
            ->required($data['phone'] ?? '', 'phone')
            ->phone($data['phone'] ?? '', 'phone')
            ->maxLength($data['phone'] ?? '', 20, 'phone')
            ->required($data['card_number'] ?? '', 'card_number')
            ->maxLength($data['card_number'] ?? '', 32, 'card_number')
            ->required($data['status'] ?? '', 'status');

        if (!empty($data['complaints'])) {
            $validator->maxLength($data['complaints'], 2000, 'complaints');
        }

        $errors = $validator->isValid() ? [] : $validator->getFirstErrors();

        if (!isset($errors['card_number']) && ($data['card_number'] ?? '') !== '') {
            if (!preg_match('/^\d{13,19}$/', $data['card_number'])) {
                $errors['card_number'] = 'Card number must contain 13 to 19 digits only.';
            }
        }

        if (!isset($errors['status']) && !$this->isAllowedStatus($data['status'] ?? '')) {
            $errors['status'] = 'Please select a valid status.';
        }

        $numericFields = [
            'order_distance'      => 'Order distance',
            'daily_kilometers'    => 'Daily kilometers',
            'weekday_cost_per_km' => 'Weekday cost per km',
            'holiday_cost_per_km' => 'Holiday cost per km',
        ];
        foreach ($numericFields as $field => $label) {
            if (isset($errors[$field])) {
                continue;
            }
            $err = $this->validateNonNegativeNumeric($data[$field] ?? '', $label);
            if ($err !== null) {
                $errors[$field] = $err;
            }
        }

        if (!isset($errors['warning_count'])) {
            $err = $this->validateWarningCount($data['warning_count'] ?? '');
            if ($err !== null) {
                $errors['warning_count'] = $err;
            }
        }

        return $errors;
    }

    private function isAllowedStatus(string $value): bool
    {
        return in_array($value, DeliveryDriver::statuses(), true);
    }

    private function validateNonNegativeNumeric(string $value, string $label): ?string
    {
        if (trim($value) === '') {
            return $label . ' is required.';
        }
        if (!is_numeric($value)) {
            return $label . ' must be a valid number.';
        }
        $num = (float) $value;
        if ($num < 0) {
            return $label . ' cannot be negative.';
        }
        if ($num > 999999.99) {
            return $label . ' must not exceed 999999.99.';
        }
        return null;
    }

    private function validateWarningCount(string $value): ?string
    {
        if (trim($value) === '') {
            return 'Warning count is required.';
        }
        if (!preg_match('/^\d+$/', $value)) {
            return 'Warning count must be a non-negative integer.';
        }
        $num = (int) $value;
        if ($num < 0 || $num > 99) {
            return 'Warning count must be between 0 and 99.';
        }
        return null;
    }
}
