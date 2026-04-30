<?php
/** @var array[] $orders  Order rows joined with customer, restaurant, driver */
/** @var bool   $isAdmin  True when the authenticated user is an admin */
/** @var string $search   Current search term (empty string if none) */
?>
<?php $currentPage = 'orders'; ?>

<div class="page-header">
    <h2 class="page-title">Orders</h2>
    <a href="<?= baseUrl('orders/browse') ?>" class="btn btn-primary">
        <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
        Place New Order
    </a>
</div>

<!-- Search Bar -->
<form action="<?= baseUrl('orders') ?>" method="GET" class="search-bar">
    <input
        type="text"
        name="search"
        class="form-input"
        placeholder="Search by customer, restaurant, combo, or status..."
        value="<?= e($search ?? '') ?>"
    >
    <button type="submit" class="btn btn-primary">
        <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/></svg>
        Search
    </button>
    <?php if (!empty($search)): ?>
        <a href="<?= baseUrl('orders') ?>" class="btn btn-ghost">Clear</a>
    <?php endif; ?>
</form>

<?php if (empty($orders)): ?>
    <!-- Empty State -->
    <div class="card">
        <div class="empty-state">
            <div class="empty-state-icon">
                <svg viewBox="0 0 24 24" width="64" height="64" fill="var(--color-text-light)"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 3c1.93 0 3.5 1.57 3.5 3.5S13.93 13 12 13s-3.5-1.57-3.5-3.5S10.07 6 12 6zm7 13H5v-.23c0-.62.28-1.2.76-1.58C7.47 15.82 9.64 15 12 15s4.53.82 6.24 2.19c.48.38.76.97.76 1.58V19z"/></svg>
            </div>
            <h3 class="empty-state-title">
                <?= !empty($search) ? 'No results found' : 'No orders yet' ?>
            </h3>
            <p class="empty-state-text">
                <?php if (!empty($search)): ?>
                    Try adjusting your search terms or clear the search to see all orders.
                <?php else: ?>
                    Place your first order by browsing the available restaurants.
                <?php endif; ?>
            </p>
            <?php if (empty($search)): ?>
                <a href="<?= baseUrl('orders/browse') ?>" class="btn btn-primary" style="margin-top: var(--space-md);">
                    Browse Restaurants
                </a>
            <?php endif; ?>
        </div>
    </div>
<?php else: ?>
    <!-- Desktop Table View -->
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 48px;">#</th>
                    <th>Customer</th>
                    <th>Restaurant</th>
                    <th>Combo</th>
                    <th style="width: 60px;">Qty</th>
                    <th style="width: 100px;">Total</th>
                    <th>Driver</th>
                    <th style="width: 110px;">Status</th>
                    <th style="width: 110px;">Date</th>
                    <th style="width: 100px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $o): ?>
                    <tr id="order-row-<?= (int) $o['id'] ?>">
                        <td style="color: var(--color-text-secondary); font-size: var(--font-size-xs);"><?= (int) $o['id'] ?></td>
                        <td><strong><?= e($o['customer_name']) ?></strong></td>
                        <td>
                            <strong><?= e($o['restaurant_name']) ?></strong>
                            <div style="font-size: var(--font-size-xs); color: var(--color-text-secondary);">
                                <?= e($o['food_type']) ?>
                            </div>
                        </td>
                        <td><?= e($o['combo_name']) ?></td>
                        <td style="text-align: center;"><?= (int) $o['quantity'] ?></td>
                        <td>$<?= e(number_format((float) $o['total'], 2)) ?></td>
                        <td style="font-size: var(--font-size-sm);">
                            <?= !empty($o['driver_name']) ? e($o['driver_name']) : '<span style="color:var(--color-text-secondary)">—</span>' ?>
                        </td>
                        <td>
                            <span class="order-status order-status-<?= e($o['status']) ?>">
                                <?= e(Order::displayStatus($o['status'])) ?>
                            </span>
                        </td>
                        <td style="font-size: var(--font-size-xs); color: var(--color-text-secondary);">
                            <?= e(date('M j, Y', strtotime($o['created_at']))) ?>
                        </td>
                        <td>
                            <div class="actions">
                                <a href="<?= baseUrl('orders/show?id=' . (int) $o['id']) ?>"
                                   class="btn btn-icon btn-outline" title="View">
                                    <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
                                </a>
                                <?php if ($isAdmin): ?>
                                <button type="button"
                                        class="btn btn-icon btn-danger delete-btn"
                                        title="Delete"
                                        data-id="<?= (int) $o['id'] ?>"
                                        data-label="Order #<?= (int) $o['id'] ?> for <?= e($o['customer_name']) ?>">
                                    <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/></svg>
                                </button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Mobile Card View -->
    <div class="order-cards">
        <?php foreach ($orders as $o): ?>
            <div class="order-card" id="order-card-<?= (int) $o['id'] ?>">
                <div class="order-card-header">
                    <div>
                        <div class="order-card-customer"><?= e($o['customer_name']) ?></div>
                        <div class="order-card-restaurant"><?= e($o['restaurant_name']) ?></div>
                    </div>
                    <span class="order-status order-status-<?= e($o['status']) ?>">
                        <?= e(Order::displayStatus($o['status'])) ?>
                    </span>
                </div>
                <div class="order-card-details">
                    <div class="order-card-detail">
                        <div class="order-card-detail-label">Combo</div>
                        <div class="order-card-detail-value"><?= e($o['combo_name']) ?></div>
                    </div>
                    <div class="order-card-detail">
                        <div class="order-card-detail-label">Qty</div>
                        <div class="order-card-detail-value"><?= (int) $o['quantity'] ?></div>
                    </div>
                    <div class="order-card-detail">
                        <div class="order-card-detail-label">Total</div>
                        <div class="order-card-detail-value"><strong>$<?= e(number_format((float) $o['total'], 2)) ?></strong></div>
                    </div>
                    <div class="order-card-detail">
                        <div class="order-card-detail-label">Date</div>
                        <div class="order-card-detail-value"><?= e(date('M j, Y', strtotime($o['created_at']))) ?></div>
                    </div>
                    <div class="order-card-detail">
                        <div class="order-card-detail-label">Driver</div>
                        <div class="order-card-detail-value">
                            <?= !empty($o['driver_name']) ? e($o['driver_name']) : '—' ?>
                        </div>
                    </div>
                </div>
                <div class="order-card-actions">
                    <a href="<?= baseUrl('orders/show?id=' . (int) $o['id']) ?>" class="btn btn-outline btn-sm">
                        <svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor"><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
                        View
                    </a>
                    <?php if ($isAdmin): ?>
                    <button type="button"
                            class="btn btn-danger btn-sm delete-btn"
                            data-id="<?= (int) $o['id'] ?>"
                            data-label="Order #<?= (int) $o['id'] ?> for <?= e($o['customer_name']) ?>">
                        <svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/></svg>
                        Delete
                    </button>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php if ($isAdmin): ?>
<!-- Delete Confirmation Modal (scoped to orders) -->
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
