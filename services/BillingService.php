<?php

/**
 * Computes invoice breakdown for an existing order.
 * Subtotal = SUM(quantity × combo_price) from invoice_lines.
 * Delivery fee = (weekend ? km_cost_holidays : km_cost_regular)  -- flat fee, since the new schema removed `order_distance`.
 * VAT = 13% of (subtotal + delivery fee).
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
        $items = $order['items'] ?? [];
        $subtotal = 0.0;
        foreach ($items as $line) {
            $subtotal += (float) $line['combo_price'] * (int) $line['quantity'];
        }
        $subtotal  = round($subtotal, 2);
        $delivery  = $this->computeDeliveryFee($order);
        $vat       = round(($subtotal + $delivery) * self::VAT_RATE, 2);
        $totalPaid = round($subtotal + $delivery + $vat, 2);

        return [
            'order'      => $order,
            'items'      => $items,
            'subtotal'   => $subtotal,
            'transport'  => $delivery,
            'vat'        => $vat,
            'vat_rate'   => self::VAT_RATE,
            'total_paid' => $totalPaid,
            'transport_breakdown' => $this->deliveryBreakdown($order, $delivery),
        ];
    }

    private function computeDeliveryFee(array $order): float
    {
        $isWeekend = $this->isWeekend($order['creation_date'] ?? null);
        $rate = $isWeekend
            ? (float) ($order['km_cost_holidays'] ?? 0)
            : (float) ($order['km_cost_regular'] ?? 0);
        return round($rate, 2);
    }

    private function deliveryBreakdown(array $order, float $delivery): array
    {
        $isWeekend = $this->isWeekend($order['creation_date'] ?? null);
        return [
            'has_driver' => !empty($order['driver_id']),
            'rate'       => $isWeekend
                ? (float) ($order['km_cost_holidays'] ?? 0)
                : (float) ($order['km_cost_regular'] ?? 0),
            'is_weekend' => $isWeekend,
            'fee'        => $delivery,
        ];
    }

    private function isWeekend(?string $datetime): bool
    {
        if (!$datetime) {
            return false;
        }
        $dayOfWeek = (int) date('N', strtotime($datetime));
        return $dayOfWeek >= 6;
    }
}
