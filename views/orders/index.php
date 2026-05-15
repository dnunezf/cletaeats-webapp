<?php
/** @var array[] $orders   Joined order rows */
/** @var bool    $isAdmin  True when admin */
/** @var string  $search   Current search */
?>
<?php $currentPage = 'orders'; ?>

<div class="page-header">
    <h2 class="page-title">Orders</h2>
    <a href="<?= baseUrl('orders/browse') ?>" class="btn btn-primary">Place New Order</a>
</div>

<form action="<?= baseUrl('orders') ?>" method="GET" class="search-bar">
    <input type="text" name="search" class="form-input"
           placeholder="Search by customer, driver, or status..."
           value="<?= e($search ?? '') ?>">
    <button type="submit" class="btn btn-primary">Search</button>
    <?php if (!empty($search)): ?>
        <a href="<?= baseUrl('orders') ?>" class="btn btn-ghost">Clear</a>
    <?php endif; ?>
</form>

<?php if (empty($orders)): ?>
    <div class="card">
        <div class="empty-state">
            <h3 class="empty-state-title"><?= !empty($search) ? 'No results found' : 'No orders yet' ?></h3>
            <?php if (empty($search)): ?>
                <a href="<?= baseUrl('orders/browse') ?>" class="btn btn-primary" style="margin-top: var(--space-md);">Browse Restaurants</a>
            <?php endif; ?>
        </div>
    </div>
<?php else: ?>
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 48px;">#</th>
                    <th>Customer</th>
                    <th>Restaurant</th>
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
                            <strong><?= e($o['restaurant_name'] ?? '—') ?></strong>
                            <?php if (!empty($o['category'])): ?>
                                <div style="font-size: var(--font-size-xs); color: var(--color-text-secondary);"><?= e(ucfirst($o['category'])) ?></div>
                            <?php endif; ?>
                        </td>
                        <td>$<?= e(number_format((float) $o['total'], 2)) ?></td>
                        <td style="font-size: var(--font-size-sm);"><?= e($o['driver_name']) ?></td>
                        <td>
                            <span class="order-status order-status-<?= e($o['status']) ?>">
                                <?= e(Order::displayStatus($o['status'])) ?>
                            </span>
                        </td>
                        <td style="font-size: var(--font-size-xs); color: var(--color-text-secondary);">
                            <?= e(date('M j, Y', strtotime($o['creation_date']))) ?>
                        </td>
                        <td>
                            <div class="actions">
                                <a href="<?= baseUrl('orders/show?id=' . (int) $o['id']) ?>"
                                   class="btn btn-icon btn-outline" title="View" aria-label="View"><?= actionIcon('view') ?></a>
                                <?php if ($isAdmin): ?>
                                <button type="button" class="btn btn-icon btn-danger delete-btn"
                                        title="Delete" aria-label="Delete"
                                        data-id="<?= (int) $o['id'] ?>"
                                        data-label="Order #<?= (int) $o['id'] ?>"><?= actionIcon('delete') ?></button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<?php if ($isAdmin): ?>
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
