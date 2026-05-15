<?php $currentPage = 'dashboard'; ?>

<?php
/**
 * Role-aware dashboard.
 *
 * Each role lands on a card grid showing only its allowed flows. The role
 * matrix is enforced by routes/middleware; this view is purely presentational.
 */
$role = currentRole();

/** Render a single card linking to $url. */
$card = function (string $url, string $title, string $subtitle, string $bg, string $color, string $svgPath): void {
    ?>
    <a href="<?= baseUrl($url) ?>" class="card" style="text-decoration: none; color: inherit;">
        <div style="display: flex; align-items: center; gap: var(--space-md);">
            <div style="width: 48px; height: 48px; border-radius: var(--radius-md); background: <?= e($bg) ?>; display: flex; align-items: center; justify-content: center;">
                <svg viewBox="0 0 24 24" width="24" height="24" fill="<?= e($color) ?>"><path d="<?= $svgPath /* SVG path data, not user input */ ?>"/></svg>
            </div>
            <div>
                <div style="font-size: var(--font-size-lg); font-weight: 600;"><?= e($title) ?></div>
                <div style="font-size: var(--font-size-sm); color: var(--color-text-secondary);"><?= e($subtitle) ?></div>
            </div>
        </div>
    </a>
    <?php
};
?>

<div class="page-header">
    <h2 class="page-title">Welcome, <?= e($_SESSION['username'] ?? 'User') ?></h2>
    <p class="page-subtitle" style="text-transform: capitalize;">Signed in as <?= e($role ?? 'guest') ?></p>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: var(--space-lg);">
<?php if ($role === 'admin'): ?>
    <?php $card('customers',      'Customers',      'Manage customer records',                '#E3F2FD', '#1565C0', 'M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z'); ?>
    <?php $card('restaurants',    'Restaurants',    'Manage restaurant accounts',             '#FFEBEE', '#C62828', 'M11 9H9V2H7v7H5V2H3v7c0 2.12 1.66 3.84 3.75 3.97V22h2.5v-9.03C11.34 12.84 13 11.12 13 9V2h-2v7zm5-3v8h2.5v8H21V2c-2.76 0-5 2.24-5 4z'); ?>
    <?php $card('drivers',        'Delivery Drivers', 'Manage drivers and availability',      '#E8F5E9', '#2E7D32', 'M18 18.5c.83 0 1.5-.67 1.5-1.5s-.67-1.5-1.5-1.5-1.5.67-1.5 1.5.67 1.5 1.5 1.5zM19.5 9.5h-1.84l-1.48-4.45C15.92 4.42 15.33 4 14.66 4H12v2h2.65l1.67 5H5.5c-1.38 0-2.5 1.12-2.5 2.5v3.5h2c0 1.66 1.34 3 3 3s3-1.34 3-3h4c0 1.66 1.34 3 3 3s3-1.34 3-3h2V14c0-2.76-2.24-4.5-4.5-4.5zM8 18.5c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zM13 9H5c-.55 0-1 .45-1 1s.45 1 1 1h8V9z'); ?>
    <?php $card('combos',         'Combos',         'Manage menu items across restaurants',   '#FFF8E1', '#F57F17', 'M3 3h18v2H3V3zm0 4h18v2H3V7zm0 4h12v2H3v-2zm0 4h12v2H3v-2zm0 4h18v2H3v-2z'); ?>
    <?php $card('orders',         'Orders',         'All orders and lifecycle',               '#EDE7F6', '#4527A0', 'M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 3c1.93 0 3.5 1.57 3.5 3.5S13.93 13 12 13s-3.5-1.57-3.5-3.5S10.07 6 12 6zm7 13H5v-.23c0-.62.28-1.2.76-1.58C7.47 15.82 9.64 15 12 15s4.53.82 6.24 2.19c.48.38.76.97.76 1.58V19z'); ?>
    <?php $card('reports',        'Reports',        'Dashboards, KPIs and analytics',         '#E0F7FA', '#00838F', 'M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 14H7v-2h5v2zm3-4H7v-2h8v2zm0-4H7V7h8v2z'); ?>
    <?php $card('users',          'Users',          'All system accounts',                    '#FCE4EC', '#AD1457', 'M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z'); ?>
    <?php $card('users/pending',  'Pending Users',  'Review and approve new accounts',        '#FFF3E0', '#E65100', 'M12 4V1L8 5l4 4V6c3.31 0 6 2.69 6 6 0 1.01-.25 1.97-.7 2.8l1.46 1.46C19.54 15.03 20 13.57 20 12c0-4.42-3.58-8-8-8zm0 14c-3.31 0-6-2.69-6-6 0-1.01.25-1.97.7-2.8L5.24 7.74C4.46 8.97 4 10.43 4 12c0 4.42 3.58 8 8 8v3l4-4-4-4v3z'); ?>
    <?php $card('register',       'Register User',  'Create new system accounts',             '#E8EAF6', '#303F9F', 'M15 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm-9-2V7H4v3H1v2h3v3h2v-3h3v-2H6zm9 4c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z'); ?>
