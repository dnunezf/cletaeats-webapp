<?php

/**
 * Locations via stored procedures.
 */
class LocationRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare('CALL sp_read_location(?)');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        $stmt->closeCursor();
        return $row ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare('CALL sp_create_location(?, ?, ?)');
        $stmt->execute([
            $data['address'],
            $data['city'],
            $data['postal_code'],
        ]);
        $row = $stmt->fetch();
        $stmt->closeCursor();
        return (int) ($row['id'] ?? 0);
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare('CALL sp_update_location(?, ?, ?, ?)');
        $stmt->execute([
            $id,
            $data['address'],
            $data['city'],
            $data['postal_code'],
        ]);
        $row = $stmt->fetch();
        $stmt->closeCursor();
        return (int) ($row['rows_affected'] ?? 0) > 0;
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('CALL sp_delete_location(?)');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        $stmt->closeCursor();
        return (int) ($row['rows_affected'] ?? 0) > 0;
    }
}
