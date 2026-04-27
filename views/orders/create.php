<?php $currentPage = 'orders'; ?>
<?php $errors = getFlash('errors') ?? []; ?>

<div class="page-header">
    <h2 class="page-title">Place Order</h2>
    <a href="<?= baseUrl('orders/browse') ?>" class="btn btn-ghost">
        <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/></svg>
        Change Restaurant
    </a>
</div>

<div class="order-form-layout">
    <!-- Form -->
    <div class="order-form-main">
        <div class="card">
            <form id="orderForm" action="<?= baseUrl('orders/store') ?>" method="POST" novalidate>
                <?= csrfField() ?>
                <input type="hidden" name="restaurant_id" value="<?= (int) $restaurant['id'] ?>">

                <div class="form-section-title">Customer</div>

                <div class="form-group">
                    <label for="customer_id" class="form-label">
                        Select Customer <span style="color: var(--color-error)">*</span>
                    </label>
                    <select id="customer_id" name="customer_id" class="form-input" required>
                        <option value="">-- Select a customer --</option>
                        <?php
                            $selectedCustomer = old('customer_id', '');
                            foreach ($customers as $c):
                                $fullName = e($c['first_name'] . ' ' . $c['last_name']);
                        ?>
                            <option value="<?= (int) $c['id'] ?>"
                                <?= (string) $c['id'] === (string) $selectedCustomer ? 'selected' : '' ?>>
                                <?= $fullName ?><?= !empty($c['email']) ? ' — ' . e($c['email']) : '' ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (!empty($errors['customer_id'])): ?>
                        <span class="form-error" style="display: block;"><?= e($errors['customer_id']) ?></span>
                    <?php else: ?>
                        <span class="form-error" id="customer_id-error"></span>
                    <?php endif; ?>
                </div>

                <div class="form-section-title">Order Details</div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="quantity" class="form-label">
                            Quantity <span style="color: var(--color-error)">*</span>
                        </label>
                        <input
                            type="number"
                            id="quantity"
                            name="quantity"
                            class="form-input"
                            placeholder="1"
                            min="1"
                            max="99"
                            value="<?= e(old('quantity', '1')) ?>"
                            data-unit-price="<?= e(number_format((float) $restaurant['combo_price'], 2, '.', '')) ?>"
                            required
                        >
                        <span class="form-error" id="quantity-error"></span>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Unit Price</label>
                        <input
                            type="text"
                            class="form-input"
                            value="$<?= e(number_format((float) $restaurant['combo_price'], 2)) ?>"
                            readonly
                            style="background: var(--color-bg-secondary); cursor: default;"
                        >
                    </div>
                </div>

                <div class="form-group">
                    <label for="notes" class="form-label">Notes <span style="color: var(--color-text-secondary); font-weight: 400;">(optional)</span></label>
                    <textarea
                        id="notes"
                        name="notes"
                        class="form-input"
                        rows="3"
                        placeholder="Special instructions, delivery notes..."
                        maxlength="500"
                    ><?= e(old('notes', '')) ?></textarea>
                    <span class="form-error" id="notes-error"></span>
                </div>

                <div style="display: flex; gap: var(--space-md); justify-content: flex-end; padding-top: var(--space-md);">
                    <a href="<?= baseUrl('orders/browse') ?>" class="btn btn-ghost">Cancel</a>
                    <button type="submit" class="btn btn-primary">
                        <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 3c1.93 0 3.5 1.57 3.5 3.5S13.93 13 12 13s-3.5-1.57-3.5-3.5S10.07 6 12 6zm7 13H5v-.23c0-.62.28-1.2.76-1.58C7.47 15.82 9.64 15 12 15s4.53.82 6.24 2.19c.48.38.76.97.76 1.58V19z"/></svg>
                        Place Order
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Order Summary Sidebar -->
    <div class="order-summary-sidebar">
        <div class="order-summary card">
            <div class="order-summary-title">Order Summary</div>

            <div class="order-summary-restaurant">
                <div class="order-summary-restaurant-name"><?= e($restaurant['name']) ?></div>
                <span class="food-type-chip"><?= e($restaurant['food_type']) ?></span>
            </div>

            <div class="order-summary-combo">
                <div class="order-summary-label">Combo</div>
                <div class="order-summary-combo-name"><?= e($restaurant['combo_name']) ?></div>
                <?php if (!empty($restaurant['combo_description'])): ?>
                    <div class="order-summary-combo-desc"><?= e($restaurant['combo_description']) ?></div>
                <?php endif; ?>
            </div>

            <div class="order-summary-breakdown">
                <div class="order-summary-row">
                    <span>Unit price</span>
                    <span>$<?= e(number_format((float) $restaurant['combo_price'], 2)) ?></span>
                </div>
                <div class="order-summary-row">
                    <span>Quantity</span>
                    <span id="summaryQty">1</span>
                </div>
                <div class="order-summary-total">
                    <span>Total</span>
                    <span id="summaryTotal">$<?= e(number_format((float) $restaurant['combo_price'], 2)) ?></span>
                </div>
            </div>
        </div>
    </div>
</div>
