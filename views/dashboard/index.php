<?php $currentPage = 'dashboard'; ?>

<div class="page-header">
    <h2 class="page-title">Welcome, <?= e($_SESSION['username'] ?? 'User') ?></h2>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: var(--space-lg);">
    <a href="<?= baseUrl('customers') ?>" class="card" style="text-decoration: none; color: inherit;">
        <div style="display: flex; align-items: center; gap: var(--space-md);">
            <div style="width: 48px; height: 48px; border-radius: var(--radius-md); background: var(--color-primary-bg); display: flex; align-items: center; justify-content: center;">
                <svg viewBox="0 0 24 24" width="24" height="24" fill="var(--color-primary)"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>
            </div>
            <div>
                <div style="font-size: var(--font-size-lg); font-weight: 600;">Customers</div>
                <div style="font-size: var(--font-size-sm); color: var(--color-text-secondary);">Manage your customer records</div>
            </div>
        </div>
    </a>

    <?php if (($_SESSION['role'] ?? '') === 'admin'): ?>
    <a href="<?= baseUrl('users/pending') ?>" class="card" style="text-decoration: none; color: inherit;">
        <div style="display: flex; align-items: center; gap: var(--space-md);">
            <div style="width: 48px; height: 48px; border-radius: var(--radius-md); background: #FFF3E0; display: flex; align-items: center; justify-content: center;">
                <svg viewBox="0 0 24 24" width="24" height="24" fill="#E65100"><path d="M12 4V1L8 5l4 4V6c3.31 0 6 2.69 6 6 0 1.01-.25 1.97-.7 2.8l1.46 1.46C19.54 15.03 20 13.57 20 12c0-4.42-3.58-8-8-8zm0 14c-3.31 0-6-2.69-6-6 0-1.01.25-1.97.7-2.8L5.24 7.74C4.46 8.97 4 10.43 4 12c0 4.42 3.58 8 8 8v3l4-4-4-4v3z"/></svg>
            </div>
            <div>
                <div style="font-size: var(--font-size-lg); font-weight: 600;">Pending Users</div>
                <div style="font-size: var(--font-size-sm); color: var(--color-text-secondary);">Review and approve new accounts</div>
            </div>
        </div>
    </a>

    <a href="<?= baseUrl('register') ?>" class="card" style="text-decoration: none; color: inherit;">
        <div style="display: flex; align-items: center; gap: var(--space-md);">
            <div style="width: 48px; height: 48px; border-radius: var(--radius-md); background: #E8EAF6; display: flex; align-items: center; justify-content: center;">
                <svg viewBox="0 0 24 24" width="24" height="24" fill="#303F9F"><path d="M15 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm-9-2V7H4v3H1v2h3v3h2v-3h3v-2H6zm9 4c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
            </div>
            <div>
                <div style="font-size: var(--font-size-lg); font-weight: 600;">Register User</div>
                <div style="font-size: var(--font-size-sm); color: var(--color-text-secondary);">Create new system accounts</div>
            </div>
        </div>
    </a>
    <?php endif; ?>
</div>
