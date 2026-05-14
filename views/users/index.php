<?php $currentPage = 'users'; ?>
<?php $currentUserId = (int) ($_SESSION['user_id'] ?? 0); ?>

<div class="page-header">
    <h2 class="page-title">Users</h2>
</div>

<form action="<?= baseUrl('users') ?>" method="GET" class="search-bar">
    <input type="text" name="search" class="form-input"
           placeholder="Search by username, email, role, status, or document..."
           value="<?= e($search ?? '') ?>">
    <button type="submit" class="btn btn-primary">Search</button>
    <?php if (!empty($search)): ?>
        <a href="<?= baseUrl('users') ?>" class="btn btn-ghost">Clear</a>
    <?php endif; ?>
</form>

<?php if (empty($users)): ?>
    <div class="card"><div class="empty-state"><h3 class="empty-state-title">No users</h3></div></div>
<?php else: ?>
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Document</th>
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
                        <td>
                            <span class="badge <?= $u['status'] === 'active' ? 'badge-success' : 'badge-warning' ?>">
                                <?= e(ucfirst($u['status'])) ?>
                            </span>
                        </td>
                        <td><?= e($u['document'] ?? '-') ?></td>
                        <td>
                            <div class="actions">
                                <a href="<?= baseUrl('users/edit?id=' . (int) $u['id']) ?>"
                                   class="btn btn-icon btn-outline" title="Edit">Edit</a>
                                <?php if ((int) $u['id'] !== $currentUserId): ?>
                                <button type="button" class="btn btn-icon btn-danger delete-btn"
                                        title="Delete"
                                        data-id="<?= (int) $u['id'] ?>"
                                        data-name="<?= e($u['username']) ?>">Delete</button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<div class="modal-overlay" id="userDeleteModal">
    <div class="modal">
        <h3 class="modal-title">Confirm Deletion</h3>
        <p class="modal-body">Are you sure you want to delete <strong id="deleteUserName"></strong>?</p>
        <form id="userDeleteForm" action="<?= baseUrl('users/delete') ?>" method="POST">
            <?= csrfField() ?>
            <input type="hidden" name="id" id="deleteUserId" value="">
            <div class="modal-actions">
                <button type="button" class="btn btn-ghost" id="cancelUserDeleteBtn">Cancel</button>
                <button type="submit" class="btn btn-danger" id="confirmUserDeleteBtn">Delete</button>
            </div>
        </form>
    </div>
</div>
