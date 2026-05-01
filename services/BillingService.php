<?php

/**
 * Computes invoice breakdown for an existing order.
 * All values are derived on-demand — no billing columns are stored.
 */
class BillingService
{
    private const VAT_RATE = 0.13;

    private OrderService $orderService;

    public function __construct()
    {
        $this->orderService = new OrderService();
    }

    public function getInvoiceByOrderId(int $id): ?array
    {
        $order = $this->orderService->getById($id);
        if (!$order) {
            return null;
        }
        return $this->buildInvoice($order);
    }

    public function buildInvoice(array $order): array
    {
        $subtotal  = round((float) $order['total'], 2);
        $transport = $this->computeTransport($order);
        $vat       = round(($subtotal + $transport) * self::VAT_RATE, 2);
        $totalPaid = round($subtotal + $transport + $vat, 2);

        return [
            'order'       => $order,
            'subtotal'    => $subtotal,
            'transport'   => $transport,
            'vat'         => $vat,
            'vat_rate'    => self::VAT_RATE,
            'total_paid'  => $totalPaid,
            'transport_breakdown' => $this->transportBreakdown($order),
        ];
    }

    private function computeTransport(array $order): float
    {
        if (empty($order['assigned_driver_id'])) {
            return 0.00;
        }

        $distance = (float) ($order['driver_order_distance'] ?? 0);
        $rate     = $this->selectRate($order);

        return round($distance * $rate, 2);
    }

    private function selectRate(array $order): float
    {
        $dayOfWeek = (int) date('N', strtotime($order['created_at']));
        $isWeekend = $dayOfWeek >= 6;

        return $isWeekend
            ? (float) ($order['driver_holiday_cost_per_km'] ?? 0)
            : (float) ($order['driver_weekday_cost_per_km'] ?? 0);
    }

    private function transportBreakdown(array $order): array
    {
        if (empty($order['assigned_driver_id'])) {
            return ['distance' => 0, 'rate' => 0, 'is_weekend' => false, 'has_driver' => false];
        }

        $dayOfWeek = (int) date('N', strtotime($order['created_at']));
        $isWeekend = $dayOfWeek >= 6;

        return [
            'has_driver' => true,
            'distance'   => (float) ($order['driver_order_distance'] ?? 0),
            'rate'       => $this->selectRate($order),
            'is_weekend' => $isWeekend,
        ];
    }
}
