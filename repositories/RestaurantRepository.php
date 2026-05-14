<?php

/**
 * restaurants via stored procedures + joined reads.
 */
class RestaurantRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    private function joinedSelect(): string
    {
        return 'SELECT r.user_id, r.category,
                       u.username, u.email, u.document, u.status, u.role, u.location_id,
                       l.address, l.city, l.postal_code
                FROM restaurants r
                JOIN users u ON u.id = r.user_id
                LEFT JOIN locations l ON l.id = u.location_id';
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
        $stmt = $this->db->prepare($this->joinedSelect() . ' WHERE r.user_id = ? LIMIT 1');
        $stmt->execute([$userId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function search(string $term): array
    {
        $like = '%' . $term . '%';
        $stmt = $this->db->prepare(
            $this->joinedSelect() .
            ' WHERE u.username LIKE ? OR u.email LIKE ? OR u.document LIKE ? OR r.category LIKE ? OR l.city LIKE ?
              ORDER BY u.username ASC'
        );
        $stmt->execute([$like, $like, $like, $like, $like]);
        return $stmt->fetchAll();
    }

    public function create(int $userId, string $category): int
    {
        $stmt = $this->db->prepare('CALL sp_create_restaurant(?, ?)');
        $stmt->execute([$userId, $category]);
        $stmt->closeCursor();
        return $userId;
    }

    public function update(int $userId, string $category): bool
    {
        $stmt = $this->db->prepare('CALL sp_update_restaurant(?, ?)');
        $stmt->execute([$userId, $category]);
        $row = $stmt->fetch();
        $stmt->closeCursor();
        return (int) ($row['rows_affected'] ?? 0) > 0;
    }

    public function delete(int $userId): bool
    {
        $stmt = $this->db->prepare('CALL sp_delete_restaurant(?)');
        $stmt->execute([$userId]);
        $row = $stmt->fetch();
        $stmt->closeCursor();
        return (int) ($row['rows_affected'] ?? 0) > 0;
    }
}
