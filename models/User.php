<?php

/**
 * User data transfer object.
 */
class User
{
    public int $id;
    public string $username;
    public string $email;
    public string $passwordHash;
    public string $role;
    public bool $isActive;
    public string $createdAt;
    public string $updatedAt;

    public static function fromArray(array $data): self
    {
        $user = new self();
        $user->id           = (int) ($data['id'] ?? 0);
        $user->username     = $data['username'] ?? '';
        $user->email        = $data['email'] ?? '';
        $user->passwordHash = $data['password_hash'] ?? '';
        $user->role         = $data['role'] ?? 'user';
        $user->isActive     = (bool) ($data['is_active'] ?? true);
        $user->createdAt    = $data['created_at'] ?? '';
        $user->updatedAt    = $data['updated_at'] ?? '';
        return $user;
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
}
