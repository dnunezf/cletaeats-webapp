<?php

/**
 * User data transfer object — new schema (users + locations).
 */
class User
{
    public int $id;
    public string $username;
    public string $email;
    public string $passwordHash;
    public string $role;
    public string $status;
    public string $document;
    public int $locationId;

    public static function fromArray(array $data): self
    {
        $user = new self();
        $user->id           = (int) ($data['id'] ?? 0);
        $user->username     = $data['username'] ?? '';
        $user->email        = $data['email'] ?? '';
        $user->passwordHash = $data['password_hash'] ?? '';
        $user->role         = $data['role'] ?? 'customer';
        $user->status       = $data['status'] ?? 'inactive';
        $user->document     = $data['document'] ?? '';
        $user->locationId   = (int) ($data['location_id'] ?? 0);
        return $user;
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isPending(): bool
    {
        return $this->status === 'inactive';
    }

    public static function roles(): array
    {
        return ['customer', 'driver', 'restaurant', 'admin'];
    }

    public static function statuses(): array
    {
        return ['inactive', 'active'];
    }
}
