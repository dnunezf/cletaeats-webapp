<?php
/** @var array $invoice  Keys: order, subtotal, transport, vat, vat_rate, total_paid, transport_breakdown */
$order    = $invoice['order'];
$tb       = $invoice['transport_breakdown'];
?>
<?php $currentPage = 'orders'; ?>

<div class="page-header no-print">
    <h2 class="page-title">Invoice — Order #<?= (int) $order['id'] ?></h2>
    <div style="display:flex; gap: var(--space-sm);">
        <a href="<?= baseUrl('orders/show?id=' . (int) $order['id']) ?>" class="btn btn-ghost">
            <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/></svg>
            Back to Order
        </a>
        <button type="button" class="btn btn-outline" onclick="window.print()">
            <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M19 8H5c-1.66 0-3 1.34-3 3v6h4v4h12v-4h4v-6c0-1.66-1.34-3-3-3zm-3 11H8v-5h8v5zm3-7c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1zm-1-9H6v4h12V3z"/></svg>
            Print Invoice
        </button>
    </div>
</div>

<div class="invoice-wrapper">
    <div class="invoice-card card">

        <!-- Invoice Header -->
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
                <div class="invoice-date">
                    Issued: <?= e(date('F j, Y', strtotime($order['created_at']))) ?>
                </div>
                <?php if (!empty($order['delivered_at'])): ?>
                <div class="invoice-date">
                    Delivered: <?= e(date('F j, Y', strtotime($order['delivered_at']))) ?>
                </div>
                <?php endif; ?>
                <span class="order-status order-status-<?= e($order['status']) ?>" style="margin-top: 6px;">
                    <?= e(Order::displayStatus($order['status'])) ?>
                </span>
            </div>
        </div>

        <!-- Bill To / Restaurant -->
        <div class="invoice-parties">
            <div class="invoice-party">
                <div class="invoice-party-label">Bill To</div>
                <div class="invoice-party-name"><?= e($order['customer_name']) ?></div>
                <?php if (!empty($order['customer_email'])): ?>
                <div class="invoice-party-detail"><?= e($order['customer_email']) ?></div>
                <?php endif; ?>
                <?php if (!empty($order['customer_phone'])): ?>
                <div class="invoice-party-detail"><?= e($order['customer_phone']) ?></div>
                <?php endif; ?>
            </div>
            <div class="invoice-party">
                <div class="invoice-party-label">Restaurant</div>
                <div class="invoice-party-name"><?= e($order['restaurant_name']) ?></div>
                <div class="invoice-party-detail">
                    <span class="food-type-chip"><?= e($order['food_type']) ?></span>
                </div>
                <?php if (!empty($order['restaurant_address'])): ?>
                <div class="invoice-party-detail"><?= e($order['restaurant_address']) ?></div>
                <?php endif; ?>
            </div>
            <div class="invoice-party">
                <div class="invoice-party-label">Delivery Driver</div>
                <?php if (!empty($order['driver_name'])): ?>
                <div class="invoice-party-name"><?= e($order['driver_name']) ?></div>
                <?php if (!empty($order['driver_phone'])): ?>
                <div class="invoice-party-detail"><?= e($order['driver_phone']) ?></div>
                <?php endif; ?>
                <?php else: ?>
                <div class="invoice-party-detail" style="color:var(--color-text-secondary);">Unassigned</div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Line Items -->
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
                <tr>
                    <td class="invoice-td">
                        <div class="invoice-item-name"><?= e($order['combo_name']) ?></div>
                        <div class="invoice-item-sub"><?= e($order['restaurant_name']) ?></div>
                    </td>
                    <td class="invoice-td" style="text-align:right;">$<?= e(number_format((float) $order['combo_price'], 2)) ?></td>
                    <td class="invoice-td" style="text-align:center;"><?= (int) $order['quantity'] ?></td>
                    <td class="invoice-td" style="text-align:right; font-weight:600;">$<?= e(number_format($invoice['subtotal'], 2)) ?></td>
                </tr>
            </tbody>
        </table>

        <!-- Totals Breakdown -->
        <div class="invoice-totals">
            <div class="invoice-totals-inner">
                <div class="invoice-totals-row">
                    <span>Subtotal</span>
                    <span>$<?= e(number_format($invoice['subtotal'], 2)) ?></span>
                </div>
                <div class="invoice-totals-row">
                    <span>
                        Transport cost
                        <?php if ($tb['has_driver']): ?>
                        <span class="invoice-totals-note">
                            (<?= e(number_format($tb['distance'], 2)) ?> km × $<?= e(number_format($tb['rate'], 2)) ?>/km
                            <?= $tb['is_weekend'] ? '· weekend rate' : '· weekday rate' ?>)
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

        <!-- Notes -->
        <?php if (!empty($order['notes'])): ?>
        <div class="invoice-notes">
            <div class="invoice-notes-label">Notes</div>
            <div class="invoice-notes-body"><?= e($order['notes']) ?></div>
        </div>
        <?php endif; ?>

        <div class="invoice-footer no-print">
            <p>Thank you for choosing <?= e(APP_NAME) ?>!</p>
        </div>
    </div>
</div>
