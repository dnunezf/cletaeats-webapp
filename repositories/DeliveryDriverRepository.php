<?php

/**
 * SQL queries for the delivery_drivers table.
 */
class DeliveryDriverRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function findAllActive(): array
    {
        $stmt = $this->db->query(
            'SELECT * FROM delivery_drivers WHERE is_active = 1 ORDER BY full_name ASC'
        );
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM delivery_drivers WHERE id = ? AND is_active = 1 LIMIT 1');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function findByIdNumber(string $idNumber): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM delivery_drivers WHERE id_number = ? AND is_active = 1 LIMIT 1');
        $stmt->execute([$idNumber]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function findByIdNumberExcluding(string $idNumber, int $excludeId): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM delivery_drivers WHERE id_number = ? AND id != ? AND is_active = 1 LIMIT 1'
        );
        $stmt->execute([$idNumber, $excludeId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function search(string $term): array
    {
        $like = '%' . $term . '%';
        $stmt = $this->db->prepare(
            "SELECT * FROM delivery_drivers
             WHERE is_active = 1
               AND (full_name LIKE ? OR id_number LIKE ? OR phone LIKE ? OR email LIKE ? OR address LIKE ?)
             ORDER BY full_name ASC"
        );
        $stmt->execute([$like, $like, $like, $like, $like]);
        return $stmt->fetchAll();
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO delivery_drivers
             (full_name, id_number, email, address, phone, card_number, status,
              order_distance, daily_kilometers, weekday_cost_per_km, holiday_cost_per_km,
              warning_count, complaints)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $data['full_name'],
            $data['id_number'],
            $data['email'],
            $data['address'],
            $data['phone'],
            $data['card_number'],
            $data['status'],
            (float) $data['order_distance'],
            (float) $data['daily_kilometers'],
            (float) $data['weekday_cost_per_km'],
            (float) $data['holiday_cost_per_km'],
            (int) $data['warning_count'],
            $data['complaints'] !== '' ? $data['complaints'] : null,
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE delivery_drivers
             SET full_name = ?, id_number = ?, email = ?, address = ?, phone = ?,
                 card_number = ?, status = ?, order_distance = ?, daily_kilometers = ?,
                 weekday_cost_per_km = ?, holiday_cost_per_km = ?, warning_count = ?,
                 complaints = ?
             WHERE id = ? AND is_active = 1'
        );
        return $stmt->execute([
            $data['full_name'],
            $data['id_number'],
            $data['email'],
            $data['address'],
            $data['phone'],
            $data['card_number'],
            $data['status'],
            (float) $data['order_distance'],
            (float) $data['daily_kilometers'],
            (float) $data['weekday_cost_per_km'],
            (float) $data['holiday_cost_per_km'],
            (int) $data['warning_count'],
            $data['complaints'] !== '' ? $data['complaints'] : null,
            $id,
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM delivery_drivers WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->rowCount() > 0;
    }
}
