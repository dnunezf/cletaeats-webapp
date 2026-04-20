<?php $currentPage = 'users'; ?>
<?php $currentUserId = (int) ($_SESSION['user_id'] ?? 0); ?>

<div class="page-header">
    <h2 class="page-title">Users</h2>
</div>

<!-- Search Bar -->
<form action="<?= baseUrl('users') ?>" method="GET" class="search-bar">
    <input
        type="text"
        name="search"
        class="form-input"
        placeholder="Search by username, email, role, or status..."
        value="<?= e($search ?? '') ?>"
    >
    <button type="submit" class="btn btn-primary">
        <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/></svg>
        Search
    </button>
    <?php if (!empty($search)): ?>
        <a href="<?= baseUrl('users') ?>" class="btn btn-ghost">Clear</a>
    <?php endif; ?>
</form>

<?php if (empty($users)): ?>
    <div class="card">
        <div class="empty-state">
            <div class="empty-state-icon">
                <svg viewBox="0 0 24 24" width="64" height="64" fill="var(--color-text-light)"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>
            </div>
            <h3 class="empty-state-title">
                <?= !empty($search) ? 'No results found' : 'No users yet' ?>
            </h3>
            <p class="empty-state-text">
                <?= !empty($search)
                    ? 'Try adjusting your search terms or clear the search to see all users.'
                    : 'No users have been registered yet.' ?>
            </p>
        </div>
    </div>
<?php else: ?>
    <!-- Desktop Table View -->
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Active</th>
                    <th style="width: 120px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $u): ?>
                    <tr id="user-row-<?= (int) $u['id'] ?>">
                        <td>
                            <strong><?= e($u['username']) ?></strong>
                            <?php if ((int) $u['id'] === $currentUserId): ?>
                                <span style="font-size: var(--font-size-xs); color: var(--color-text-secondary);">(you)</span>
                            <?php endif; ?>
                        </td>
                        <td><?= e($u['email']) ?></td>
                        <td><?= e(ucfirst($u['role'])) ?></td>
                        <td><?= e(ucfirst($u['status'])) ?></td>
                        <td><?= ((int) $u['is_active'] === 1) ? 'Yes' : 'No' ?></td>
                        <td>
                            <div class="actions">
                                <a href="<?= baseUrl('users/edit?id=' . (int) $u['id']) ?>"
                                   class="btn btn-icon btn-outline" title="Edit">
                                    <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/></svg>
                                </a>
                                <?php if ((int) $u['id'] !== $currentUserId): ?>
                                <button type="button"
                                        class="btn btn-icon btn-danger delete-btn"
                                        title="Delete"
                                        data-id="<?= (int) $u['id'] ?>"
                                        data-name="<?= e($u['username']) ?>">
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
    <div class="customer-cards">
        <?php foreach ($users as $u): ?>
            <div class="customer-card" id="user-card-<?= (int) $u['id'] ?>">
                <div class="customer-card-header">
                    <div>
                        <div class="customer-card-name">
                            <?= e($u['username']) ?>
                            <?php if ((int) $u['id'] === $currentUserId): ?>
                                <span style="font-size: var(--font-size-xs); color: var(--color-text-secondary);">(you)</span>
                            <?php endif; ?>
                        </div>
                        <div class="customer-card-email"><?= e($u['email']) ?></div>
                    </div>
                </div>
                <div class="customer-card-details">
                    <div class="customer-card-detail">
                        <div class="customer-card-detail-label">Role</div>
                        <div class="customer-card-detail-value"><?= e(ucfirst($u['role'])) ?></div>
                    </div>
                    <div class="customer-card-detail">
                        <div class="customer-card-detail-label">Status</div>
                        <div class="customer-card-detail-value"><?= e(ucfirst($u['status'])) ?></div>
                    </div>
                    <div class="customer-card-detail">
                        <div class="customer-card-detail-label">Active</div>
                        <div class="customer-card-detail-value"><?= ((int) $u['is_active'] === 1) ? 'Yes' : 'No' ?></div>
                    </div>
                </div>
                <div class="customer-card-actions">
                    <a href="<?= baseUrl('users/edit?id=' . (int) $u['id']) ?>" class="btn btn-outline btn-sm">
                        <svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/></svg>
                        Edit
                    </a>
                    <?php if ((int) $u['id'] !== $currentUserId): ?>
                    <button type="button"
                            class="btn btn-danger btn-sm delete-btn"
                            data-id="<?= (int) $u['id'] ?>"
                            data-name="<?= e($u['username']) ?>">
                        <svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/></svg>
                        Delete
                    </button>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<!-- Delete Confirmation Modal (scoped to users) -->
<div class="modal-overlay" id="userDeleteModal">
    <div class="modal">
        <h3 class="modal-title">Confirm Deletion</h3>
        <p class="modal-body">
            Are you sure you want to delete <strong id="deleteUserName"></strong>?
            This action cannot be undone.
        </p>
        <form id="userDeleteForm" action="<?= baseUrl('users/delete') ?>" method="POST">
            <?= csrfField() ?>
            <input type="hidden" name="id" id="deleteUserId" value="">
            <div class="modal-actions">
                <button type="button" class="btn btn-ghost" id="cancelUserDeleteBtn">Cancel</button>
                <button type="submit" class="btn btn-danger" id="confirmUserDeleteBtn">
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/></svg>
                    Delete
                </button>
            </div>
        </form>
    </div>
</div>
