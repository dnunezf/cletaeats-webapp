<?php $currentPage = 'restaurants'; ?>
<?php $isAdmin = (($_SESSION['role'] ?? '') === 'admin'); ?>

<div class="page-header">
    <h2 class="page-title">Restaurants</h2>
</div>

<!-- Search Bar -->
<form action="<?= baseUrl('restaurants') ?>" method="GET" class="search-bar">
    <input
        type="text"
        name="search"
        class="form-input"
        placeholder="Search by name, legal ID, food type, or address..."
        value="<?= e($search ?? '') ?>"
    >
    <button type="submit" class="btn btn-primary">
        <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/></svg>
        Search
    </button>
    <?php if (!empty($search)): ?>
        <a href="<?= baseUrl('restaurants') ?>" class="btn btn-ghost">Clear</a>
    <?php endif; ?>
</form>

<?php if (empty($restaurants)): ?>
    <!-- Empty State -->
    <div class="card">
        <div class="empty-state">
            <div class="empty-state-icon">
                <svg viewBox="0 0 24 24" width="64" height="64" fill="var(--color-text-light)"><path d="M11 9H9V2H7v7H5V2H3v7c0 2.12 1.66 3.84 3.75 3.97V22h2.5v-9.03C11.34 12.84 13 11.12 13 9V2h-2v7zm5-3v8h2.5v8H21V2c-2.76 0-5 2.24-5 4z"/></svg>
            </div>
            <h3 class="empty-state-title">
                <?= !empty($search) ? 'No results found' : 'No restaurants yet' ?>
            </h3>
            <p class="empty-state-text">
                <?php if (!empty($search)): ?>
                    Try adjusting your search terms or clear the search to see all restaurants.
                <?php elseif ($isAdmin): ?>
                    Get started by adding your first restaurant using the button below.
                <?php else: ?>
                    There are no restaurants registered yet.
                <?php endif; ?>
            </p>
        </div>
    </div>
<?php else: ?>
    <!-- Desktop Table View -->
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Legal ID</th>
                    <th>Food Type</th>
                    <th>Combo</th>
                    <th style="width: 120px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($restaurants as $r): ?>
                    <tr id="restaurant-row-<?= (int) $r['id'] ?>">
                        <td>
                            <strong><?= e($r['name']) ?></strong>
                            <div style="font-size: var(--font-size-xs); color: var(--color-text-secondary);">
                                <?= e($r['address']) ?>
                            </div>
                        </td>
                        <td><?= e($r['legal_id']) ?></td>
                        <td><?= e($r['food_type']) ?></td>
                        <td>
                            <strong><?= e($r['combo_name']) ?></strong>
                            <div style="font-size: var(--font-size-xs); color: var(--color-text-secondary);">
                                $<?= e(number_format((float) $r['combo_price'], 2)) ?>
                            </div>
                        </td>
                        <td>
                            <div class="actions">
                                <?php if ($isAdmin): ?>
                                <a href="<?= baseUrl('restaurants/edit?id=' . (int) $r['id']) ?>"
                                   class="btn btn-icon btn-outline" title="Edit">
                                    <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/></svg>
                                </a>
                                <button type="button"
                                        class="btn btn-icon btn-danger delete-btn"
                                        title="Delete"
                                        data-id="<?= (int) $r['id'] ?>"
                                        data-name="<?= e($r['name']) ?>">
                                    <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/></svg>
                                </button>
                                <?php else: ?>
                                    <span style="color: var(--color-text-light); font-size: var(--font-size-xs);">—</span>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Mobile Card View -->
    <div class="restaurant-cards">
        <?php foreach ($restaurants as $r): ?>
            <div class="restaurant-card" id="restaurant-card-<?= (int) $r['id'] ?>">
                <div class="restaurant-card-header">
                    <div>
                        <div class="restaurant-card-name"><?= e($r['name']) ?></div>
                        <div class="restaurant-card-type"><?= e($r['food_type']) ?></div>
                    </div>
                </div>
                <div class="restaurant-card-details">
                    <div class="restaurant-card-detail">
                        <div class="restaurant-card-detail-label">Legal ID</div>
                        <div class="restaurant-card-detail-value"><?= e($r['legal_id']) ?></div>
                    </div>
                    <div class="restaurant-card-detail">
                        <div class="restaurant-card-detail-label">Combo Price</div>
                        <div class="restaurant-card-detail-value">$<?= e(number_format((float) $r['combo_price'], 2)) ?></div>
                    </div>
                    <div class="restaurant-card-detail" style="grid-column: 1 / -1;">
                        <div class="restaurant-card-detail-label">Address</div>
                        <div class="restaurant-card-detail-value"><?= e($r['address']) ?></div>
                    </div>
                    <div class="restaurant-card-detail" style="grid-column: 1 / -1;">
                        <div class="restaurant-card-detail-label">Combo</div>
                        <div class="restaurant-card-detail-value">
                            <strong><?= e($r['combo_name']) ?></strong>
                            <?php if (!empty($r['combo_description'])): ?>
                                — <?= e($r['combo_description']) ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php if ($isAdmin): ?>
                <div class="restaurant-card-actions">
                    <a href="<?= baseUrl('restaurants/edit?id=' . (int) $r['id']) ?>" class="btn btn-outline btn-sm">
                        <svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/></svg>
                        Edit
                    </a>
                    <button type="button"
                            class="btn btn-danger btn-sm delete-btn"
                            data-id="<?= (int) $r['id'] ?>"
                            data-name="<?= e($r['name']) ?>">
                        <svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/></svg>
                        Delete
                    </button>
                </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php if ($isAdmin): ?>
<!-- FAB -->
<a href="<?= baseUrl('restaurants/create') ?>" class="fab" title="Add Restaurant">
    <svg viewBox="0 0 24 24" width="24" height="24" fill="currentColor"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
</a>
<?php endif; ?>

<!-- Delete Confirmation Modal (scoped to restaurants) -->
<div class="modal-overlay" id="restaurantDeleteModal">
    <div class="modal">
        <h3 class="modal-title">Confirm Deletion</h3>
        <p class="modal-body">
            Are you sure you want to delete <strong id="deleteRestaurantName"></strong>?
            This action cannot be undone.
        </p>
        <form id="restaurantDeleteForm" action="<?= baseUrl('restaurants/delete') ?>" method="POST">
            <?= csrfField() ?>
            <input type="hidden" name="id" id="deleteRestaurantId" value="">
            <div class="modal-actions">
                <button type="button" class="btn btn-ghost" id="cancelRestaurantDeleteBtn">Cancel</button>
                <button type="submit" class="btn btn-danger" id="confirmRestaurantDeleteBtn">
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/></svg>
                    Delete
                </button>
            </div>
        </form>
    </div>
</div>
