<?php $currentPage = 'orders'; ?>

<div class="page-header">
    <h2 class="page-title">Browse Restaurants</h2>
    <a href="<?= baseUrl('orders') ?>" class="btn btn-ghost">
        <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/></svg>
        Back to Orders
    </a>
</div>

<p class="browse-subtitle">Select a restaurant to start placing an order. Each restaurant offers one featured combo.</p>

<?php if (empty($restaurants)): ?>
    <div class="card">
        <div class="empty-state">
            <div class="empty-state-icon">
                <svg viewBox="0 0 24 24" width="64" height="64" fill="var(--color-text-light)"><path d="M11 9H9V2H7v7H5V2H3v7c0 2.12 1.66 3.84 3.75 3.97V22h2.5v-9.03C11.34 12.84 13 11.12 13 9V2h-2v7zm5-3v8h2.5v8H21V2c-2.76 0-5 2.24-5 4z"/></svg>
            </div>
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
                        <div class="restaurant-tile-name"><?= e($r['name']) ?></div>
                        <span class="food-type-chip"><?= e($r['food_type']) ?></span>
                    </div>
                </div>
                <div class="restaurant-tile-combo">
                    <div class="restaurant-tile-combo-label">Featured Combo</div>
                    <div class="restaurant-tile-combo-name"><?= e($r['combo_name']) ?></div>
                    <?php if (!empty($r['combo_description'])): ?>
                        <div class="restaurant-tile-combo-desc"><?= e($r['combo_description']) ?></div>
                    <?php endif; ?>
                    <div class="restaurant-tile-price">$<?= e(number_format((float) $r['combo_price'], 2)) ?> per unit</div>
                </div>
                <a href="<?= baseUrl('orders/create?restaurant_id=' . (int) $r['id']) ?>" class="btn btn-primary btn-block">
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
                    Order from this restaurant
                </a>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
