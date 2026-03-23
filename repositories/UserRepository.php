<?php

/**
 * SQL queries for the users table.
 */
class UserRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function findByUsername(string $username): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE username = ? LIMIT 1');
        $stmt->execute([$username]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO users (username, email, password_hash, role, is_active, status)
             VALUES (?, ?, ?, ?, 1, ?)'
        );
        $stmt->execute([
            $data['username'],
            $data['email'],
            $data['password_hash'],
            $data['role'] ?? 'user',
            $data['status'] ?? 'active',
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function findByStatus(string $status): array
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE status = ? ORDER BY created_at DESC');
        $stmt->execute([$status]);
        return $stmt->fetchAll();
    }

    public function updateStatus(int $id, string $status): bool
    {
        $stmt = $this->db->prepare('UPDATE users SET status = ?, updated_at = NOW() WHERE id = ?');
        return $stmt->execute([$status, $id]);
    }
}
