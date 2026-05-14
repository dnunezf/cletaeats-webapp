<?php
/** @var array $invoice Keys: order, items, subtotal, transport, vat, vat_rate, total_paid, transport_breakdown */
$order = $invoice['order'];
$items = $invoice['items'] ?? [];
$tb    = $invoice['transport_breakdown'];
?>
<?php $currentPage = 'orders'; ?>

<div class="page-header no-print">
    <h2 class="page-title">Invoice — Order #<?= (int) $order['id'] ?></h2>
    <div style="display:flex; gap: var(--space-sm);">
        <a href="<?= baseUrl('orders/show?id=' . (int) $order['id']) ?>" class="btn btn-ghost">Back to Order</a>
        <button type="button" class="btn btn-outline" onclick="window.print()">Print Invoice</button>
    </div>
</div>

<div class="invoice-wrapper">
    <div class="invoice-card card">

        <div class="invoice-header">
            <div class="invoice-brand">
                <div class="invoice-brand-icon">
                    <svg viewBox="0 0 24 24" width="32" height="32" fill="currentColor"><path d="M11 9H9V2H7v7H5V2H3v7c0 2.12 1.66 3.84 3.75 3.97V22h2.5v-9.03C11.34 12.84 13 11.12 13 9V2h-2v7zm5-3v8h2.5v8H21V2c-2.76 0-5 2.24-5 4z"/></svg>
                </div>
                <div>
                    <div class="invoice-brand-name"><?= e(APP_NAME) ?></div>
                    <div class="invoice-brand-tagline">Food Delivery Service</div>
                </div>
            </div>
            <div class="invoice-meta">
                <div class="invoice-number">Invoice #<?= (int) $order['id'] ?></div>
                <div class="invoice-date">Issued: <?= e(date('F j, Y', strtotime($order['creation_date']))) ?></div>
                <?php if (!empty($order['delivered_date'])): ?>
                <div class="invoice-date">Delivered: <?= e(date('F j, Y', strtotime($order['delivered_date']))) ?></div>
                <?php endif; ?>
                <span class="order-status order-status-<?= e($order['status']) ?>" style="margin-top: 6px;">
                    <?= e(Order::displayStatus($order['status'])) ?>
                </span>
            </div>
        </div>

        <div class="invoice-parties">
            <div class="invoice-party">
                <div class="invoice-party-label">Bill To</div>
                <div class="invoice-party-name"><?= e($order['customer_name']) ?></div>
                <?php if (!empty($order['customer_email'])): ?>
                <div class="invoice-party-detail"><?= e($order['customer_email']) ?></div>
                <?php endif; ?>
                <div class="invoice-party-detail">Card: <?= e($order['costumer_card_number'] ?? '') ?></div>
            </div>
            <div class="invoice-party">
                <div class="invoice-party-label">Restaurant</div>
                <div class="invoice-party-name"><?= e($order['restaurant_name'] ?? '—') ?></div>
                <?php if (!empty($order['category'])): ?>
                <div class="invoice-party-detail">
                    <span class="food-type-chip"><?= e(ucfirst($order['category'])) ?></span>
                </div>
                <?php endif; ?>
            </div>
            <div class="invoice-party">
                <div class="invoice-party-label">Delivery Driver</div>
                <div class="invoice-party-name"><?= e($order['driver_name']) ?></div>
                <?php if (!empty($order['driver_email'])): ?>
                <div class="invoice-party-detail"><?= e($order['driver_email']) ?></div>
                <?php endif; ?>
            </div>
        </div>

        <table class="invoice-items-table">
            <thead>
                <tr>
                    <th class="invoice-th">Description</th>
                    <th class="invoice-th" style="text-align:right;">Unit Price</th>
                    <th class="invoice-th" style="text-align:center;">Qty</th>
                    <th class="invoice-th" style="text-align:right;">Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $line): ?>
                <tr>
                    <td class="invoice-td">
                        <div class="invoice-item-name"><?= e($line['combo_name']) ?></div>
                        <?php if (!empty($line['combo_description'])): ?>
                        <div class="invoice-item-sub"><?= e($line['combo_description']) ?></div>
                        <?php endif; ?>
                    </td>
                    <td class="invoice-td" style="text-align:right;">$<?= e(number_format((float) $line['combo_price'], 2)) ?></td>
                    <td class="invoice-td" style="text-align:center;"><?= (int) $line['quantity'] ?></td>
                    <td class="invoice-td" style="text-align:right; font-weight:600;">
                        $<?= e(number_format((float) $line['combo_price'] * (int) $line['quantity'], 2)) ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="invoice-totals">
            <div class="invoice-totals-inner">
                <div class="invoice-totals-row">
                    <span>Subtotal</span>
                    <span>$<?= e(number_format($invoice['subtotal'], 2)) ?></span>
                </div>
                <div class="invoice-totals-row">
                    <span>
                        Delivery Fee
                        <?php if ($tb['has_driver']): ?>
                        <span class="invoice-totals-note">
                            (<?= $tb['is_weekend'] ? 'holiday rate' : 'regular rate' ?>:
                            $<?= e(number_format($tb['rate'], 2)) ?>)
                        </span>
                        <?php endif; ?>
                    </span>
                    <span>$<?= e(number_format($invoice['transport'], 2)) ?></span>
                </div>
                <div class="invoice-totals-row">
                    <span>VAT (<?= (int) ($invoice['vat_rate'] * 100) ?>%)</span>
                    <span>$<?= e(number_format($invoice['vat'], 2)) ?></span>
                </div>
                <div class="invoice-totals-total">
                    <span>Total Paid</span>
                    <span>$<?= e(number_format($invoice['total_paid'], 2)) ?></span>
                </div>
            </div>
        </div>

        <div class="invoice-footer no-print">
            <p>Thank you for choosing <?= e(APP_NAME) ?>!</p>
        </div>
    </div>
</div>
