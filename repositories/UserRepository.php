<?php

/**
 * users table — direct reads + stored-procedure writes.
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

    public function findByDocument(string $document, string $role): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE document = ? AND role = ? LIMIT 1');
        $stmt->execute([$document, $role]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT u.*, l.address, l.city, l.postal_code
             FROM users u
             LEFT JOIN locations l ON l.id = u.location_id
             WHERE u.id = ? LIMIT 1'
        );
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare('CALL sp_create_user(?, ?, ?, ?, ?, ?)');
        $stmt->execute([
            $data['username'],
            $data['email'],
            $data['password_hash'],
            $data['role'],
            $data['document'],
            (int) $data['location_id'],
        ]);
        $row = $stmt->fetch();
        $stmt->closeCursor();
        return (int) ($row['id'] ?? 0);
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare('CALL sp_update_user(?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([
            $id,
            $data['username'],
            $data['email'],
            $data['password_hash'],
            $data['role'],
            $data['status'],
            $data['document'],
            (int) $data['location_id'],
        ]);
        $row = $stmt->fetch();
        $stmt->closeCursor();
        return (int) ($row['rows_affected'] ?? 0) > 0;
    }

    public function updatePassword(int $id, string $passwordHash): bool
    {
        $stmt = $this->db->prepare('UPDATE users SET password_hash = ? WHERE id = ?');
        return $stmt->execute([$passwordHash, $id]);
    }

    public function updateStatus(int $id, string $status): bool
    {
        $stmt = $this->db->prepare('UPDATE users SET status = ? WHERE id = ?');
        return $stmt->execute([$status, $id]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('CALL sp_delete_user(?)');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        $stmt->closeCursor();
        return (int) ($row['rows_affected'] ?? 0) > 0;
    }

    public function findByStatus(string $status): array
    {
        $stmt = $this->db->prepare(
            'SELECT u.*, l.address, l.city, l.postal_code
             FROM users u
             LEFT JOIN locations l ON l.id = u.location_id
             WHERE u.status = ?
             ORDER BY u.id DESC'
        );
        $stmt->execute([$status]);
        return $stmt->fetchAll();
    }

    public function findAll(): array
    {
        $stmt = $this->db->query(
            'SELECT u.*, l.address, l.city, l.postal_code
             FROM users u
             LEFT JOIN locations l ON l.id = u.location_id
             ORDER BY u.id DESC'
        );
        return $stmt->fetchAll();
    }

    public function search(string $term): array
    {
        $like = '%' . $term . '%';
        $stmt = $this->db->prepare(
            'SELECT u.*, l.address, l.city, l.postal_code
             FROM users u
             LEFT JOIN locations l ON l.id = u.location_id
             WHERE u.username LIKE ? OR u.email LIKE ? OR u.role LIKE ? OR u.status LIKE ? OR u.document LIKE ?
             ORDER BY u.id DESC'
        );
        $stmt->execute([$like, $like, $like, $like, $like]);
        return $stmt->fetchAll();
    }

    public function findByUsernameExcluding(string $username, int $excludeId): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE username = ? AND id <> ? LIMIT 1');
        $stmt->execute([$username, $excludeId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function findByEmailExcluding(string $email, int $excludeId): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE email = ? AND id <> ? LIMIT 1');
        $stmt->execute([$email, $excludeId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function findByDocumentExcluding(string $document, string $role, int $excludeId): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE document = ? AND role = ? AND id <> ? LIMIT 1');
        $stmt->execute([$document, $role, $excludeId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function countActiveAdmins(): int
    {
        $stmt = $this->db->query("SELECT COUNT(*) FROM users WHERE role = 'admin' AND status = 'active'");
        return (int) $stmt->fetchColumn();
    }
}
