<nav class="nav-drawer" id="navDrawer">
    <div class="nav-brand">
        <div class="nav-brand-icon">
            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path d="M11 9H9V2H7v7H5V2H3v7c0 2.12 1.66 3.84 3.75 3.97V22h2.5v-9.03C11.34 12.84 13 11.12 13 9V2h-2v7zm5-3v8h2.5v8H21V2c-2.76 0-5 2.24-5 4z"/>
            </svg>
        </div>
        <span class="nav-brand-text"><?= e(APP_NAME) ?></span>
    </div>

    <div class="nav-menu">
        <div class="nav-section-title">Main</div>

        <a href="<?= baseUrl('dashboard') ?>" class="nav-item <?= ($currentPage ?? '') === 'dashboard' ? 'active' : '' ?>">
            <svg viewBox="0 0 24 24"><path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/></svg>
            <span>Dashboard</span>
        </a>

        <div class="nav-section-title">Management</div>

        <a href="<?= baseUrl('customers') ?>" class="nav-item <?= ($currentPage ?? '') === 'customers' ? 'active' : '' ?>">
            <svg viewBox="0 0 24 24"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>
            <span>Customers</span>
        </a>

        <a href="<?= baseUrl('restaurants') ?>" class="nav-item <?= ($currentPage ?? '') === 'restaurants' ? 'active' : '' ?>">
            <svg viewBox="0 0 24 24"><path d="M11 9H9V2H7v7H5V2H3v7c0 2.12 1.66 3.84 3.75 3.97V22h2.5v-9.03C11.34 12.84 13 11.12 13 9V2h-2v7zm5-3v8h2.5v8H21V2c-2.76 0-5 2.24-5 4z"/></svg>
            <span>Restaurants</span>
        </a>

        <?php if (($_SESSION['role'] ?? '') === 'admin'): ?>
            <div class="nav-section-title">Administration</div>

            <a href="<?= baseUrl('users') ?>" class="nav-item <?= ($currentPage ?? '') === 'users' ? 'active' : '' ?>">
                <svg viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                <span>Users</span>
            </a>

            <a href="<?= baseUrl('users/pending') ?>" class="nav-item <?= ($currentPage ?? '') === 'pending-users' ? 'active' : '' ?>">
                <svg viewBox="0 0 24 24"><path d="M12 4V1L8 5l4 4V6c3.31 0 6 2.69 6 6 0 1.01-.25 1.97-.7 2.8l1.46 1.46C19.54 15.03 20 13.57 20 12c0-4.42-3.58-8-8-8zm0 14c-3.31 0-6-2.69-6-6 0-1.01.25-1.97.7-2.8L5.24 7.74C4.46 8.97 4 10.43 4 12c0 4.42 3.58 8 8 8v3l4-4-4-4v3z"/></svg>
                <span>Pending Users</span>
            </a>

            <a href="<?= baseUrl('register') ?>" class="nav-item <?= ($currentPage ?? '') === 'register' ? 'active' : '' ?>">
                <svg viewBox="0 0 24 24"><path d="M15 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm-9-2V7H4v3H1v2h3v3h2v-3h3v-2H6zm9 4c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                <span>Register User</span>
            </a>
        <?php endif; ?>
    </div>

    <div class="nav-footer">
        <div class="nav-user">
            <div class="nav-user-avatar">
                <?= strtoupper(substr($_SESSION['username'] ?? 'U', 0, 1)) ?>
            </div>
            <div class="nav-user-info">
                <div class="nav-user-name"><?= e($_SESSION['username'] ?? 'User') ?></div>
                <div class="nav-user-role"><?= e($_SESSION['role'] ?? 'user') ?></div>
            </div>
        </div>
        <a href="<?= baseUrl('logout') ?>" class="btn btn-outline btn-block btn-sm">
            <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M17 7l-1.41 1.41L18.17 11H8v2h10.17l-2.58 2.58L17 17l5-5zM4 5h8V3H4c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h8v-2H4V5z"/></svg>
            Logout
        </a>
    </div>
</nav>

<div class="nav-overlay" id="navOverlay"></div>
