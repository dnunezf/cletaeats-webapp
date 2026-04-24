<?php $currentPage = 'drivers'; ?>
<?php $isAdmin = (($_SESSION['role'] ?? '') === 'admin'); ?>

<div class="page-header">
    <h2 class="page-title">Delivery Drivers</h2>
</div>

<!-- Search Bar -->
<form action="<?= baseUrl('drivers') ?>" method="GET" class="search-bar">
    <input
        type="text"
        name="search"
        class="form-input"
        placeholder="Search by name, ID number, phone, email, or address..."
        value="<?= e($search ?? '') ?>"
    >
    <button type="submit" class="btn btn-primary">
        <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/></svg>
        Search
    </button>
    <?php if (!empty($search)): ?>
        <a href="<?= baseUrl('drivers') ?>" class="btn btn-ghost">Clear</a>
    <?php endif; ?>
</form>

<?php if (empty($drivers)): ?>
    <!-- Empty State -->
    <div class="card">
        <div class="empty-state">
            <div class="empty-state-icon">
                <svg viewBox="0 0 24 24" width="64" height="64" fill="var(--color-text-light)"><path d="M18 18.5c.83 0 1.5-.67 1.5-1.5s-.67-1.5-1.5-1.5-1.5.67-1.5 1.5.67 1.5 1.5 1.5zM19.5 9.5h-1.84l-1.48-4.45C15.92 4.42 15.33 4 14.66 4H12v2h2.65l1.67 5H5.5c-1.38 0-2.5 1.12-2.5 2.5v3.5h2c0 1.66 1.34 3 3 3s3-1.34 3-3h4c0 1.66 1.34 3 3 3s3-1.34 3-3h2V14c0-2.76-2.24-4.5-4.5-4.5zM8 18.5c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zM13 9H5c-.55 0-1 .45-1 1s.45 1 1 1h8V9z"/></svg>
            </div>
            <h3 class="empty-state-title">
                <?= !empty($search) ? 'No results found' : 'No delivery drivers yet' ?>
            </h3>
            <p class="empty-state-text">
                <?php if (!empty($search)): ?>
                    Try adjusting your search terms or clear the search to see all drivers.
                <?php elseif ($isAdmin): ?>
                    Get started by adding your first delivery driver using the button below.
                <?php else: ?>
                    There are no delivery drivers registered yet.
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
                    <th>Phone</th>
                    <th>Status</th>
                    <th>Daily km</th>
                    <th>Warnings</th>
                    <th style="width: 120px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($drivers as $d): ?>
                    <tr id="driver-row-<?= (int) $d['id'] ?>">
                        <td>
                            <strong><?= e($d['full_name']) ?></strong>
                            <div style="font-size: var(--font-size-xs); color: var(--color-text-secondary);">
                                ID: <?= e($d['id_number']) ?>
                            </div>
                        </td>
                        <td><?= e($d['phone']) ?></td>
                        <td>
                            <span class="status-badge status-<?= e($d['status']) ?>">
                                <?= e(ucfirst($d['status'])) ?>
                            </span>
                        </td>
                        <td><?= e(number_format((float) $d['daily_kilometers'], 2)) ?> km</td>
                        <td>
                            <span class="warning-pill <?= ((int) $d['warning_count']) > 0 ? 'warning-pill-active' : '' ?>">
                                <?= (int) $d['warning_count'] ?>
                            </span>
                        </td>
                        <td>
                            <div class="actions">
                                <?php if ($isAdmin): ?>
                                <a href="<?= baseUrl('drivers/edit?id=' . (int) $d['id']) ?>"
                                   class="btn btn-icon btn-outline" title="Edit">
                                    <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/></svg>
                                </a>
                                <button type="button"
                                        class="btn btn-icon btn-danger delete-btn"
                                        title="Delete"
                                        data-id="<?= (int) $d['id'] ?>"
                                        data-name="<?= e($d['full_name']) ?>">
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
    <div class="driver-cards">
        <?php foreach ($drivers as $d): ?>
            <div class="driver-card" id="driver-card-<?= (int) $d['id'] ?>">
                <div class="driver-card-header">
                    <div>
                        <div class="driver-card-name"><?= e($d['full_name']) ?></div>
                        <div class="driver-card-sub">ID: <?= e($d['id_number']) ?></div>
                    </div>
                    <span class="status-badge status-<?= e($d['status']) ?>">
                        <?= e(ucfirst($d['status'])) ?>
                    </span>
                </div>
                <div class="driver-card-details">
                    <div class="driver-card-detail">
                        <div class="driver-card-detail-label">Phone</div>
                        <div class="driver-card-detail-value"><?= e($d['phone']) ?></div>
                    </div>
                    <div class="driver-card-detail">
                        <div class="driver-card-detail-label">Email</div>
                        <div class="driver-card-detail-value"><?= e($d['email']) ?></div>
                    </div>
                    <div class="driver-card-detail">
                        <div class="driver-card-detail-label">Daily km</div>
                        <div class="driver-card-detail-value"><?= e(number_format((float) $d['daily_kilometers'], 2)) ?></div>
                    </div>
                    <div class="driver-card-detail">
                        <div class="driver-card-detail-label">Warnings</div>
                        <div class="driver-card-detail-value">
                            <span class="warning-pill <?= ((int) $d['warning_count']) > 0 ? 'warning-pill-active' : '' ?>">
                                <?= (int) $d['warning_count'] ?>
                            </span>
                        </div>
                    </div>
                    <div class="driver-card-detail" style="grid-column: 1 / -1;">
                        <div class="driver-card-detail-label">Address</div>
                        <div class="driver-card-detail-value"><?= e($d['address']) ?></div>
                    </div>
                    <?php if (!empty($d['complaints'])): ?>
                    <div class="driver-card-detail" style="grid-column: 1 / -1;">
                        <div class="driver-card-detail-label">Complaints</div>
                        <div class="driver-card-detail-value"><?= e($d['complaints']) ?></div>
                    </div>
                    <?php endif; ?>
                </div>
                <?php if ($isAdmin): ?>
                <div class="driver-card-actions">
                    <a href="<?= baseUrl('drivers/edit?id=' . (int) $d['id']) ?>" class="btn btn-outline btn-sm">
                        <svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/></svg>
                        Edit
                    </a>
                    <button type="button"
                            class="btn btn-danger btn-sm delete-btn"
                            data-id="<?= (int) $d['id'] ?>"
                            data-name="<?= e($d['full_name']) ?>">
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
<a href="<?= baseUrl('drivers/create') ?>" class="fab" title="Add Delivery Driver">
    <svg viewBox="0 0 24 24" width="24" height="24" fill="currentColor"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
</a>
<?php endif; ?>

<!-- Delete Confirmation Modal (scoped to drivers) -->
<div class="modal-overlay" id="driverDeleteModal">
    <div class="modal">
        <h3 class="modal-title">Confirm Deletion</h3>
        <p class="modal-body">
            Are you sure you want to delete <strong id="deleteDriverName"></strong>?
            This action cannot be undone.
        </p>
        <form id="driverDeleteForm" action="<?= baseUrl('drivers/delete') ?>" method="POST">
            <?= csrfField() ?>
            <input type="hidden" name="id" id="deleteDriverId" value="">
            <div class="modal-actions">
                <button type="button" class="btn btn-ghost" id="cancelDriverDeleteBtn">Cancel</button>
                <button type="submit" class="btn btn-danger" id="confirmDriverDeleteBtn">
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/></svg>
                    Delete
                </button>
            </div>
        </form>
    </div>
</div>
