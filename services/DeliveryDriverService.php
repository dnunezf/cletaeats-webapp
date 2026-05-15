<?php

/**
 * Driver CRUD orchestrates users + locations + drivers tables.
 */
class DeliveryDriverService
{
    private DeliveryDriverRepository $repo;
    private UserRepository $userRepo;
    private LocationRepository $locationRepo;

    public function __construct()
    {
        $this->repo         = new DeliveryDriverRepository();
        $this->userRepo     = new UserRepository();
        $this->locationRepo = new LocationRepository();
    }

    public function getAll(): array
    {
        return $this->repo->findAll();
    }

    public function getActive(): array
    {
        return $this->repo->findAllActive();
    }

    public function getById(int $userId): ?array
    {
        return $this->repo->findById($userId);
    }

    public function search(string $term): array
    {
        $term = trim($term);
        return $term === '' ? $this->getAll() : $this->repo->search($term);
    }

    public function create(array $data): int|array
    {
        $errors = $this->validate($data, isCreate: true);
        if (!empty($errors)) {
            return $errors;
        }
        if ($this->userRepo->findByUsername($data['username'])) {
            return ['username' => 'This username is already taken.'];
        }
        if ($this->userRepo->findByEmail($data['email'])) {
            return ['email' => 'This email is already registered.'];
        }
        if ($this->userRepo->findByDocument($data['document'], 'driver')) {
            return ['document' => 'This document is already registered.'];
        }

        $pdo = Database::getConnection();
        $pdo->beginTransaction();
        try {
            $locationId = $this->locationRepo->create([
                'address'     => $data['address'],
                'city'        => $data['city'],
                'postal_code' => $data['postal_code'],
            ]);

            $hash = password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 12]);
            $userId = $this->userRepo->create([
                'username'      => trim($data['username']),
                'email'         => trim($data['email']),
                'password_hash' => $hash,
                'role'          => 'driver',
                'document'      => trim($data['document']),
                'location_id'   => $locationId,
            ]);
            $this->userRepo->updateStatus($userId, 'active');

            $this->repo->create(
                $userId,
                $data['card_number'],
                (float) $data['km_cost_regular'],
                (float) $data['km_cost_holidays']
            );

            $pdo->commit();
            return $userId;
        } catch (Throwable $e) {
            $pdo->rollBack();
            return ['general' => 'Unable to create driver: ' . $e->getMessage()];
        }
    }

    public function update(int $userId, array $data): bool|array
    {
        $existing = $this->repo->findById($userId);
        if (!$existing) {
            return ['general' => 'Driver not found.'];
        }
        $errors = $this->validate($data, isCreate: false);
        if (!empty($errors)) {
            return $errors;
        }

        if ($this->userRepo->findByUsernameExcluding($data['username'], $userId)) {
            return ['username' => 'This username is already taken.'];
        }
        if ($this->userRepo->findByEmailExcluding($data['email'], $userId)) {
            return ['email' => 'This email is already registered.'];
        }
        if ($this->userRepo->findByDocumentExcluding($data['document'], 'driver', $userId)) {
            return ['document' => 'This document is already registered.'];
        }

        $pdo = Database::getConnection();
        $pdo->beginTransaction();
        try {
            $locationId = (int) $existing['location_id'];
            $this->locationRepo->update($locationId, [
                'address'     => $data['address'],
                'city'        => $data['city'],
                'postal_code' => $data['postal_code'],
            ]);

            $this->userRepo->update($userId, [
                'username'      => trim($data['username']),
                'email'         => trim($data['email']),
                'password_hash' => $existing['password_hash'] ?? '',
                'role'          => 'driver',
                'status'        => $data['user_status'] ?? $existing['user_status'] ?? 'active',
                'document'      => trim($data['document']),
                'location_id'   => $locationId,
            ]);

            if (!empty($data['password'])) {
                $hash = password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 12]);
                $this->userRepo->updatePassword($userId, $hash);
            }

            $this->repo->update(
                $userId,
                $data['status'],
                (int) $data['penalties'],
                $data['card_number'],
                (float) $data['km_cost_regular'],
                (float) $data['km_cost_holidays']
            );

            $pdo->commit();
            return true;
        } catch (Throwable $e) {
            $pdo->rollBack();
            return ['general' => 'Unable to update driver: ' . $e->getMessage()];
        }
    }

    public function delete(int $userId): bool
    {
        $pdo = Database::getConnection();
        $pdo->beginTransaction();
        try {
            $this->repo->delete($userId);
            $this->userRepo->delete($userId);
            $pdo->commit();
            return true;
        } catch (Throwable $e) {
            $pdo->rollBack();
            return false;
        }
    }

    private function validate(array $data, bool $isCreate): array
    {
        $v = new Validator();
        $v->required($data['username'] ?? '', 'username')
          ->alphanumeric($data['username'] ?? '', 'username')
          ->minLength($data['username'] ?? '', 3, 'username')
          ->maxLength($data['username'] ?? '', 255, 'username')
          ->required($data['email'] ?? '', 'email')
          ->email($data['email'] ?? '', 'email')
          ->maxLength($data['email'] ?? '', 255, 'email')
          ->required($data['document'] ?? '', 'document')
          ->maxLength($data['document'] ?? '', 255, 'document')
          ->required($data['address'] ?? '', 'address')
          ->required($data['city'] ?? '', 'city')
          ->required($data['postal_code'] ?? '', 'postal_code')
          ->required($data['card_number'] ?? '', 'card_number')
          ->required($data['status'] ?? '', 'status');

        if ($isCreate) {
            $v->required($data['password'] ?? '', 'password')
              ->password($data['password'] ?? '', 'password')
              ->matches($data['password'] ?? '', $data['password_confirm'] ?? '', 'password');
        } elseif (!empty($data['password'])) {
            $v->password($data['password'], 'password')
              ->matches($data['password'], $data['password_confirm'] ?? '', 'password');
        }

        $errors = $v->isValid() ? [] : $v->getFirstErrors();

        if (!isset($errors['card_number']) && !preg_match('/^\d{13,19}$/', $data['card_number'] ?? '')) {
            $errors['card_number'] = 'Card number must contain 13 to 19 digits.';
        }
        if (!isset($errors['status']) && !in_array($data['status'] ?? '', DeliveryDriver::statuses(), true)) {
            $errors['status'] = 'Please select a valid status.';
        }

        foreach (['km_cost_regular' => 'Regular km cost', 'km_cost_holidays' => 'Holidays km cost'] as $field => $label) {
            if (isset($errors[$field])) {
                continue;
            }
            $val = $data[$field] ?? '';
            if (trim($val) === '' || !is_numeric($val) || (float) $val < 0 || (float) $val > 999999.99) {
                $errors[$field] = $label . ' must be a non-negative number up to 999999.99.';
            }
        }

        if (!isset($errors['penalties']) && isset($data['penalties']) && $data['penalties'] !== '') {
            if (!preg_match('/^\d+$/', $data['penalties']) || (int) $data['penalties'] > 99) {
                $errors['penalties'] = 'Penalties must be a non-negative integer ≤ 99.';
            }
        }

        return $errors;
    }
}
