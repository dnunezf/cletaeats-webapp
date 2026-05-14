<?php $currentPage = 'orders'; ?>

<div class="page-header">
    <h2 class="page-title">Browse Restaurants</h2>
    <a href="<?= baseUrl('orders') ?>" class="btn btn-ghost">Back to Orders</a>
</div>

<p class="browse-subtitle">Select a restaurant to start placing an order.</p>

<?php if (empty($restaurants)): ?>
    <div class="card">
        <div class="empty-state">
            <h3 class="empty-state-title">No restaurants available</h3>
            <p class="empty-state-text">There are no active restaurants to order from at this time.</p>
        </div>
    </div>
<?php else: ?>
    <div class="restaurant-grid">
        <?php foreach ($restaurants as $r): ?>
            <div class="restaurant-tile">
                <div class="restaurant-tile-header">
                    <div class="restaurant-tile-icon">
                        <svg viewBox="0 0 24 24" width="28" height="28" fill="currentColor"><path d="M11 9H9V2H7v7H5V2H3v7c0 2.12 1.66 3.84 3.75 3.97V22h2.5v-9.03C11.34 12.84 13 11.12 13 9V2h-2v7zm5-3v8h2.5v8H21V2c-2.76 0-5 2.24-5 4z"/></svg>
                    </div>
                    <div>
                        <div class="restaurant-tile-name"><?= e($r['username']) ?></div>
                        <span class="food-type-chip"><?= e(ucfirst($r['category'] ?? '')) ?></span>
                    </div>
                </div>
                <div class="restaurant-tile-combo">
                    <div class="restaurant-tile-combo-label">Location</div>
                    <div class="restaurant-tile-combo-desc"><?= e(trim(($r['address'] ?? '') . ', ' . ($r['city'] ?? ''), ', ')) ?></div>
                </div>
                <a href="<?= baseUrl('orders/create?restaurant_id=' . (int) $r['user_id']) ?>" class="btn btn-primary btn-block">
                    Order from this restaurant
                </a>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
