<?php $currentPage = 'customers'; ?>
<?php $isAdmin = (($_SESSION['role'] ?? '') === 'admin'); ?>

<div class="page-header">
    <h2 class="page-title">Customers</h2>
</div>

<form action="<?= baseUrl('customers') ?>" method="GET" class="search-bar">
    <input type="text" name="search" class="form-input"
           placeholder="Search by name, email, document, card number, or city..."
           value="<?= e($search ?? '') ?>">
    <button type="submit" class="btn btn-primary">Search</button>
    <?php if (!empty($search)): ?>
        <a href="<?= baseUrl('customers') ?>" class="btn btn-ghost">Clear</a>
    <?php endif; ?>
</form>

<?php if (empty($customers)): ?>
    <div class="card">
        <div class="empty-state">
            <h3 class="empty-state-title"><?= !empty($search) ? 'No results found' : 'No customers yet' ?></h3>
            <p class="empty-state-text">
                <?= !empty($search) ? 'Try adjusting your search.' : 'Add the first customer using the button below.' ?>
            </p>
        </div>
    </div>
<?php else: ?>
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Document</th>
                    <th>Card</th>
                    <th>City</th>
                    <th>Status</th>
                    <th style="width: 120px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($customers as $c): ?>
                    <tr id="customer-row-<?= (int) $c['user_id'] ?>">
                        <td><strong><?= e($c['username']) ?></strong></td>
                        <td><?= e($c['email']) ?></td>
                        <td><?= e($c['document'] ?? '-') ?></td>
                        <td><?= e($c['card_number'] ?? '-') ?></td>
                        <td><?= e($c['city'] ?? '-') ?></td>
                        <td>
                            <span class="badge <?= ($c['status'] ?? '') === 'active' ? 'badge-success' : 'badge-warning' ?>">
                                <?= e(ucfirst($c['status'] ?? '')) ?>
                            </span>
                        </td>
                        <td>
                            <div class="actions">
                                <?php if ($isAdmin): ?>
                                <a href="<?= baseUrl('customers/edit?id=' . (int) $c['user_id']) ?>"
                                   class="btn btn-icon btn-outline" title="Edit">Edit</a>
                                <button type="button" class="btn btn-icon btn-danger delete-btn"
                                        title="Delete"
                                        data-id="<?= (int) $c['user_id'] ?>"
                                        data-name="<?= e($c['username']) ?>">Delete</button>
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
<a href="<?= baseUrl('customers/create') ?>" class="fab" title="Add Customer">
    <svg viewBox="0 0 24 24" width="24" height="24" fill="currentColor"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
</a>
<?php endif; ?>

<?php require BASE_PATH . '/views/partials/delete-modal.php'; ?>
