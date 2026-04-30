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
                    r.food_type,
                    d.full_name AS driver_name,
                    d.phone     AS driver_phone
             FROM orders o
             JOIN customers c    ON c.id = o.customer_id
             JOIN restaurants r  ON r.id = o.restaurant_id
             LEFT JOIN delivery_drivers d ON d.id = o.assigned_driver_id
             ORDER BY o.created_at DESC"
        );
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT o.*,
                    CONCAT(c.first_name, ' ', c.last_name) AS customer_name,
                    c.email        AS customer_email,
                    c.phone_number AS customer_phone,
                    r.name         AS restaurant_name,
                    r.food_type,
                    r.address      AS restaurant_address,
                    d.full_name    AS driver_name,
                    d.phone        AS driver_phone
             FROM orders o
             JOIN customers c    ON c.id = o.customer_id
             JOIN restaurants r  ON r.id = o.restaurant_id
             LEFT JOIN delivery_drivers d ON d.id = o.assigned_driver_id
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
                    r.name  AS restaurant_name,
                    r.food_type,
                    d.full_name AS driver_name,
                    d.phone     AS driver_phone
             FROM orders o
             JOIN customers c    ON c.id = o.customer_id
             JOIN restaurants r  ON r.id = o.restaurant_id
             LEFT JOIN delivery_drivers d ON d.id = o.assigned_driver_id
             WHERE CONCAT(c.first_name, ' ', c.last_name) LIKE ?
                OR r.name LIKE ?
                OR o.combo_name LIKE ?
                OR o.status LIKE ?
                OR d.full_name LIKE ?
             ORDER BY o.created_at DESC"
        );
        $stmt->execute([$like, $like, $like, $like, $like]);
        return $stmt->fetchAll();
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO orders
                (customer_id, restaurant_id, assigned_driver_id, combo_name, combo_price, quantity, total, status, notes)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            (int) $data['customer_id'],
            (int) $data['restaurant_id'],
            isset($data['assigned_driver_id']) ? (int) $data['assigned_driver_id'] : null,
            $data['combo_name'],
            (float) $data['combo_price'],
            (int) $data['quantity'],
            (float) $data['total'],
            $data['status'] ?? 'preparing',
            $data['notes'] ?: null,
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function updateStatus(int $id, string $status, ?string $deliveredAt): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE orders SET status = ?, delivered_at = ?, updated_at = NOW() WHERE id = ?'
        );
        $stmt->execute([$status, $deliveredAt, $id]);
        return $stmt->rowCount() > 0;
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM orders WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->rowCount() > 0;
    }

    /**
     * Returns the id of the first available driver with fewer than 4 warnings, or null.
     */
    public function findFirstAvailableDriverId(): ?int
    {
        $stmt = $this->db->query(
            "SELECT id FROM delivery_drivers
             WHERE is_active = 1 AND status = 'available' AND warning_count < 4
             ORDER BY id ASC LIMIT 1"
        );
        $row = $stmt->fetch();
        return $row ? (int) $row['id'] : null;
    }
}
