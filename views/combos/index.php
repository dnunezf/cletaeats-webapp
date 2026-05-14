<?php $currentPage = 'combos'; ?>
<?php $isAdmin = (($_SESSION['role'] ?? '') === 'admin'); ?>

<div class="page-header">
    <h2 class="page-title">
        Combos<?php if (!empty($restaurant)): ?> — <?= e($restaurant['username']) ?><?php endif; ?>
    </h2>
</div>

<form action="<?= baseUrl('combos') ?>" method="GET" class="search-bar">
    <input type="text" name="search" class="form-input"
           placeholder="Search by name or restaurant..." value="<?= e($search ?? '') ?>">
    <button type="submit" class="btn btn-primary">Search</button>
    <?php if (!empty($search)): ?>
        <a href="<?= baseUrl('combos') ?>" class="btn btn-ghost">Clear</a>
    <?php endif; ?>
</form>

<?php if (empty($combos)): ?>
    <div class="card"><div class="empty-state"><h3 class="empty-state-title">No combos</h3></div></div>
<?php else: ?>
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Combo</th>
                    <th>Description</th>
                    <?php if (empty($restaurant)): ?><th>Restaurant</th><?php endif; ?>
                    <th style="text-align: right;">Price</th>
                    <?php if ($isAdmin): ?><th style="width: 140px;">Actions</th><?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($combos as $c): ?>
                    <tr id="combo-row-<?= (int) $c['id'] ?>">
                        <td><strong><?= e($c['name']) ?></strong></td>
                        <td><?= e($c['description']) ?></td>
                        <?php if (empty($restaurant)): ?>
                            <td><?= e($c['restaurant_name'] ?? '—') ?></td>
                        <?php endif; ?>
                        <td style="text-align: right; font-weight: 600;">$<?= e(number_format((float) $c['price'], 2)) ?></td>
                        <?php if ($isAdmin): ?>
                        <td>
                            <div class="actions">
                                <a href="<?= baseUrl('combos/edit?id=' . (int) $c['id']) ?>" class="btn btn-icon btn-outline">Edit</a>
                                <button type="button" class="btn btn-icon btn-danger delete-btn"
                                        data-id="<?= (int) $c['id'] ?>"
                                        data-name="<?= e($c['name']) ?>">Delete</button>
                            </div>
                        </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<?php if ($isAdmin): ?>
<a href="<?= baseUrl('combos/create' . (!empty($restaurant) ? '?restaurant_id=' . (int) $restaurant['user_id'] : '')) ?>" class="fab">
    <svg viewBox="0 0 24 24" width="24" height="24" fill="currentColor"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
</a>
<?php endif; ?>

<div class="modal-overlay" id="comboDeleteModal">
    <div class="modal">
        <h3 class="modal-title">Confirm Deletion</h3>
        <p class="modal-body">Are you sure you want to delete <strong id="deleteComboName"></strong>?</p>
        <form id="comboDeleteForm" action="<?= baseUrl('combos/delete') ?>" method="POST">
            <?= csrfField() ?>
            <input type="hidden" name="id" id="deleteComboId" value="">
            <div class="modal-actions">
                <button type="button" class="btn btn-ghost" id="cancelComboDeleteBtn">Cancel</button>
                <button type="submit" class="btn btn-danger" id="confirmComboDeleteBtn">Delete</button>
            </div>
        </form>
    </div>
</div>
