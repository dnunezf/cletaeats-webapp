<?php $currentPage = 'restaurants'; ?>
<?php $isAdmin = (($_SESSION['role'] ?? '') === 'admin'); ?>

<div class="page-header">
    <h2 class="page-title">Restaurants</h2>
</div>

<form action="<?= baseUrl('restaurants') ?>" method="GET" class="search-bar">
    <input type="text" name="search" class="form-input"
           placeholder="Search by name, document, category, or city..."
           value="<?= e($search ?? '') ?>">
    <button type="submit" class="btn btn-primary">Search</button>
    <?php if (!empty($search)): ?>
        <a href="<?= baseUrl('restaurants') ?>" class="btn btn-ghost">Clear</a>
    <?php endif; ?>
</form>

<?php if (empty($restaurants)): ?>
    <div class="card">
        <div class="empty-state">
            <h3 class="empty-state-title"><?= !empty($search) ? 'No results found' : 'No restaurants yet' ?></h3>
        </div>
    </div>
<?php else: ?>
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Document</th>
                    <th>Category</th>
                    <th>City</th>
                    <th>Status</th>
                    <th style="width: 180px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($restaurants as $r): ?>
                    <tr id="restaurant-row-<?= (int) $r['user_id'] ?>">
                        <td>
                            <strong><?= e($r['username']) ?></strong>
                            <div style="font-size: var(--font-size-xs); color: var(--color-text-secondary);">
                                <?= e($r['address'] ?? '') ?>
                            </div>
                        </td>
                        <td><?= e($r['document'] ?? '-') ?></td>
                        <td><?= e(ucfirst($r['category'] ?? '')) ?></td>
                        <td><?= e($r['city'] ?? '-') ?></td>
                        <td>
                            <span class="badge <?= ($r['status'] ?? '') === 'active' ? 'badge-success' : 'badge-warning' ?>">
                                <?= e(ucfirst($r['status'] ?? '')) ?>
                            </span>
                        </td>
                        <td>
                            <div class="actions">
                                <a href="<?= baseUrl('combos?restaurant_id=' . (int) $r['user_id']) ?>"
                                   class="btn btn-icon btn-outline" title="Combos" aria-label="View combos"><?= actionIcon('combos') ?></a>
                                <?php if ($isAdmin): ?>
                                <a href="<?= baseUrl('restaurants/edit?id=' . (int) $r['user_id']) ?>"
                                   class="btn btn-icon btn-outline" title="Edit" aria-label="Edit"><?= actionIcon('edit') ?></a>
                                <button type="button" class="btn btn-icon btn-danger delete-btn"
                                        title="Delete" aria-label="Delete"
                                        data-id="<?= (int) $r['user_id'] ?>"
                                        data-name="<?= e($r['username']) ?>"><?= actionIcon('delete') ?></button>
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
<a href="<?= baseUrl('restaurants/create') ?>" class="fab" title="Add Restaurant">
    <svg viewBox="0 0 24 24" width="24" height="24" fill="currentColor"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
</a>
<?php endif; ?>

<div class="modal-overlay" id="restaurantDeleteModal">
    <div class="modal">
        <h3 class="modal-title">Confirm Deletion</h3>
        <p class="modal-body">Are you sure you want to delete <strong id="deleteRestaurantName"></strong>?</p>
        <form id="restaurantDeleteForm" action="<?= baseUrl('restaurants/delete') ?>" method="POST">
            <?= csrfField() ?>
            <input type="hidden" name="id" id="deleteRestaurantId" value="">
            <div class="modal-actions">
                <button type="button" class="btn btn-ghost" id="cancelRestaurantDeleteBtn">Cancel</button>
                <button type="submit" class="btn btn-danger" id="confirmRestaurantDeleteBtn">Delete</button>
            </div>
        </form>
    </div>
</div>
