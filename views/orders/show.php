<?php
/** @var array       $order      Order row joined with customer/driver/restaurant, with 'items' list */
/** @var bool        $isAdmin    True when admin */
/** @var array|null  $complaint  Complaint row if filed */
?>
<?php $currentPage = 'orders'; ?>
<?php $transitions = Order::transitions()[$order['status']] ?? []; ?>
<?php $items = $order['items'] ?? []; ?>

<div class="page-header">
    <h2 class="page-title">Order #<?= (int) $order['id'] ?></h2>
    <div style="display:flex; gap: var(--space-sm);">
        <a href="<?= baseUrl('orders') ?>" class="btn btn-ghost">Back to Orders</a>
        <?php if (userIsAnyOf(['admin', 'customer'])): ?>
            <a href="<?= baseUrl('billing/show?id=' . (int) $order['id']) ?>" class="btn btn-outline">View Invoice</a>
        <?php endif; ?>
        <?php if (userIsAnyOf(['admin', 'customer']) && $order['status'] === 'delivered' && empty($complaint)): ?>
            <a href="<?= baseUrl('complaints/create?order_id=' . (int) $order['id']) ?>" class="btn btn-outline">File Complaint</a>
        <?php endif; ?>
    </div>
</div>

<div class="order-show-layout">
    <div class="card order-detail-card">
        <div class="order-detail-header">
            <div>
                <div class="order-detail-id">Order #<?= (int) $order['id'] ?></div>
                <div class="order-detail-date"><?= e(date('F j, Y \a\t g:i A', strtotime($order['creation_date']))) ?></div>
                <?php if (!empty($order['delivered_date'])): ?>
                <div class="order-detail-delivered-at">
                    Delivered <?= e(date('F j, Y', strtotime($order['delivered_date']))) ?>
                </div>
                <?php endif; ?>
            </div>
            <span class="order-status order-status-<?= e($order['status']) ?>" id="orderStatusBadge">
                <?= e(Order::displayStatus($order['status'])) ?>
            </span>
        </div>

        <div class="order-detail-grid">
            <div class="order-detail-section">
                <div class="order-detail-section-title">Customer</div>
                <div class="order-detail-field">
                    <div class="order-detail-label">Name</div>
                    <div class="order-detail-value"><strong><?= e($order['customer_name']) ?></strong></div>
                </div>
                <?php if (!empty($order['customer_email'])): ?>
                <div class="order-detail-field">
                    <div class="order-detail-label">Email</div>
                    <div class="order-detail-value"><?= e($order['customer_email']) ?></div>
                </div>
                <?php endif; ?>
                <div class="order-detail-field">
                    <div class="order-detail-label">Card</div>
                    <div class="order-detail-value"><?= e($order['costumer_card_number'] ?? '') ?></div>
                </div>
            </div>

            <div class="order-detail-section">
                <div class="order-detail-section-title">Restaurant</div>
                <div class="order-detail-field">
                    <div class="order-detail-label">Name</div>
                    <div class="order-detail-value"><strong><?= e($order['restaurant_name'] ?? '—') ?></strong></div>
                </div>
                <?php if (!empty($order['category'])): ?>
                <div class="order-detail-field">
                    <div class="order-detail-label">Category</div>
                    <div class="order-detail-value">
                        <span class="food-type-chip"><?= e(ucfirst($order['category'])) ?></span>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="order-detail-section order-driver-section">
            <div class="order-detail-section-title">Delivery Driver</div>
            <div class="order-driver-card">
                <div class="order-driver-avatar">
                    <svg viewBox="0 0 24 24" width="24" height="24" fill="currentColor"><path d="M18 18.5c.83 0 1.5-.67 1.5-1.5s-.67-1.5-1.5-1.5-1.5.67-1.5 1.5.67 1.5 1.5 1.5z"/></svg>
                </div>
                <div class="order-driver-info">
                    <div class="order-driver-name"><?= e($order['driver_name']) ?></div>
                    <?php if (!empty($order['driver_email'])): ?>
                    <div class="order-driver-phone"><?= e($order['driver_email']) ?></div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="order-detail-section" style="margin-top: var(--space-lg);">
            <div class="order-detail-section-title">Order Items</div>
            <?php foreach ($items as $line): ?>
                <div class="order-detail-combo-row">
                    <div>
                        <div class="order-detail-combo-name"><?= e($line['combo_name']) ?></div>
                        <div class="order-detail-combo-unit">$<?= e(number_format((float) $line['combo_price'], 2)) ?> × <?= (int) $line['quantity'] ?></div>
                    </div>
                    <div class="order-detail-combo-total">$<?= e(number_format((float) $line['combo_price'] * (int) $line['quantity'], 2)) ?></div>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if (!empty($complaint)): ?>
        <div class="order-detail-section" style="margin-top: var(--space-lg);">
            <div class="order-detail-section-title">Complaint</div>
            <div class="order-detail-notes"><?= e($complaint['content']) ?></div>
            <div style="margin-top: var(--space-sm);">Rating: <strong><?= (int) $complaint['rating'] ?>/5</strong></div>
            <?php if ($isAdmin): ?>
            <form action="<?= baseUrl('complaints/delete') ?>" method="POST" style="margin-top: var(--space-sm);">
                <?= csrfField() ?>
                <input type="hidden" name="order_id" value="<?= (int) $order['id'] ?>">
                <button type="submit" class="btn btn-ghost btn-sm">Remove Complaint</button>
            </form>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <div class="order-detail-footer">
            <div class="order-detail-total-label">Order Total</div>
            <div class="order-detail-total-amount">$<?= e(number_format((float) $order['total'], 2)) ?></div>
        </div>

        <?php if (userIsAnyOf(['admin', 'driver']) && !empty($transitions)): ?>
        <div class="order-status-controls">
            <div class="order-status-controls-title">Update Status</div>
            <form id="orderStatusForm" action="<?= baseUrl('orders/update-status') ?>" method="POST">
                <?= csrfField() ?>
                <input type="hidden" name="id" value="<?= (int) $order['id'] ?>">
                <input type="hidden" name="status" id="statusInput" value="">
                <div class="order-status-buttons">
                    <?php foreach ($transitions as $nextStatus): ?>
                    <button type="button" class="btn btn-outline status-action-btn" data-status="<?= e($nextStatus) ?>">
                        <?= e(Order::displayStatus($nextStatus)) ?>
                    </button>
                    <?php endforeach; ?>
                </div>
            </form>
        </div>
        <?php endif; ?>

        <?php if ($isAdmin): ?>
        <div style="border-top: 1px solid var(--color-border); padding-top: var(--space-md); margin-top: var(--space-md); display: flex; justify-content: flex-end;">
            <button type="button" class="btn btn-danger delete-btn"
                    data-id="<?= (int) $order['id'] ?>"
                    data-label="Order #<?= (int) $order['id'] ?> for <?= e($order['customer_name']) ?>">
                Delete Order
            </button>
        </div>

        <div class="modal-overlay" id="orderDeleteModal">
            <div class="modal">
                <h3 class="modal-title">Confirm Deletion</h3>
                <p class="modal-body">Are you sure you want to delete <strong id="deleteOrderLabel"></strong>?</p>
                <form id="orderDeleteForm" action="<?= baseUrl('orders/delete') ?>" method="POST">
                    <?= csrfField() ?>
                    <input type="hidden" name="id" id="deleteOrderId" value="">
                    <div class="modal-actions">
                        <button type="button" class="btn btn-ghost" id="cancelOrderDeleteBtn">Cancel</button>
                        <button type="submit" class="btn btn-danger" id="confirmOrderDeleteBtn">Delete</button>
                    </div>
                </form>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<template id="deliveredAtTemplate">
    <div class="order-detail-delivered-at" id="deliveredAtRow"></div>
</template>