<?php elseif ($role === 'customer'): ?>
    <?php $card('orders/browse', 'Browse Restaurants', 'Discover restaurants and place an order', '#FFEBEE', '#C62828', 'M11 9H9V2H7v7H5V2H3v7c0 2.12 1.66 3.84 3.75 3.97V22h2.5v-9.03C11.34 12.84 13 11.12 13 9V2h-2v7zm5-3v8h2.5v8H21V2c-2.76 0-5 2.24-5 4z'); ?>
    <?php $card('orders',        'My Orders',          'Track your past and current orders',     '#EDE7F6', '#4527A0', 'M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 3c1.93 0 3.5 1.57 3.5 3.5S13.93 13 12 13s-3.5-1.57-3.5-3.5S10.07 6 12 6zm7 13H5v-.23c0-.62.28-1.2.76-1.58C7.47 15.82 9.64 15 12 15s4.53.82 6.24 2.19c.48.38.76.97.76 1.58V19z'); ?>
<?php elseif ($role === 'driver'): ?>
    <?php $card('orders', 'My Deliveries', 'Orders assigned to you', '#E8F5E9', '#2E7D32', 'M18 18.5c.83 0 1.5-.67 1.5-1.5s-.67-1.5-1.5-1.5-1.5.67-1.5 1.5.67 1.5 1.5 1.5zM19.5 9.5h-1.84l-1.48-4.45C15.92 4.42 15.33 4 14.66 4H12v2h2.65l1.67 5H5.5c-1.38 0-2.5 1.12-2.5 2.5v3.5h2c0 1.66 1.34 3 3 3s3-1.34 3-3h4c0 1.66 1.34 3 3 3s3-1.34 3-3h2V14c0-2.76-2.24-4.5-4.5-4.5zM8 18.5c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zM13 9H5c-.55 0-1 .45-1 1s.45 1 1 1h8V9z'); ?>
<?php elseif ($role === 'restaurant'): ?>
    <?php $card('combos', 'My Combos',              'Manage your menu items',                   '#FFF8E1', '#F57F17', 'M3 3h18v2H3V3zm0 4h18v2H3V7zm0 4h12v2H3v-2zm0 4h12v2H3v-2zm0 4h18v2H3v-2z'); ?>
    <?php $card('orders', 'My Restaurant Orders',   'Orders that include your combos',          '#EDE7F6', '#4527A0', 'M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 3c1.93 0 3.5 1.57 3.5 3.5S13.93 13 12 13s-3.5-1.57-3.5-3.5S10.07 6 12 6zm7 13H5v-.23c0-.62.28-1.2.76-1.58C7.47 15.82 9.64 15 12 15s4.53.82 6.24 2.19c.48.38.76.97.76 1.58V19z'); ?>
<?php else: ?>
    <div class="card">
        <p>Your account doesn't have any modules assigned. Please contact an administrator.</p>
    </div>
<?php endif; ?>
</div>
