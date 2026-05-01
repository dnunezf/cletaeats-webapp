<?php

/**
 * Aggregation queries for the reports dashboard.
 */
class ReportsRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function customersByStatus(int $isActive): array
    {
        $stmt = $this->db->prepare(
            'SELECT id, first_name, last_name, email, phone_number, city, created_at
             FROM customers WHERE is_active = ? ORDER BY first_name, last_name'
        );
        $stmt->execute([$isActive]);
        return $stmt->fetchAll();
    }

    public function driversList(): array
    {
        $stmt = $this->db->query(
            "SELECT d.id, d.full_name, d.phone, d.status, d.warning_count, d.complaints,
                    d.order_distance, d.weekday_cost_per_km, d.holiday_cost_per_km, d.is_active,
                    COUNT(o.id) AS total_deliveries
             FROM delivery_drivers d
             LEFT JOIN orders o ON o.assigned_driver_id = d.id
             GROUP BY d.id
             ORDER BY d.full_name"
        );
        return $stmt->fetchAll();
    }

    public function restaurantPerformance(?string $from, ?string $to): array
    {
        [$where, $params] = $this->dateFilter('o.created_at', $from, $to);

        $stmt = $this->db->prepare(
            "SELECT r.id, r.name, r.food_type, r.is_active,
                    COUNT(o.id)     AS total_orders,
                    COALESCE(SUM(o.total), 0) AS total_revenue
             FROM restaurants r
             LEFT JOIN orders o ON o.restaurant_id = r.id {$where}
             GROUP BY r.id
             ORDER BY total_orders DESC, r.name"
        );
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function totalSoldOverall(?string $from, ?string $to): float
    {
        [$where, $params] = $this->dateFilter('created_at', $from, $to);
        $stmt = $this->db->prepare(
            "SELECT COALESCE(SUM(total), 0) AS grand_total FROM orders {$where}"
        );
        $stmt->execute($params);
        return (float) $stmt->fetchColumn();
    }

    public function totalOrdersCount(?string $from, ?string $to): int
    {
        [$where, $params] = $this->dateFilter('created_at', $from, $to);
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM orders {$where}");
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    public function ordersCountByStatus(?string $from, ?string $to): array
    {
        [$where, $params] = $this->dateFilter('created_at', $from, $to);
        $stmt = $this->db->prepare(
            "SELECT status, COUNT(*) AS total FROM orders {$where} GROUP BY status ORDER BY status"
        );
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function ordersByCustomer(?string $from, ?string $to): array
    {
        [$where, $params] = $this->dateFilter('o.created_at', $from, $to);
        $stmt = $this->db->prepare(
            "SELECT CONCAT(c.first_name, ' ', c.last_name) AS customer_name,
                    c.email,
                    COUNT(o.id)               AS total_orders,
                    COALESCE(SUM(o.total), 0) AS total_spent
             FROM orders o
             JOIN customers c ON c.id = o.customer_id {$where}
             GROUP BY o.customer_id
             ORDER BY total_orders DESC, customer_name"
        );
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function peakHour(?string $from, ?string $to): array
    {
        [$where, $params] = $this->dateFilter('created_at', $from, $to);
        $stmt = $this->db->prepare(
            "SELECT HOUR(created_at) AS hour_bucket, COUNT(*) AS total
             FROM orders {$where}
             GROUP BY hour_bucket
             ORDER BY total DESC, hour_bucket"
        );
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function activeCustomersCount(): int
    {
        return (int) $this->db->query('SELECT COUNT(*) FROM customers WHERE is_active = 1')->fetchColumn();
    }

    public function suspendedCustomersCount(): int
    {
        return (int) $this->db->query('SELECT COUNT(*) FROM customers WHERE is_active = 0')->fetchColumn();
    }

    public function activeDriversCount(): int
    {
        return (int) $this->db->query('SELECT COUNT(*) FROM delivery_drivers WHERE is_active = 1')->fetchColumn();
    }

    public function activeRestaurantsCount(): int
    {
        return (int) $this->db->query('SELECT COUNT(*) FROM restaurants WHERE is_active = 1')->fetchColumn();
    }

    /**
     * Builds a WHERE/AND clause for date range filtering on $column.
     * Returns ['WHERE/AND ...', params[]] or ['', []] when no valid range.
     */
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
