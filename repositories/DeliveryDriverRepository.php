<?php

/**
 * drivers via stored procedures + joined reads.
 */
class DeliveryDriverRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    private function joinedSelect(): string
    {
        return "SELECT d.user_id, d.status, d.penalties, d.card_number,
                       d.km_cost_regular, d.km_cost_holidays,
                       u.username, u.email, u.document, u.status AS user_status, u.role, u.location_id,
                       l.address, l.city, l.postal_code
                FROM drivers d
                JOIN users u ON u.id = d.user_id
                LEFT JOIN locations l ON l.id = u.location_id";
    }

    public function findAll(): array
    {
        $stmt = $this->db->query($this->joinedSelect() . ' ORDER BY u.username ASC');
        return $stmt->fetchAll();
    }

    public function findAllActive(): array
    {
        $stmt = $this->db->query($this->joinedSelect() . " WHERE u.status = 'active' ORDER BY u.username ASC");
        return $stmt->fetchAll();
    }

    public function findById(int $userId): ?array
    {
        $stmt = $this->db->prepare($this->joinedSelect() . ' WHERE d.user_id = ? LIMIT 1');
        $stmt->execute([$userId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function search(string $term): array
    {
        $like = '%' . $term . '%';
        $stmt = $this->db->prepare(
            $this->joinedSelect() .
            ' WHERE u.username LIKE ? OR u.email LIKE ? OR u.document LIKE ? OR d.card_number LIKE ? OR l.city LIKE ?
              ORDER BY u.username ASC'
        );
        $stmt->execute([$like, $like, $like, $like, $like]);
        return $stmt->fetchAll();
    }

    public function create(int $userId, string $cardNumber, float $kmCostRegular, float $kmCostHolidays): int
    {
        $stmt = $this->db->prepare('CALL sp_create_driver(?, ?, ?, ?)');
        $stmt->execute([$userId, $cardNumber, $kmCostRegular, $kmCostHolidays]);
        $stmt->closeCursor();
        return $userId;
    }

    public function update(int $userId, string $status, int $penalties, string $cardNumber, float $kmCostRegular, float $kmCostHolidays): bool
    {
        $stmt = $this->db->prepare('CALL sp_update_driver(?, ?, ?, ?, ?, ?)');
        $stmt->execute([$userId, $status, $penalties, $cardNumber, $kmCostRegular, $kmCostHolidays]);
        $row = $stmt->fetch();
        $stmt->closeCursor();
        return (int) ($row['rows_affected'] ?? 0) > 0;
    }

    public function updateAvailability(int $userId, string $status): bool
    {
        $stmt = $this->db->prepare('UPDATE drivers SET status = ? WHERE user_id = ?');
        return $stmt->execute([$status, $userId]);
    }

    public function delete(int $userId): bool
    {
        $stmt = $this->db->prepare('CALL sp_delete_driver(?)');
        $stmt->execute([$userId]);
        $row = $stmt->fetch();
        $stmt->closeCursor();
        return (int) ($row['rows_affected'] ?? 0) > 0;
    }

    public function findFirstAvailableDriverId(): ?int
    {
        $stmt = $this->db->query(
            "SELECT d.user_id
             FROM drivers d
             JOIN users u ON u.id = d.user_id
             WHERE u.status = 'active' AND d.status = 'available' AND d.penalties < 4
             ORDER BY d.user_id ASC LIMIT 1"
        );
        $row = $stmt->fetch();
        return $row ? (int) $row['user_id'] : null;
    }

    public function hasOngoingOrders(int $driverUserId): bool
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM orders WHERE driver_id = ? AND status IN ('pending','ongoing')");
        $stmt->execute([$driverUserId]);
        return (int) $stmt->fetchColumn() > 0;
    }
}
