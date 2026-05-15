<?php

/**
 * orders + invoice_lines via stored procedures + joined reads.
 */
class OrderRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    private function listSelect(): string
    {
        // Restaurant derived via first invoice_line → combo → restaurant_id → users.username.
        return "SELECT o.*,
                       cu.username AS customer_name,
                       cu.email    AS customer_email,
                       du.username AS driver_name,
                       du.email    AS driver_email,
                       du.document AS driver_document,
                       d.km_cost_regular,
                       d.km_cost_holidays,
                       (SELECT ru.username
                          FROM invoice_lines il
                          JOIN combos cb ON cb.id = il.combo_id
                          JOIN users ru ON ru.id = cb.restaurant_id
                          WHERE il.order_id = o.id
                          LIMIT 1) AS restaurant_name,
                       (SELECT r.category
                          FROM invoice_lines il
                          JOIN combos cb ON cb.id = il.combo_id
                          JOIN restaurants r ON r.user_id = cb.restaurant_id
                          WHERE il.order_id = o.id
                          LIMIT 1) AS category,
                       (SELECT COALESCE(SUM(il2.quantity * il2.combo_price), 0)
                          FROM invoice_lines il2
                          WHERE il2.order_id = o.id) AS total
                FROM orders o
                JOIN customers c  ON c.user_id  = o.customer_id
                JOIN users cu     ON cu.id      = c.user_id
                JOIN drivers d    ON d.user_id  = o.driver_id
                JOIN users du     ON du.id      = d.user_id";
    }

    public function findAll(): array
    {
        $stmt = $this->db->query($this->listSelect() . ' ORDER BY o.creation_date DESC');
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare($this->listSelect() . ' WHERE o.id = ? LIMIT 1');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function search(string $term): array
    {
        $like = '%' . $term . '%';
        $stmt = $this->db->prepare(
            $this->listSelect() .
            ' WHERE cu.username LIKE ? OR du.username LIKE ? OR o.status LIKE ?
              ORDER BY o.creation_date DESC'
        );
        $stmt->execute([$like, $like, $like]);
        return $stmt->fetchAll();
    }

    // ---- Role-scoped finders ---------------------------------------------

    public function findAllByCustomer(int $userId): array
    {
        $stmt = $this->db->prepare(
            $this->listSelect() . ' WHERE o.customer_id = ? ORDER BY o.creation_date DESC'
        );
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function searchByCustomer(int $userId, string $term): array
    {
        $like = '%' . $term . '%';
        $stmt = $this->db->prepare(
            $this->listSelect() .
            ' WHERE o.customer_id = ?
                AND (cu.username LIKE ? OR du.username LIKE ? OR o.status LIKE ?)
              ORDER BY o.creation_date DESC'
        );
        $stmt->execute([$userId, $like, $like, $like]);
        return $stmt->fetchAll();
    }

    public function findAllByDriver(int $userId): array
    {
        $stmt = $this->db->prepare(
            $this->listSelect() . ' WHERE o.driver_id = ? ORDER BY o.creation_date DESC'
        );
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function searchByDriver(int $userId, string $term): array
    {
        $like = '%' . $term . '%';
        $stmt = $this->db->prepare(
            $this->listSelect() .
            ' WHERE o.driver_id = ?
                AND (cu.username LIKE ? OR du.username LIKE ? OR o.status LIKE ?)
              ORDER BY o.creation_date DESC'
        );
        $stmt->execute([$userId, $like, $like, $like]);
        return $stmt->fetchAll();
    }

    public function findAllByRestaurant(int $userId): array
    {
        $stmt = $this->db->prepare(
            $this->listSelect() .
            ' WHERE EXISTS (
                  SELECT 1 FROM invoice_lines il
                  JOIN combos cb ON cb.id = il.combo_id
                  WHERE il.order_id = o.id AND cb.restaurant_id = ?
              )
              ORDER BY o.creation_date DESC'
        );
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function searchByRestaurant(int $userId, string $term): array
    {
        $like = '%' . $term . '%';
        $stmt = $this->db->prepare(
            $this->listSelect() .
            ' WHERE EXISTS (
                  SELECT 1 FROM invoice_lines il
                  JOIN combos cb ON cb.id = il.combo_id
                  WHERE il.order_id = o.id AND cb.restaurant_id = ?
              )
              AND (cu.username LIKE ? OR du.username LIKE ? OR o.status LIKE ?)
              ORDER BY o.creation_date DESC'
        );
        $stmt->execute([$userId, $like, $like, $like]);
        return $stmt->fetchAll();
    }

    // ---- Ownership checks (single-row existence) -------------------------

    public function isOwnedByCustomer(int $orderId, int $userId): bool
    {
        $stmt = $this->db->prepare('SELECT 1 FROM orders WHERE id = ? AND customer_id = ? LIMIT 1');
        $stmt->execute([$orderId, $userId]);
        return (bool) $stmt->fetchColumn();
    }

    public function isOwnedByDriver(int $orderId, int $userId): bool
    {
        $stmt = $this->db->prepare('SELECT 1 FROM orders WHERE id = ? AND driver_id = ? LIMIT 1');
        $stmt->execute([$orderId, $userId]);
        return (bool) $stmt->fetchColumn();
    }

    public function isVisibleToRestaurant(int $orderId, int $userId): bool
    {
        $stmt = $this->db->prepare(
            'SELECT 1
               FROM invoice_lines il
               JOIN combos cb ON cb.id = il.combo_id
              WHERE il.order_id = ? AND cb.restaurant_id = ?
              LIMIT 1'
        );
        $stmt->execute([$orderId, $userId]);
        return (bool) $stmt->fetchColumn();
    }

    public function findInvoiceLines(int $orderId): array
    {
        $stmt = $this->db->prepare(
            'SELECT il.combo_id, il.quantity, il.combo_price,
                    c.name AS combo_name, c.description AS combo_description
             FROM invoice_lines il
             JOIN combos c ON c.id = il.combo_id
             WHERE il.order_id = ?
             ORDER BY c.name ASC'
        );
        $stmt->execute([$orderId]);
        return $stmt->fetchAll();
    }

    public function create(int $customerId, int $driverId): int
    {
        $stmt = $this->db->prepare('CALL sp_create_order(?, ?)');
        $stmt->execute([$customerId, $driverId]);
        $row = $stmt->fetch();
        $stmt->closeCursor();
        return (int) ($row['id'] ?? 0);
    }

    public function addInvoiceLine(int $comboId, int $orderId, int $quantity): bool
    {
        $stmt = $this->db->prepare('CALL sp_create_invoice_line(?, ?, ?)');
        $stmt->execute([$comboId, $orderId, $quantity]);
        $stmt->closeCursor();
        return true;
    }

    public function updateStatus(int $id, string $status, ?string $deliveredDate): bool
    {
        $stmt = $this->db->prepare('CALL sp_update_order(?, ?, ?)');
        $stmt->execute([$id, $status, $deliveredDate]);
        $row = $stmt->fetch();
        $stmt->closeCursor();
        return (int) ($row['rows_affected'] ?? 0) > 0;
    }

    public function deleteInvoiceLine(int $comboId, int $orderId): bool
    {
        $stmt = $this->db->prepare('CALL sp_delete_invoice_line(?, ?)');
        $stmt->execute([$comboId, $orderId]);
        $row = $stmt->fetch();
        $stmt->closeCursor();
        return (int) ($row['rows_affected'] ?? 0) > 0;
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('CALL sp_delete_order(?)');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        $stmt->closeCursor();
        return (int) ($row['rows_affected'] ?? 0) > 0;
    }
}
