<?php $currentPage = 'pending-users'; ?>

<div class="page-header">
    <h2 class="page-title">Inactive Users</h2>
</div>

<?php if (empty($users)): ?>
    <div class="card"><div class="empty-state"><h3 class="empty-state-title">No inactive users</h3></div></div>
<?php else: ?>
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Document</th>
                    <th style="width: 120px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $u): ?>
                    <tr>
                        <td><strong><?= e($u['username']) ?></strong></td>
                        <td><?= e($u['email']) ?></td>
                        <td><?= e(ucfirst($u['role'])) ?></td>
                        <td><?= e($u['document'] ?? '-') ?></td>
                        <td>
                            <form action="<?= baseUrl('users/approve') ?>" method="POST" style="display: inline;">
                                <?= csrfField() ?>
                                <input type="hidden" name="id" value="<?= (int) $u['id'] ?>">
                                <button type="submit" class="btn btn-primary btn-sm">Approve</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
