<?php $currentPage = 'drivers'; ?>
<?php $isAdmin = (($_SESSION['role'] ?? '') === 'admin'); ?>

<div class="page-header">
    <h2 class="page-title">Delivery Drivers</h2>
</div>

<form action="<?= baseUrl('drivers') ?>" method="GET" class="search-bar">
    <input type="text" name="search" class="form-input"
           placeholder="Search by name, document, email, card, or city..."
           value="<?= e($search ?? '') ?>">
    <button type="submit" class="btn btn-primary">Search</button>
    <?php if (!empty($search)): ?>
        <a href="<?= baseUrl('drivers') ?>" class="btn btn-ghost">Clear</a>
    <?php endif; ?>
</form>

<?php if (empty($drivers)): ?>
    <div class="card"><div class="empty-state"><h3 class="empty-state-title">No drivers</h3></div></div>
<?php else: ?>
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Document</th>
                    <th>Status</th>
                    <th>Penalties</th>
                    <th>Reg / Holiday $/km</th>
                    <th>Account</th>
                    <th style="width: 120px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($drivers as $d): ?>
                    <tr id="driver-row-<?= (int) $d['user_id'] ?>">
                        <td><strong><?= e($d['username']) ?></strong></td>
                        <td><?= e($d['document'] ?? '-') ?></td>
                        <td><span class="status-badge status-<?= e($d['status']) ?>"><?= e(ucfirst($d['status'])) ?></span></td>
                        <td>
                            <span class="warning-pill <?= ((int) $d['penalties']) > 0 ? 'warning-pill-active' : '' ?>">
                                <?= (int) $d['penalties'] ?>
                            </span>
                        </td>
                        <td>$<?= e(number_format((float) $d['km_cost_regular'], 2)) ?> / $<?= e(number_format((float) $d['km_cost_holidays'], 2)) ?></td>
                        <td>
                            <span class="badge <?= ($d['user_status'] ?? '') === 'active' ? 'badge-success' : 'badge-warning' ?>">
                                <?= e(ucfirst($d['user_status'] ?? '')) ?>
                            </span>
                        </td>
                        <td>
                            <div class="actions">
                                <?php if ($isAdmin): ?>
                                <a href="<?= baseUrl('drivers/edit?id=' . (int) $d['user_id']) ?>" class="btn btn-icon btn-outline">Edit</a>
                                <button type="button" class="btn btn-icon btn-danger delete-btn"
                                        data-id="<?= (int) $d['user_id'] ?>"
                                        data-name="<?= e($d['username']) ?>">Delete</button>
                                <?php else: ?>
                                    <span style="color: var(--color-text-light);">—</span>
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
<a href="<?= baseUrl('drivers/create') ?>" class="fab" title="Add Delivery Driver">
    <svg viewBox="0 0 24 24" width="24" height="24" fill="currentColor"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
</a>
<?php endif; ?>

<div class="modal-overlay" id="driverDeleteModal">
    <div class="modal">
        <h3 class="modal-title">Confirm Deletion</h3>
        <p class="modal-body">Are you sure you want to delete <strong id="deleteDriverName"></strong>?</p>
        <form id="driverDeleteForm" action="<?= baseUrl('drivers/delete') ?>" method="POST">
            <?= csrfField() ?>
            <input type="hidden" name="id" id="deleteDriverId" value="">
            <div class="modal-actions">
                <button type="button" class="btn btn-ghost" id="cancelDriverDeleteBtn">Cancel</button>
                <button type="submit" class="btn btn-danger" id="confirmDriverDeleteBtn">Delete</button>
            </div>
        </form>
    </div>
</div>
