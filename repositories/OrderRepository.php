<?php

/**
 * SQL queries for the orders table.
 */
class OrderRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function findAll(): array
    {
        $stmt = $this->db->query(
            "SELECT o.*,
                    CONCAT(c.first_name, ' ', c.last_name) AS customer_name,
                    r.name AS restaurant_name,
                    r.food_type
             FROM orders o
             JOIN customers c ON c.id = o.customer_id
             JOIN restaurants r ON r.id = o.restaurant_id
             ORDER BY o.created_at DESC"
        );
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT o.*,
                    CONCAT(c.first_name, ' ', c.last_name) AS customer_name,
                    c.email AS customer_email,
                    c.phone_number AS customer_phone,
                    r.name AS restaurant_name,
                    r.food_type,
                    r.address AS restaurant_address
             FROM orders o
             JOIN customers c ON c.id = o.customer_id
             JOIN restaurants r ON r.id = o.restaurant_id
             WHERE o.id = ? LIMIT 1"
        );
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function search(string $term): array
    {
        $like = '%' . $term . '%';
        $stmt = $this->db->prepare(
            "SELECT o.*,
                    CONCAT(c.first_name, ' ', c.last_name) AS customer_name,
                    r.name AS restaurant_name,
                    r.food_type
             FROM orders o
             JOIN customers c ON c.id = o.customer_id
             JOIN restaurants r ON r.id = o.restaurant_id
             WHERE CONCAT(c.first_name, ' ', c.last_name) LIKE ?
                OR r.name LIKE ?
                OR o.combo_name LIKE ?
                OR o.status LIKE ?
             ORDER BY o.created_at DESC"
        );
        $stmt->execute([$like, $like, $like, $like]);
        return $stmt->fetchAll();
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO orders (customer_id, restaurant_id, combo_name, combo_price, quantity, total, notes)
             VALUES (?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            (int) $data['customer_id'],
            (int) $data['restaurant_id'],
            $data['combo_name'],
            (float) $data['combo_price'],
            (int) $data['quantity'],
            (float) $data['total'],
            $data['notes'] ?: null,
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM orders WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->rowCount() > 0;
    }
}
