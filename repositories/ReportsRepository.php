<?php

/**
 * Aggregation queries against the new schema.
 */
class ReportsRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function customersByStatus(string $status): array
    {
        $stmt = $this->db->prepare(
            'SELECT c.user_id AS id, u.username, u.email, u.document, u.status, l.city
             FROM customers c
             JOIN users u ON u.id = c.user_id
             LEFT JOIN locations l ON l.id = u.location_id
             WHERE u.status = ?
             ORDER BY u.username'
        );
        $stmt->execute([$status]);
        return $stmt->fetchAll();
    }

    public function driversList(): array
    {
        $stmt = $this->db->query(
            "SELECT d.user_id AS id, u.username AS full_name, u.email, u.status AS user_status,
                    d.status, d.penalties AS warning_count,
                    d.km_cost_regular, d.km_cost_holidays,
                    (SELECT COUNT(*) FROM orders o WHERE o.driver_id = d.user_id) AS total_deliveries,
                    (SELECT COUNT(*) FROM complaints cmp
                       JOIN orders o ON o.id = cmp.order_id
                       WHERE o.driver_id = d.user_id) AS complaint_count,
                    (SELECT AVG(cmp.rating) FROM complaints cmp
                       JOIN orders o ON o.id = cmp.order_id
                       WHERE o.driver_id = d.user_id) AS avg_rating
             FROM drivers d
             JOIN users u ON u.id = d.user_id
             ORDER BY u.username"
        );
        return $stmt->fetchAll();
    }

    public function restaurantPerformance(?string $from, ?string $to): array
    {
        [$where, $params] = $this->dateFilter('o.creation_date', $from, $to);

        $sql = "SELECT r.user_id AS id, u.username AS name, r.category AS food_type, u.status,
                       COUNT(DISTINCT o.id) AS total_orders,
                       COALESCE(SUM(il.quantity * il.combo_price), 0) AS total_revenue
                FROM restaurants r
                JOIN users u ON u.id = r.user_id
                LEFT JOIN combos cb ON cb.restaurant_id = r.user_id
                LEFT JOIN invoice_lines il ON il.combo_id = cb.id
                LEFT JOIN orders o ON o.id = il.order_id {$where}
                GROUP BY r.user_id
                ORDER BY total_orders DESC, u.username";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function totalSoldOverall(?string $from, ?string $to): float
    {
        [$where, $params] = $this->dateFilter('o.creation_date', $from, $to);
        $sql = "SELECT COALESCE(SUM(il.quantity * il.combo_price), 0) AS grand_total
                FROM invoice_lines il
                JOIN orders o ON o.id = il.order_id {$where}";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (float) $stmt->fetchColumn();
    }

    public function totalOrdersCount(?string $from, ?string $to): int
    {
        [$where, $params] = $this->dateFilter('creation_date', $from, $to);
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM orders {$where}");
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    public function ordersCountByStatus(?string $from, ?string $to): array
    {
        [$where, $params] = $this->dateFilter('creation_date', $from, $to);
        $stmt = $this->db->prepare(
            "SELECT status, COUNT(*) AS total FROM orders {$where} GROUP BY status ORDER BY status"
        );
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function ordersByCustomer(?string $from, ?string $to): array
    {
        [$where, $params] = $this->dateFilter('o.creation_date', $from, $to);
        $stmt = $this->db->prepare(
            "SELECT u.username AS customer_name, u.email,
                    COUNT(DISTINCT o.id) AS total_orders,
                    COALESCE(SUM(il.quantity * il.combo_price), 0) AS total_spent
             FROM orders o
             JOIN customers c  ON c.user_id = o.customer_id
             JOIN users u      ON u.id = c.user_id
             LEFT JOIN invoice_lines il ON il.order_id = o.id
             {$where}
             GROUP BY o.customer_id
             ORDER BY total_orders DESC, customer_name"
        );
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function peakHour(?string $from, ?string $to): array
    {
        [$where, $params] = $this->dateFilter('creation_date', $from, $to);
        $stmt = $this->db->prepare(
            "SELECT HOUR(creation_date) AS hour_bucket, COUNT(*) AS total
             FROM orders {$where}
             GROUP BY hour_bucket
             ORDER BY total DESC, hour_bucket"
        );
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function activeCustomersCount(): int
    {
        return (int) $this->db->query(
            "SELECT COUNT(*) FROM customers c JOIN users u ON u.id = c.user_id WHERE u.status = 'active'"
        )->fetchColumn();
    }

    public function suspendedCustomersCount(): int
    {
        return (int) $this->db->query(
            "SELECT COUNT(*) FROM customers c JOIN users u ON u.id = c.user_id WHERE u.status = 'inactive'"
        )->fetchColumn();
    }

    public function activeDriversCount(): int
    {
        return (int) $this->db->query(
            "SELECT COUNT(*) FROM drivers d JOIN users u ON u.id = d.user_id WHERE u.status = 'active'"
        )->fetchColumn();
    }

    public function activeRestaurantsCount(): int
    {
        return (int) $this->db->query(
            "SELECT COUNT(*) FROM restaurants r JOIN users u ON u.id = r.user_id WHERE u.status = 'active'"
        )->fetchColumn();
    }

    private function dateFilter(string $column, ?string $from, ?string $to): array
    {
        if (!$from || !$to) {
            return ['', []];
        }
        $f = DateTimeImmutable::createFromFormat('Y-m-d', $from);
        $t = DateTimeImmutable::createFromFormat('Y-m-d', $to);
        if (!$f || !$t || $f > $t) {
            return ['', []];
        }
        return [
            "WHERE {$column} BETWEEN ? AND ?",
            [$from . ' 00:00:00', $to . ' 23:59:59'],
        ];
    }
}
