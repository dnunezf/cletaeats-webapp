<?php

/**
 * combos via stored procedures + joined reads.
 */
class ComboRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare('CALL sp_read_combo(?)');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        $stmt->closeCursor();
        return $row ?: null;
    }

    public function findAll(): array
    {
        $stmt = $this->db->query(
            'SELECT c.*, u.username AS restaurant_name
             FROM combos c
             JOIN users u ON u.id = c.restaurant_id
             ORDER BY u.username ASC, c.name ASC'
        );
        return $stmt->fetchAll();
    }

    public function findAllByRestaurant(int $restaurantId): array
    {
        $stmt = $this->db->prepare('SELECT * FROM combos WHERE restaurant_id = ? ORDER BY name ASC');
        $stmt->execute([$restaurantId]);
        return $stmt->fetchAll();
    }

    public function search(string $term): array
    {
        $like = '%' . $term . '%';
        $stmt = $this->db->prepare(
            'SELECT c.*, u.username AS restaurant_name
             FROM combos c
             JOIN users u ON u.id = c.restaurant_id
             WHERE c.name LIKE ? OR c.description LIKE ? OR u.username LIKE ?
             ORDER BY u.username ASC, c.name ASC'
        );
        $stmt->execute([$like, $like, $like]);
        return $stmt->fetchAll();
    }

    public function create(int $restaurantId, string $name, string $description, float $price): int
    {
        $stmt = $this->db->prepare('CALL sp_create_combo(?, ?, ?, ?)');
        $stmt->execute([$restaurantId, $name, $description, $price]);
        $row = $stmt->fetch();
        $stmt->closeCursor();
        return (int) ($row['id'] ?? 0);
    }

    public function update(int $id, string $name, string $description, float $price): bool
    {
        $stmt = $this->db->prepare('CALL sp_update_combo(?, ?, ?, ?)');
        $stmt->execute([$id, $name, $description, $price]);
        $row = $stmt->fetch();
        $stmt->closeCursor();
        return (int) ($row['rows_affected'] ?? 0) > 0;
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('CALL sp_delete_combo(?)');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        $stmt->closeCursor();
        return (int) ($row['rows_affected'] ?? 0) > 0;
    }
}
