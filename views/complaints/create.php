<?php $currentPage = 'orders'; ?>

<div class="page-header">
    <h2 class="page-title">File Complaint — Order #<?= (int) $orderId ?></h2>
    <a href="<?= baseUrl('orders/show?id=' . (int) $orderId) ?>" class="btn btn-ghost">Back to Order</a>
</div>

<div class="customer-form-container">
    <div class="card">
        <form action="<?= baseUrl('complaints/store') ?>" method="POST" novalidate>
            <?= csrfField() ?>
            <input type="hidden" name="order_id" value="<?= (int) $orderId ?>">

            <div class="form-group">
                <label for="content" class="form-label">What went wrong? <span style="color: var(--color-error)">*</span></label>
                <textarea id="content" name="content" class="form-input" rows="4"
                          maxlength="255" required><?= e(old('content')) ?></textarea>
            </div>

            <div class="form-group">
                <label for="rating" class="form-label">Rating (1–5) <span style="color: var(--color-error)">*</span></label>
                <select id="rating" name="rating" class="form-input" required>
                    <?php $sel = (int) old('rating', 3); for ($i = 1; $i <= 5; $i++): ?>
                        <option value="<?= $i ?>" <?= $sel === $i ? 'selected' : '' ?>><?= $i ?></option>
                    <?php endfor; ?>
                </select>
            </div>

            <div style="display: flex; gap: var(--space-md); justify-content: flex-end; padding-top: var(--space-md);">
                <a href="<?= baseUrl('orders/show?id=' . (int) $orderId) ?>" class="btn btn-ghost">Cancel</a>
                <button type="submit" class="btn btn-primary">Submit Complaint</button>
            </div>
        </form>
    </div>
</div>
