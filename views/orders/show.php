<?php
/** @var array $order    Order row joined with customer, restaurant, driver */
/** @var bool  $isAdmin  True when the authenticated user is an admin */
?>
<?php $currentPage = 'orders'; ?>
<?php $transitions = Order::transitions()[$order['status']] ?? []; ?>

<div class="page-header">
    <h2 class="page-title">Order #<?= (int) $order['id'] ?></h2>
    <a href="<?= baseUrl('orders') ?>" class="btn btn-ghost">
        <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/></svg>
        Back to Orders
    </a>
</div>

<div class="order-show-layout">
    <div class="card order-detail-card">
        <div class="order-detail-header">
            <div>
                <div class="order-detail-id">Order #<?= (int) $order['id'] ?></div>
                <div class="order-detail-date"><?= e(date('F j, Y \a\t g:i A', strtotime($order['created_at']))) ?></div>
                <?php if (!empty($order['delivered_at'])): ?>
                <div class="order-detail-delivered-at">
                    Delivered <?= e(date('F j, Y \a\t g:i A', strtotime($order['delivered_at']))) ?>
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
                <?php if (!empty($order['customer_phone'])): ?>
                <div class="order-detail-field">
                    <div class="order-detail-label">Phone</div>
                    <div class="order-detail-value"><?= e($order['customer_phone']) ?></div>
                </div>
                <?php endif; ?>
            </div>

            <div class="order-detail-section">
                <div class="order-detail-section-title">Restaurant</div>
                <div class="order-detail-field">
                    <div class="order-detail-label">Name</div>
                    <div class="order-detail-value"><strong><?= e($order['restaurant_name']) ?></strong></div>
                </div>
                <div class="order-detail-field">
                    <div class="order-detail-label">Food Type</div>
                    <div class="order-detail-value">
                        <span class="food-type-chip"><?= e($order['food_type']) ?></span>
                    </div>
                </div>
                <?php if (!empty($order['restaurant_address'])): ?>
                <div class="order-detail-field">
                    <div class="order-detail-label">Address</div>
                    <div class="order-detail-value"><?= e($order['restaurant_address']) ?></div>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Assigned Driver -->
        <div class="order-detail-section order-driver-section">
            <div class="order-detail-section-title">Delivery Driver</div>
            <?php if (!empty($order['driver_name'])): ?>
            <div class="order-driver-card">
                <div class="order-driver-avatar">
                    <svg viewBox="0 0 24 24" width="24" height="24" fill="currentColor"><path d="M18 18.5c.83 0 1.5-.67 1.5-1.5s-.67-1.5-1.5-1.5-1.5.67-1.5 1.5.67 1.5 1.5 1.5zM19.5 9.5h-1.84l-1.48-4.45C15.92 4.42 15.33 4 14.66 4H12v2h2.65l1.67 5H5.5c-1.38 0-2.5 1.12-2.5 2.5v3.5h2c0 1.66 1.34 3 3 3s3-1.34 3-3h4c0 1.66 1.34 3 3 3s3-1.34 3-3h2V14c0-2.76-2.24-4.5-4.5-4.5zM8 18.5c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5z"/></svg>
                </div>
                <div class="order-driver-info">
                    <div class="order-driver-name"><?= e($order['driver_name']) ?></div>
                    <?php if (!empty($order['driver_phone'])): ?>
                    <div class="order-driver-phone"><?= e($order['driver_phone']) ?></div>
                    <?php endif; ?>
                </div>
            </div>
            <?php else: ?>
            <div class="order-detail-value" style="color: var(--color-text-secondary);">No driver assigned.</div>
            <?php endif; ?>
        </div>

        <div class="order-detail-section" style="margin-top: var(--space-lg);">
            <div class="order-detail-section-title">Order Items</div>
            <div class="order-detail-combo-row">
                <div>
                    <div class="order-detail-combo-name"><?= e($order['combo_name']) ?></div>
                    <div class="order-detail-combo-unit">$<?= e(number_format((float) $order['combo_price'], 2)) ?> × <?= (int) $order['quantity'] ?></div>
                </div>
                <div class="order-detail-combo-total">$<?= e(number_format((float) $order['total'], 2)) ?></div>
            </div>
        </div>

        <?php if (!empty($order['notes'])): ?>
        <div class="order-detail-section" style="margin-top: var(--space-lg);">
            <div class="order-detail-section-title">Notes</div>
            <div class="order-detail-notes"><?= e($order['notes']) ?></div>
        </div>
        <?php endif; ?>

        <div class="order-detail-footer">
            <div class="order-detail-total-label">Order Total</div>
            <div class="order-detail-total-amount">$<?= e(number_format((float) $order['total'], 2)) ?></div>
        </div>

        <?php if ($isAdmin && !empty($transitions)): ?>
        <!-- Status Control Panel -->
        <div class="order-status-controls">
            <div class="order-status-controls-title">Update Status</div>
            <form id="orderStatusForm" action="<?= baseUrl('orders/update-status') ?>" method="POST">
                <?= csrfField() ?>
                <input type="hidden" name="id" value="<?= (int) $order['id'] ?>">
                <input type="hidden" name="status" id="statusInput" value="">
                <div class="order-status-buttons">
                    <?php foreach ($transitions as $nextStatus): ?>
                    <button type="button"
                            class="btn btn-outline status-action-btn"
                            data-status="<?= e($nextStatus) ?>">
                        <?= e(Order::displayStatus($nextStatus)) ?>
                    </button>
                    <?php endforeach; ?>
                </div>
            </form>
        </div>
        <?php endif; ?>

        <?php if ($isAdmin): ?>
        <div style="border-top: 1px solid var(--color-border); padding-top: var(--space-md); margin-top: var(--space-md); display: flex; justify-content: flex-end;">
            <button type="button"
                    class="btn btn-danger delete-btn"
                    data-id="<?= (int) $order['id'] ?>"
                    data-label="Order #<?= (int) $order['id'] ?> for <?= e($order['customer_name']) ?>">
                <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/></svg>
                Delete Order
            </button>
        </div>

        <!-- Delete Confirmation Modal -->
        <div class="modal-overlay" id="orderDeleteModal">
            <div class="modal">
                <h3 class="modal-title">Confirm Deletion</h3>
                <p class="modal-body">
                    Are you sure you want to delete <strong id="deleteOrderLabel"></strong>?
                    This action cannot be undone.
                </p>
                <form id="orderDeleteForm" action="<?= baseUrl('orders/delete') ?>" method="POST">
                    <?= csrfField() ?>
                    <input type="hidden" name="id" id="deleteOrderId" value="">
                    <div class="modal-actions">
                        <button type="button" class="btn btn-ghost" id="cancelOrderDeleteBtn">Cancel</button>
                        <button type="submit" class="btn btn-danger" id="confirmOrderDeleteBtn">
                            <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/></svg>
                            Delete
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Delivered-at row (hidden until JS reveals it) -->
<template id="deliveredAtTemplate">
    <div class="order-detail-delivered-at" id="deliveredAtRow"></div>
</template>
