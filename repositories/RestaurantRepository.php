<?php

/**
 * SQL queries for the restaurants table.
 */
class RestaurantRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function findAllActive(): array
    {
        $stmt = $this->db->query(
            'SELECT * FROM restaurants WHERE is_active = 1 ORDER BY name ASC'
        );
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM restaurants WHERE id = ? AND is_active = 1 LIMIT 1');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function findByLegalId(string $legalId): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM restaurants WHERE legal_id = ? AND is_active = 1 LIMIT 1');
        $stmt->execute([$legalId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function findByLegalIdExcluding(string $legalId, int $excludeId): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM restaurants WHERE legal_id = ? AND id != ? AND is_active = 1 LIMIT 1'
        );
        $stmt->execute([$legalId, $excludeId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function search(string $term): array
    {
        $like = '%' . $term . '%';
        $stmt = $this->db->prepare(
            "SELECT * FROM restaurants
             WHERE is_active = 1
               AND (name LIKE ? OR legal_id LIKE ? OR food_type LIKE ? OR address LIKE ? OR combo_name LIKE ?)
             ORDER BY name ASC"
        );
        $stmt->execute([$like, $like, $like, $like, $like]);
        return $stmt->fetchAll();
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO restaurants
             (name, legal_id, address, food_type, combo_name, combo_description, combo_price)
             VALUES (?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $data['name'],
            $data['legal_id'],
            $data['address'],
            $data['food_type'],
            $data['combo_name'],
            $data['combo_description'] ?: null,
            (float) $data['combo_price'],
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE restaurants
             SET name = ?, legal_id = ?, address = ?, food_type = ?,
                 combo_name = ?, combo_description = ?, combo_price = ?
             WHERE id = ? AND is_active = 1'
        );
        return $stmt->execute([
            $data['name'],
            $data['legal_id'],
            $data['address'],
            $data['food_type'],
            $data['combo_name'],
            $data['combo_description'] ?: null,
            (float) $data['combo_price'],
            $id,
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM restaurants WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->rowCount() > 0;
    }
}
