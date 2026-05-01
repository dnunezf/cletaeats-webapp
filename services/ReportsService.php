<?php

/**
 * Orchestrates all report queries and returns a structured dashboard array.
 */
class ReportsService
{
    private ReportsRepository $repo;

    public function __construct()
    {
        $this->repo = new ReportsRepository();
    }

    public function buildDashboard(?string $from, ?string $to): array
    {
        $restaurants     = $this->repo->restaurantPerformance($from, $to);
        $ordersByStatus  = $this->repo->ordersCountByStatus($from, $to);
        $peakHour        = $this->repo->peakHour($from, $to);
        $drivers         = $this->repo->driversList();

        return [
            'kpi' => [
                'total_sold'          => $this->repo->totalSoldOverall($from, $to),
                'total_orders'        => $this->repo->totalOrdersCount($from, $to),
                'active_customers'    => $this->repo->activeCustomersCount(),
                'suspended_customers' => $this->repo->suspendedCustomersCount(),
                'active_drivers'      => $this->repo->activeDriversCount(),
                'active_restaurants'  => $this->repo->activeRestaurantsCount(),
                'peak_hour'           => $peakHour[0] ?? null,
            ],
            'restaurants'         => $restaurants,
            'top_restaurant'      => $this->findTop($restaurants),
            'bottom_restaurant'   => $this->findBottom($restaurants),
            'orders_by_status'    => $ordersByStatus,
            'peak_hours'          => $peakHour,
            'orders_by_customer'  => $this->repo->ordersByCustomer($from, $to),
            'active_customers'    => $this->repo->customersByStatus(1),
            'suspended_customers' => $this->repo->customersByStatus(0),
            'drivers'             => $this->enrichDrivers($drivers),
        ];
    }

    private function findTop(array $restaurants): ?array
    {
        foreach ($restaurants as $r) {
            if ((int) $r['total_orders'] > 0) {
                return $r;
            }
        }
        return null;
    }

    private function findBottom(array $restaurants): ?array
    {
        $withOrders = array_filter($restaurants, fn($r) => (int) $r['total_orders'] > 0);
        if (count($withOrders) < 2) {
            return null;
        }
        return end($withOrders) ?: null;
    }

    /**
     * Parses the TEXT complaints column into a per-driver count.
     */
    private function enrichDrivers(array $drivers): array
    {
        foreach ($drivers as &$d) {
            $text  = trim($d['complaints'] ?? '');
            $lines = $text !== '' ? array_filter(array_map('trim', explode("\n", $text))) : [];
            $d['complaint_count'] = count($lines);
        }
        unset($d);
        return $drivers;
    }
}
