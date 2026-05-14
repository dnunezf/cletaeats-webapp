<?php

/**
 * Customer CRUD orchestrates users + locations + customers tables in a transaction.
 */
class CustomerService
{
    private CustomerRepository $repo;
    private UserRepository $userRepo;
    private LocationRepository $locationRepo;

    public function __construct()
    {
        $this->repo         = new CustomerRepository();
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
        if ($this->userRepo->findByDocument($data['document'], 'customer')) {
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
                'role'          => 'customer',
                'document'      => trim($data['document']),
                'location_id'   => $locationId,
            ]);

            // Admin-created customers are active by default.
            $this->userRepo->updateStatus($userId, 'active');

            $this->repo->create($userId, $data['card_number']);

            $pdo->commit();
            return $userId;
        } catch (Throwable $e) {
            $pdo->rollBack();
            return ['general' => 'Unable to create customer: ' . $e->getMessage()];
        }
    }

    public function update(int $userId, array $data): bool|array
    {
        $existing = $this->repo->findById($userId);
        if (!$existing) {
            return ['general' => 'Customer not found.'];
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
        if ($this->userRepo->findByDocumentExcluding($data['document'], 'customer', $userId)) {
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
                'role'          => 'customer',
                'status'        => $data['status'] ?? $existing['status'],
                'document'      => trim($data['document']),
                'location_id'   => $locationId,
            ]);

            if (!empty($data['password'])) {
                $hash = password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 12]);
                $this->userRepo->updatePassword($userId, $hash);
            }

            $this->repo->update($userId, $data['card_number']);

            $pdo->commit();
            return true;
        } catch (Throwable $e) {
            $pdo->rollBack();
            return ['general' => 'Unable to update customer: ' . $e->getMessage()];
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
          ->required($data['card_number'] ?? '', 'card_number');

        if ($isCreate) {
            $v->required($data['password'] ?? '', 'password')
              ->minLength($data['password'] ?? '', 8, 'password')
              ->matches($data['password'] ?? '', $data['password_confirm'] ?? '', 'password');
        } elseif (!empty($data['password'])) {
            $v->minLength($data['password'], 8, 'password')
              ->matches($data['password'], $data['password_confirm'] ?? '', 'password');
        }

        $errors = $v->isValid() ? [] : $v->getFirstErrors();

        if (!isset($errors['card_number']) && !preg_match('/^\d{13,19}$/', $data['card_number'] ?? '')) {
            $errors['card_number'] = 'Card number must contain 13 to 19 digits.';
        }

        return $errors;
    }
}
