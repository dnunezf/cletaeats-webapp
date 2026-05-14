<?php

/**
 * Authentication + public registration (new schema).
 * Public registration creates: location → user(role=customer, status=inactive) → customer.
 */
class AuthService
{
    private UserRepository $userRepo;
    private LocationRepository $locationRepo;
    private CustomerRepository $customerRepo;

    public function __construct()
    {
        $this->userRepo     = new UserRepository();
        $this->locationRepo = new LocationRepository();
        $this->customerRepo = new CustomerRepository();
    }

    public function authenticate(string $username, string $password): User|string
    {
        $row = $this->userRepo->findByUsername($username);
        if (!$row) {
            return 'Invalid credentials.';
        }
        $user = User::fromArray($row);

        if ($user->status === 'inactive') {
            return 'Your account is pending approval by an administrator.';
        }
        if (!password_verify($password, $user->passwordHash)) {
            return 'Invalid credentials.';
        }
        return $user;
    }

    /**
     * Admin-initiated user registration with role + status flexibility.
     * Returns the new user id or an error array.
     */
    public function register(array $data): int|array
    {
        $errors = $this->validateBase($data, isCreate: true);
        if (!empty($errors)) {
            return $errors;
        }

        $role = in_array($data['role'] ?? '', User::roles(), true) ? $data['role'] : 'customer';

        if ($this->userRepo->findByUsername($data['username'])) {
            return ['username' => 'This username is already taken.'];
        }
        if ($this->userRepo->findByEmail($data['email'])) {
            return ['email' => 'This email is already registered.'];
        }
        if ($this->userRepo->findByDocument($data['document'], $role)) {
            return ['document' => 'This document is already registered for this role.'];
        }

        $passwordHash = password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 12]);

        $pdo = Database::getConnection();
        $pdo->beginTransaction();
        try {
            $locationId = $this->locationRepo->create([
                'address'     => $data['address'],
                'city'        => $data['city'],
                'postal_code' => $data['postal_code'],
            ]);

            $userId = $this->userRepo->create([
                'username'      => trim($data['username']),
                'email'         => trim($data['email']),
                'password_hash' => $passwordHash,
                'role'          => $role,
                'document'      => trim($data['document']),
                'location_id'   => $locationId,
            ]);

            // Activate immediately for admin-driven creation.
            $this->userRepo->updateStatus($userId, 'active');

            // If role is customer, create the customer row (card_number).
            if ($role === 'customer' && !empty($data['card_number'])) {
                $this->customerRepo->create($userId, $data['card_number']);
            }

            $pdo->commit();
            return $userId;
        } catch (Throwable $e) {
            $pdo->rollBack();
            return ['general' => 'Registration failed: ' . $e->getMessage()];
        }
    }

    /**
     * Public sign-up — always creates a customer in 'inactive' status (pending approval).
     */
    public function createAccount(array $data): int|array
    {
        $errors = $this->validateBase($data, isCreate: true);
        if (!empty($errors)) {
            return $errors;
        }
        if (empty($data['card_number']) || !preg_match('/^\d{13,19}$/', $data['card_number'])) {
            return ['card_number' => 'Card number must contain 13 to 19 digits.'];
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

        $passwordHash = password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 12]);

        $pdo = Database::getConnection();
        $pdo->beginTransaction();
        try {
            $locationId = $this->locationRepo->create([
                'address'     => $data['address'],
                'city'        => $data['city'],
                'postal_code' => $data['postal_code'],
            ]);

            $userId = $this->userRepo->create([
                'username'      => trim($data['username']),
                'email'         => trim($data['email']),
                'password_hash' => $passwordHash,
                'role'          => 'customer',
                'document'      => trim($data['document']),
                'location_id'   => $locationId,
            ]);

            $this->customerRepo->create($userId, $data['card_number']);

            $pdo->commit();
            return $userId;
        } catch (Throwable $e) {
            $pdo->rollBack();
            return ['general' => 'Account creation failed: ' . $e->getMessage()];
        }
    }

    private function validateBase(array $data, bool $isCreate): array
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
          ->maxLength($data['address'] ?? '', 255, 'address')
          ->required($data['city'] ?? '', 'city')
          ->maxLength($data['city'] ?? '', 255, 'city')
          ->required($data['postal_code'] ?? '', 'postal_code')
          ->maxLength($data['postal_code'] ?? '', 255, 'postal_code');

        if ($isCreate) {
            $v->required($data['password'] ?? '', 'password')
              ->minLength($data['password'] ?? '', 8, 'password')
              ->maxLength($data['password'] ?? '', 72, 'password')
              ->matches($data['password'] ?? '', $data['password_confirm'] ?? '', 'password');
        }
        return $v->isValid() ? [] : $v->getFirstErrors();
    }
}
