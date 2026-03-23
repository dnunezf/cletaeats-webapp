<?php $currentPage = 'pending-users'; ?>

<div class="page-header">
    <h2 class="page-title">Pending Users</h2>
</div>

<?php if (empty($users)): ?>
    <div class="card">
        <div class="empty-state">
            <div class="empty-state-icon">
                <svg viewBox="0 0 24 24" width="64" height="64" fill="var(--color-text-light)"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
            </div>
            <h3 class="empty-state-title">No pending users</h3>
            <p class="empty-state-text">All user accounts have been approved.</p>
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
                    <th>Registered</th>
                    <th style="width: 120px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $u): ?>
                    <tr>
                        <td><strong><?= e($u['username']) ?></strong></td>
                        <td><?= e($u['email']) ?></td>
                        <td><?= e(date('M j, Y', strtotime($u['created_at']))) ?></td>
                        <td>
                            <form action="<?= baseUrl('users/approve') ?>" method="POST" style="display: inline;">
                                <?= csrfField() ?>
                                <input type="hidden" name="id" value="<?= (int) $u['id'] ?>">
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                                    Approve
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Mobile Card View -->
    <div class="customer-cards">
        <?php foreach ($users as $u): ?>
            <div class="customer-card">
                <div class="customer-card-header">
                    <div>
                        <div class="customer-card-name"><?= e($u['username']) ?></div>
                        <div class="customer-card-email"><?= e($u['email']) ?></div>
                    </div>
                </div>
                <div class="customer-card-details">
                    <div class="customer-card-detail">
                        <div class="customer-card-detail-label">Registered</div>
                        <div class="customer-card-detail-value"><?= e(date('M j, Y', strtotime($u['created_at']))) ?></div>
                    </div>
                </div>
                <div class="customer-card-actions">
                    <form action="<?= baseUrl('users/approve') ?>" method="POST">
                        <?= csrfField() ?>
                        <input type="hidden" name="id" value="<?= (int) $u['id'] ?>">
                        <button type="submit" class="btn btn-primary btn-sm">
                            <svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                            Approve
                        </button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
