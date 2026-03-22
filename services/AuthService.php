<?php

/**
 * Authentication and registration business logic.
 */
class AuthService
{
    private UserRepository $userRepo;

    public function __construct()
    {
        $this->userRepo = new UserRepository();
    }

    /**
     * Authenticate a user by username and password.
     * Returns the User on success, or an error string on failure.
     */
    public function authenticate(string $username, string $password): User|string
    {
        $row = $this->userRepo->findByUsername($username);

        if (!$row) {
            return 'Invalid credentials.';
        }

        $user = User::fromArray($row);

        if (!$user->isActive) {
            return 'This account has been deactivated.';
        }

        if (!password_verify($password, $user->passwordHash)) {
            return 'Invalid credentials.';
        }

        return $user;
    }

    /**
     * Register a new user. Returns user ID on success, or error array on failure.
     */
    public function register(array $data): int|array
    {
        $validator = new Validator();
        $validator
            ->required($data['username'], 'username')
            ->alphanumeric($data['username'], 'username')
            ->minLength($data['username'], 3, 'username')
            ->maxLength($data['username'], 50, 'username')
            ->required($data['email'], 'email')
            ->email($data['email'], 'email')
            ->maxLength($data['email'], 100, 'email')
            ->required($data['password'], 'password')
            ->minLength($data['password'], 8, 'password')
            ->maxLength($data['password'], 72, 'password')
            ->required($data['password_confirm'], 'password')
            ->matches($data['password'], $data['password_confirm'], 'password');

        if (!$validator->isValid()) {
            return $validator->getFirstErrors();
        }

        if ($this->userRepo->findByUsername($data['username'])) {
            return ['username' => 'This username is already taken.'];
        }

        if ($this->userRepo->findByEmail($data['email'])) {
            return ['email' => 'This email is already registered.'];
        }

        $passwordHash = password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 12]);

        return $this->userRepo->create([
            'username'      => trim($data['username']),
            'email'         => trim($data['email']),
            'password_hash' => $passwordHash,
            'role'          => $data['role'] ?? 'user',
        ]);
    }
}
