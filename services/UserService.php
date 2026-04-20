<?php

/**
 * User management business logic.
 */
class UserService
{
    private UserRepository $repo;

    public function __construct()
    {
        $this->repo = new UserRepository();
    }

    public function getAll(): array
    {
        return $this->repo->findAll();
    }

    public function search(string $term): array
    {
        return $this->repo->search($term);
    }

    public function getById(int $id): ?array
    {
        return $this->repo->findById($id);
    }

    public function getPendingUsers(): array
    {
        return $this->repo->findByStatus('pending');
    }

    public function approveUser(int $id): bool
    {
        return $this->repo->updateStatus($id, 'active');
    }

    /**
     * Update user profile. Returns true on success or error array.
     */
    public function update(int $id, array $data): bool|array
    {
        $existing = $this->repo->findById($id);
        if (!$existing) {
            return ['general' => 'User not found.'];
        }

        $validator = new Validator();
        $validator
            ->required($data['username'], 'username')
            ->alphanumeric($data['username'], 'username')
            ->minLength($data['username'], 3, 'username')
            ->maxLength($data['username'], 50, 'username')
            ->required($data['email'], 'email')
            ->email($data['email'], 'email')
            ->maxLength($data['email'], 100, 'email');

        if (!in_array($data['role'] ?? '', ['admin', 'user'], true)) {
            $validator->required('', 'role');
        }

        if (!in_array($data['status'] ?? '', ['active', 'pending'], true)) {
            $validator->required('', 'status');
        }

        if (!empty($data['password'])) {
            $validator
                ->minLength($data['password'], 8, 'password')
                ->maxLength($data['password'], 72, 'password')
                ->matches($data['password'], $data['password_confirm'] ?? '', 'password');
        }

        if (!$validator->isValid()) {
            return $validator->getFirstErrors();
        }

        if ($this->repo->findByUsernameExcluding($data['username'], $id)) {
            return ['username' => 'This username is already taken.'];
        }

        if ($this->repo->findByEmailExcluding($data['email'], $id)) {
            return ['email' => 'This email is already registered.'];
        }

        // Prevent demoting or deactivating the last active admin.
        $wasAdminActive = ($existing['role'] === 'admin') && ((int) $existing['is_active'] === 1);
        $willBeAdminActive = ($data['role'] === 'admin') && ((int) $data['is_active'] === 1);

        if ($wasAdminActive && !$willBeAdminActive && $this->repo->countAdmins() <= 1) {
            return ['role' => 'Cannot remove the last active administrator.'];
        }

        $ok = $this->repo->update($id, [
            'username'  => trim($data['username']),
            'email'     => trim($data['email']),
            'role'      => $data['role'],
            'status'    => $data['status'],
            'is_active' => (int) $data['is_active'],
        ]);

        if ($ok && !empty($data['password'])) {
            $hash = password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 12]);
            $this->repo->updatePassword($id, $hash);
        }

        return $ok ? true : ['general' => 'Unable to update user.'];
    }

    /**
     * Delete a user. Returns true on success or error array.
     */
    public function delete(int $id, int $currentUserId): bool|array
    {
        if ($id === $currentUserId) {
            return ['general' => 'You cannot delete your own account.'];
        }

        $existing = $this->repo->findById($id);
        if (!$existing) {
            return ['general' => 'User not found.'];
        }

        if ($existing['role'] === 'admin' && (int) $existing['is_active'] === 1 && $this->repo->countAdmins() <= 1) {
            return ['general' => 'Cannot delete the last active administrator.'];
        }

        return $this->repo->delete($id) ? true : ['general' => 'Unable to delete user.'];
    }
}
