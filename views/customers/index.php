<?php $currentPage = 'customers'; ?>
<?php $isAdmin = (($_SESSION['role'] ?? '') === 'admin'); ?>

<div class="page-header">
    <h2 class="page-title">Customers</h2>
</div>

<!-- Search Bar -->
<form action="<?= baseUrl('customers') ?>" method="GET" class="search-bar">
    <input
        type="text"
        name="search"
        class="form-input"
        placeholder="Search by name, email, phone, or city..."
        value="<?= e($search ?? '') ?>"
    >
    <button type="submit" class="btn btn-primary">
        <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/></svg>
        Search
    </button>
    <?php if (!empty($search)): ?>
        <a href="<?= baseUrl('customers') ?>" class="btn btn-ghost">Clear</a>
    <?php endif; ?>
</form>

<?php if (empty($customers)): ?>
    <!-- Empty State -->
    <div class="card">
        <div class="empty-state">
            <div class="empty-state-icon">
                <svg viewBox="0 0 24 24" width="64" height="64" fill="var(--color-text-light)"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>
            </div>
            <h3 class="empty-state-title">
                <?= !empty($search) ? 'No results found' : 'No customers yet' ?>
            </h3>
            <p class="empty-state-text">
                <?= !empty($search)
                    ? 'Try adjusting your search terms or clear the search to see all customers.'
                    : 'Get started by adding your first customer using the button below.' ?>
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
                    <th>Email</th>
                    <th>Phone</th>
                    <th>City</th>
                    <th style="width: 120px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($customers as $c): ?>
                    <tr id="customer-row-<?= (int) $c['id'] ?>">
                        <td>
                            <strong><?= e($c['first_name'] . ' ' . $c['last_name']) ?></strong>
                        </td>
                        <td><?= e($c['email']) ?></td>
                        <td><?= e($c['phone_number'] ?? '-') ?></td>
                        <td><?= e($c['city'] ?? '-') ?></td>
                        <td>
                            <div class="actions">
                                <?php if ($isAdmin): ?>
                                <a href="<?= baseUrl('customers/edit?id=' . (int) $c['id']) ?>"
                                   class="btn btn-icon btn-outline" title="Edit">
                                    <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/></svg>
                                </a>
                                <button type="button"
                                        class="btn btn-icon btn-danger delete-btn"
                                        title="Delete"
                                        data-id="<?= (int) $c['id'] ?>"
                                        data-name="<?= e($c['first_name'] . ' ' . $c['last_name']) ?>">
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
    <div class="customer-cards">
        <?php foreach ($customers as $c): ?>
            <div class="customer-card" id="customer-card-<?= (int) $c['id'] ?>">
                <div class="customer-card-header">
                    <div>
                        <div class="customer-card-name"><?= e($c['first_name'] . ' ' . $c['last_name']) ?></div>
                        <div class="customer-card-email"><?= e($c['email']) ?></div>
                    </div>
                </div>
                <div class="customer-card-details">
                    <div class="customer-card-detail">
                        <div class="customer-card-detail-label">Phone</div>
                        <div class="customer-card-detail-value"><?= e($c['phone_number'] ?? '-') ?></div>
                    </div>
                    <div class="customer-card-detail">
                        <div class="customer-card-detail-label">City</div>
                        <div class="customer-card-detail-value"><?= e($c['city'] ?? '-') ?></div>
                    </div>
                    <?php if (!empty($c['address'])): ?>
                    <div class="customer-card-detail" style="grid-column: 1 / -1;">
                        <div class="customer-card-detail-label">Address</div>
                        <div class="customer-card-detail-value"><?= e($c['address']) ?></div>
                    </div>
                    <?php endif; ?>
                </div>
                <?php if ($isAdmin): ?>
                <div class="customer-card-actions">
                    <a href="<?= baseUrl('customers/edit?id=' . (int) $c['id']) ?>" class="btn btn-outline btn-sm">
                        <svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/></svg>
                        Edit
                    </a>
                    <button type="button"
                            class="btn btn-danger btn-sm delete-btn"
                            data-id="<?= (int) $c['id'] ?>"
                            data-name="<?= e($c['first_name'] . ' ' . $c['last_name']) ?>">
                        <svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/></svg>
                        Delete
                    </button>
                </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php if (($_SESSION['role'] ?? '') === 'admin'): ?>
<!-- FAB -->
<a href="<?= baseUrl('customers/create') ?>" class="fab" title="Add Customer">
    <svg viewBox="0 0 24 24" width="24" height="24" fill="currentColor"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
</a>
<?php endif; ?>

<!-- Delete Confirmation Modal -->
<?php require BASE_PATH . '/views/partials/delete-modal.php'; ?>
