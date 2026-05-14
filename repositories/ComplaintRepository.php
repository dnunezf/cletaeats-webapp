<?php

/**
 * complaints via stored procedures + joined reads.
 */
class ComplaintRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function findByOrderId(int $orderId): ?array
    {
        $stmt = $this->db->prepare('CALL sp_read_complaint(?)');
        $stmt->execute([$orderId]);
        $row = $stmt->fetch();
        $stmt->closeCursor();
        return $row ?: null;
    }

    public function findAll(): array
    {
        $stmt = $this->db->query(
            'SELECT cmp.*,
                    cu.username AS customer_name,
                    du.username AS driver_name,
                    o.driver_id
             FROM complaints cmp
             JOIN orders o     ON o.id = cmp.order_id
             JOIN customers c  ON c.user_id = o.customer_id
             JOIN users cu     ON cu.id = c.user_id
             JOIN drivers d    ON d.user_id = o.driver_id
             JOIN users du     ON du.id = d.user_id
             ORDER BY cmp.order_id DESC'
        );
        return $stmt->fetchAll();
    }

    public function create(int $orderId, string $content, int $rating): bool
    {
        $stmt = $this->db->prepare('CALL sp_create_complaint(?, ?, ?)');
        $stmt->execute([$orderId, $content, $rating]);
        $stmt->closeCursor();
        return true;
    }

    public function update(int $orderId, string $content, int $rating): bool
    {
        $stmt = $this->db->prepare('CALL sp_update_complaint(?, ?, ?)');
        $stmt->execute([$orderId, $content, $rating]);
        $row = $stmt->fetch();
        $stmt->closeCursor();
        return (int) ($row['rows_affected'] ?? 0) > 0;
    }

    public function delete(int $orderId): bool
    {
        $stmt = $this->db->prepare('CALL sp_delete_complaint(?)');
        $stmt->execute([$orderId]);
        $row = $stmt->fetch();
        $stmt->closeCursor();
        return (int) ($row['rows_affected'] ?? 0) > 0;
    }
}
