<?php
/** @var string     $formAction Controller-provided form target URL */
/** @var array|null $customer   Existing customer when editing, null when creating */
$formAction ??= '';
$customer   ??= null;
?>
<form id="customerForm" action="<?= e($formAction) ?>" method="POST" novalidate>
    <?= csrfField() ?>

    <?php if (!empty($customer)): ?>
        <input type="hidden" name="id" value="<?= (int) $customer['user_id'] ?>">
    <?php endif; ?>

    <div class="form-row">
        <div class="form-group">
            <label for="username" class="form-label">Name / Username <span style="color: var(--color-error)">*</span></label>
            <input type="text" id="username" name="username" class="form-input"
                   value="<?= e(old('username', $customer['username'] ?? '')) ?>" required>
            <span class="form-error" id="username-error"></span>
        </div>
        <div class="form-group">
            <label for="email" class="form-label">Email <span style="color: var(--color-error)">*</span></label>
            <input type="email" id="email" name="email" class="form-input"
                   value="<?= e(old('email', $customer['email'] ?? '')) ?>" required>
            <span class="form-error" id="email-error"></span>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label for="document" class="form-label">Document / ID <span style="color: var(--color-error)">*</span></label>
            <input type="text" id="document" name="document" class="form-input"
                   value="<?= e(old('document', $customer['document'] ?? '')) ?>" required>
            <span class="form-error" id="document-error"></span>
        </div>
        <div class="form-group">
            <label for="card_number" class="form-label">Card Number <span style="color: var(--color-error)">*</span></label>
            <input type="text" id="card_number" name="card_number" class="form-input"
                   value="<?= e(old('card_number', $customer['card_number'] ?? '')) ?>" required>
            <span class="form-error" id="card_number-error"></span>
        </div>
    </div>

    <div class="form-group">
        <label for="address" class="form-label">Address <span style="color: var(--color-error)">*</span></label>
        <input type="text" id="address" name="address" class="form-input"
               value="<?= e(old('address', $customer['address'] ?? '')) ?>" required>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label for="city" class="form-label">City <span style="color: var(--color-error)">*</span></label>
            <input type="text" id="city" name="city" class="form-input"
                   value="<?= e(old('city', $customer['city'] ?? '')) ?>" required>
        </div>
        <div class="form-group">
            <label for="postal_code" class="form-label">Postal Code <span style="color: var(--color-error)">*</span></label>
            <input type="text" id="postal_code" name="postal_code" class="form-input"
                   value="<?= e(old('postal_code', $customer['postal_code'] ?? '')) ?>" required>
        </div>
    </div>

    <?php if (!empty($customer)): ?>
    <div class="form-row">
        <div class="form-group">
            <label for="status" class="form-label">Status</label>
            <select name="status" id="status" class="form-input">
                <?php foreach (User::statuses() as $st): ?>
                    <option value="<?= e($st) ?>" <?= ($customer['status'] ?? '') === $st ? 'selected' : '' ?>><?= e(ucfirst($st)) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    <?php endif; ?>

    <div class="form-row">
        <div class="form-group">
            <label for="password" class="form-label">
                Password <?= empty($customer) ? '<span style="color: var(--color-error)">*</span>' : '<small>(leave blank to keep)</small>' ?>
            </label>
            <input type="password" id="password" name="password"
                   class="form-input js-password" data-rules="#pwRulesCustomer"
                   <?= empty($customer) ? 'required' : '' ?>>
            <span class="form-error" id="password-error"></span>
        </div>
        <div class="form-group">
            <label for="password_confirm" class="form-label">Confirm Password</label>
            <input type="password" id="password_confirm" name="password_confirm" class="form-input">
        </div>
    </div>
    <div id="pwRulesCustomer" class="form-group"><?php require BASE_PATH . '/views/partials/password-rules.php'; ?></div>

    <div style="display: flex; gap: var(--space-md); justify-content: flex-end; padding-top: var(--space-md);">
        <a href="<?= baseUrl('customers') ?>" class="btn btn-ghost">Cancel</a>
        <button type="submit" class="btn btn-primary">
            <?= !empty($customer) ? 'Update Customer' : 'Create Customer' ?>
        </button>
    </div>
</form>
