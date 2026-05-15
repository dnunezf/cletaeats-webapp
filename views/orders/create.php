<?php
/** @var array $restaurant Restaurant row */
/** @var array $combos     Combos for the restaurant */
/** @var array $customers  Active customer rows */
?>
<?php $currentPage = 'orders'; ?>
<?php $errors = getFlash('errors') ?? []; ?>

<div class="page-header">
    <h2 class="page-title">Place Order</h2>
    <a href="<?= baseUrl('orders/browse') ?>" class="btn btn-ghost">Change Restaurant</a>
</div>

<?php if (!empty($errors['general'])): ?>
    <div class="alert alert-error"><span><?= e($errors['general']) ?></span></div>
<?php endif; ?>

<div class="order-form-layout">
    <div class="order-form-main">
        <div class="card">
            <form id="orderForm" action="<?= baseUrl('orders/store') ?>" method="POST" novalidate>
                <?= csrfField() ?>
                <input type="hidden" name="restaurant_id" value="<?= (int) $restaurant['user_id'] ?>">

                <?php if (userIsAdmin()): ?>
                <div class="form-section-title">Customer</div>
                <div class="form-group">
                    <label for="customer_id" class="form-label">Select Customer <span style="color: var(--color-error)">*</span></label>
                    <select id="customer_id" name="customer_id" class="form-input" required>
                        <option value="">-- Select a customer --</option>
                        <?php $sel = old('customer_id', ''); foreach ($customers as $c): ?>
                            <option value="<?= (int) $c['user_id'] ?>" <?= (string) $c['user_id'] === (string) $sel ? 'selected' : '' ?>>
                                <?= e($c['username']) ?><?= !empty($c['email']) ? ' — ' . e($c['email']) : '' ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (!empty($errors['customer_id'])): ?>
                        <span class="form-error" style="display: block;"><?= e($errors['customer_id']) ?></span>
                    <?php endif; ?>
                </div>
                <?php else: ?>
                    <input type="hidden" name="customer_id" value="<?= (int) (currentUserId() ?? 0) ?>">
                <?php endif; ?>

                <div class="form-section-title">Order Details</div>

                <div class="form-group">
                    <label for="combo_id" class="form-label">Combo <span style="color: var(--color-error)">*</span></label>
                    <select id="combo_id" name="combo_id" class="form-input" required>
                        <option value="">-- Select a combo --</option>
                        <?php $sc = old('combo_id', ''); foreach ($combos as $combo): ?>
                            <option value="<?= (int) $combo['id'] ?>"
                                    data-price="<?= e(number_format((float) $combo['price'], 2, '.', '')) ?>"
                                    <?= (string) $combo['id'] === (string) $sc ? 'selected' : '' ?>>
                                <?= e($combo['name']) ?> — $<?= e(number_format((float) $combo['price'], 2)) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (!empty($errors['combo_id'])): ?>
                        <span class="form-error" style="display: block;"><?= e($errors['combo_id']) ?></span>
                    <?php endif; ?>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="quantity" class="form-label">Quantity <span style="color: var(--color-error)">*</span></label>
                        <input type="number" id="quantity" name="quantity" class="form-input"
                               min="1" max="99" value="<?= e(old('quantity', '1')) ?>" required>
                        <?php if (!empty($errors['quantity'])): ?>
                            <span class="form-error" style="display: block;"><?= e($errors['quantity']) ?></span>
                        <?php endif; ?>
                    </div>
                </div>

                <div style="display: flex; gap: var(--space-md); justify-content: flex-end; padding-top: var(--space-md);">
                    <a href="<?= baseUrl('orders/browse') ?>" class="btn btn-ghost">Cancel</a>
                    <button type="submit" class="btn btn-primary">Place Order</button>
                </div>
            </form>
        </div>
    </div>

    <div class="order-summary-sidebar">
        <div class="order-summary card">
            <div class="order-summary-title">Restaurant</div>
            <div class="order-summary-restaurant">
                <div class="order-summary-restaurant-name"><?= e($restaurant['username']) ?></div>
                <span class="food-type-chip"><?= e(ucfirst($restaurant['category'] ?? '')) ?></span>
            </div>
            <div class="order-summary-combo">
                <div class="order-summary-label">Available Combos</div>
                <ul style="margin: 0; padding-left: var(--space-md);">
                    <?php foreach ($combos as $combo): ?>
                        <li><?= e($combo['name']) ?> — $<?= e(number_format((float) $combo['price'], 2)) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
</div>
