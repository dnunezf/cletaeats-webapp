<?php

/**
 * User management — role/status driven by new enums.
 */
class UserService
{
    private UserRepository $repo;
    private LocationRepository $locationRepo;

    public function __construct()
    {
        $this->repo         = new UserRepository();
        $this->locationRepo = new LocationRepository();
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
        return $this->repo->findByStatus('inactive');
    }

    public function approveUser(int $id): bool
    {
        return $this->repo->updateStatus($id, 'active');
    }

    public function update(int $id, array $data): bool|array
    {
        $existing = $this->repo->findById($id);
        if (!$existing) {
            return ['general' => 'User not found.'];
        }

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
          ->required($data['postal_code'] ?? '', 'postal_code');

        if (!in_array($data['role'] ?? '', User::roles(), true)) {
            $v->required('', 'role');
        }
        if (!in_array($data['status'] ?? '', User::statuses(), true)) {
            $v->required('', 'status');
        }

        if (!empty($data['password'])) {
            $v->minLength($data['password'], 8, 'password')
              ->maxLength($data['password'], 72, 'password')
              ->matches($data['password'], $data['password_confirm'] ?? '', 'password');
        }

        if (!$v->isValid()) {
            return $v->getFirstErrors();
        }

        if ($this->repo->findByUsernameExcluding($data['username'], $id)) {
            return ['username' => 'This username is already taken.'];
        }
        if ($this->repo->findByEmailExcluding($data['email'], $id)) {
            return ['email' => 'This email is already registered.'];
        }
        if ($this->repo->findByDocumentExcluding($data['document'], $data['role'], $id)) {
            return ['document' => 'This document is already registered for this role.'];
        }

        // Prevent removing the last active admin.
        $wasActiveAdmin = ($existing['role'] === 'admin') && ($existing['status'] === 'active');
        $willBeActiveAdmin = ($data['role'] === 'admin') && ($data['status'] === 'active');
        if ($wasActiveAdmin && !$willBeActiveAdmin && $this->repo->countActiveAdmins() <= 1) {
            return ['role' => 'Cannot remove the last active administrator.'];
        }

        $locationId = (int) $existing['location_id'];
        $this->locationRepo->update($locationId, [
            'address'     => $data['address'],
            'city'        => $data['city'],
            'postal_code' => $data['postal_code'],
        ]);

        $passwordHash = $existing['password_hash'];
        $this->repo->update($id, [
            'username'      => trim($data['username']),
            'email'         => trim($data['email']),
            'password_hash' => $passwordHash,
            'role'          => $data['role'],
            'status'        => $data['status'],
            'document'      => trim($data['document']),
            'location_id'   => $locationId,
        ]);

        if (!empty($data['password'])) {
            $hash = password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 12]);
            $this->repo->updatePassword($id, $hash);
        }

        return true;
    }

    public function delete(int $id, int $currentUserId): bool|array
    {
        if ($id === $currentUserId) {
            return ['general' => 'You cannot delete your own account.'];
        }
        $existing = $this->repo->findById($id);
        if (!$existing) {
            return ['general' => 'User not found.'];
        }
        if ($existing['role'] === 'admin' && $existing['status'] === 'active' && $this->repo->countActiveAdmins() <= 1) {
            return ['general' => 'Cannot delete the last active administrator.'];
        }
        return $this->repo->delete($id) ? true : ['general' => 'Unable to delete user.'];
    }
}
