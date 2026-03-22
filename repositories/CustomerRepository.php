<?php

/**
 * SQL queries for the customers table.
 */
class CustomerRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function findAllActive(): array
    {
        $stmt = $this->db->query(
            'SELECT * FROM customers WHERE is_active = 1 ORDER BY first_name ASC, last_name ASC'
        );
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM customers WHERE id = ? AND is_active = 1 LIMIT 1');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM customers WHERE email = ? AND is_active = 1 LIMIT 1');
        $stmt->execute([$email]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function search(string $term): array
    {
        $like = '%' . $term . '%';
        $stmt = $this->db->prepare(
            "SELECT * FROM customers
             WHERE is_active = 1
               AND (first_name LIKE ? OR last_name LIKE ? OR email LIKE ? OR phone_number LIKE ? OR city LIKE ?)
             ORDER BY first_name ASC, last_name ASC"
        );
        $stmt->execute([$like, $like, $like, $like, $like]);
        return $stmt->fetchAll();
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO customers (first_name, last_name, email, phone_number, address, city, postal_code)
             VALUES (?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $data['first_name'],
            $data['last_name'],
            $data['email'],
            $data['phone_number'] ?: null,
            $data['address'] ?: null,
            $data['city'] ?: null,
            $data['postal_code'] ?: null,
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE customers
             SET first_name = ?, last_name = ?, email = ?, phone_number = ?, address = ?, city = ?, postal_code = ?
             WHERE id = ? AND is_active = 1'
        );
        return $stmt->execute([
            $data['first_name'],
            $data['last_name'],
            $data['email'],
            $data['phone_number'] ?: null,
            $data['address'] ?: null,
            $data['city'] ?: null,
            $data['postal_code'] ?: null,
            $id,
        ]);
    }

    public function softDelete(int $id): bool
    {
        $stmt = $this->db->prepare('UPDATE customers SET is_active = 0 WHERE id = ?');
        return $stmt->execute([$id]);
    }

    public function findByEmailExcluding(string $email, int $excludeId): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM customers WHERE email = ? AND id != ? AND is_active = 1 LIMIT 1'
        );
        $stmt->execute([$email, $excludeId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }
}
