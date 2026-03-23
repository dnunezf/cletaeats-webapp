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

    public function getPendingUsers(): array
    {
        return $this->repo->findByStatus('pending');
    }

    public function approveUser(int $id): bool
    {
        return $this->repo->updateStatus($id, 'active');
    }
}
