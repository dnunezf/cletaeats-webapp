<?php
/** @var string     $formAction Controller-provided form target URL */
/** @var array|null $driver     Existing driver when editing, null when creating */
$formAction ??= '';
$driver     ??= null;
?>
<form id="driverForm" action="<?= e($formAction) ?>" method="POST" novalidate>
    <?= csrfField() ?>

    <?php if (!empty($driver)): ?>
        <input type="hidden" name="id" value="<?= (int) $driver['user_id'] ?>">
    <?php endif; ?>

    <div class="form-section-title">Account</div>

    <div class="form-row">
        <div class="form-group">
            <label for="username" class="form-label">Full Name (username) <span style="color: var(--color-error)">*</span></label>
            <input type="text" id="username" name="username" class="form-input"
                   value="<?= e(old('username', $driver['username'] ?? '')) ?>" required>
        </div>
        <div class="form-group">
            <label for="email" class="form-label">Email <span style="color: var(--color-error)">*</span></label>
            <input type="email" id="email" name="email" class="form-input"
                   value="<?= e(old('email', $driver['email'] ?? '')) ?>" required>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label for="document" class="form-label">Document / ID Number <span style="color: var(--color-error)">*</span></label>
            <input type="text" id="document" name="document" class="form-input"
                   value="<?= e(old('document', $driver['document'] ?? '')) ?>" required>
        </div>
        <div class="form-group">
            <label for="card_number" class="form-label">Card Number <span style="color: var(--color-error)">*</span></label>
            <input type="text" id="card_number" name="card_number" class="form-input"
                   value="<?= e(old('card_number', $driver['card_number'] ?? '')) ?>" required>
        </div>
    </div>

    <div class="form-section-title">Location</div>

    <div class="form-group">
        <label for="address" class="form-label">Address <span style="color: var(--color-error)">*</span></label>
        <input type="text" id="address" name="address" class="form-input"
               value="<?= e(old('address', $driver['address'] ?? '')) ?>" required>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label for="city" class="form-label">City <span style="color: var(--color-error)">*</span></label>
            <input type="text" id="city" name="city" class="form-input"
                   value="<?= e(old('city', $driver['city'] ?? '')) ?>" required>
        </div>
        <div class="form-group">
            <label for="postal_code" class="form-label">Postal Code <span style="color: var(--color-error)">*</span></label>
            <input type="text" id="postal_code" name="postal_code" class="form-input"
                   value="<?= e(old('postal_code', $driver['postal_code'] ?? '')) ?>" required>
        </div>
    </div>

    <div class="form-section-title">Operational</div>

    <div class="form-row">
        <div class="form-group">
            <label for="status" class="form-label">Availability <span style="color: var(--color-error)">*</span></label>
            <select id="status" name="status" class="form-input" required>
                <?php $sel = old('status', $driver['status'] ?? 'available'); foreach (DeliveryDriver::statuses() as $st): ?>
                    <option value="<?= e($st) ?>" <?= $sel === $st ? 'selected' : '' ?>><?= e(ucfirst($st)) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="penalties" class="form-label">Penalties <span style="color: var(--color-error)">*</span></label>
            <input type="number" id="penalties" name="penalties" class="form-input" min="0" max="99" step="1"
                   value="<?= e(old('penalties', isset($driver['penalties']) ? (string) $driver['penalties'] : '0')) ?>" required>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label for="km_cost_regular" class="form-label">Regular Cost / km <span style="color: var(--color-error)">*</span></label>
            <input type="number" id="km_cost_regular" name="km_cost_regular" class="form-input"
                   step="0.01" min="0" max="999999.99"
                   value="<?= e(old('km_cost_regular', isset($driver['km_cost_regular']) ? (string) $driver['km_cost_regular'] : '0')) ?>" required>
        </div>
        <div class="form-group">
            <label for="km_cost_holidays" class="form-label">Holiday Cost / km <span style="color: var(--color-error)">*</span></label>
            <input type="number" id="km_cost_holidays" name="km_cost_holidays" class="form-input"
                   step="0.01" min="0" max="999999.99"
                   value="<?= e(old('km_cost_holidays', isset($driver['km_cost_holidays']) ? (string) $driver['km_cost_holidays'] : '0')) ?>" required>
        </div>
    </div>

    <?php if (!empty($driver)): ?>
    <div class="form-row">
        <div class="form-group">
            <label for="user_status" class="form-label">Account Status</label>
            <select id="user_status" name="user_status" class="form-input">
                <?php foreach (User::statuses() as $us): ?>
                    <option value="<?= e($us) ?>" <?= ($driver['user_status'] ?? '') === $us ? 'selected' : '' ?>><?= e(ucfirst($us)) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    <?php endif; ?>

    <div class="form-section-title">Password</div>

    <div class="form-row">
        <div class="form-group">
            <label for="password" class="form-label">
                Password <?= empty($driver) ? '<span style="color: var(--color-error)">*</span>' : '<small>(leave blank to keep)</small>' ?>
            </label>
            <input type="password" id="password" name="password"
                   class="form-input js-password" data-rules="#pwRulesDriver"
                   <?= empty($driver) ? 'required' : '' ?>>
        </div>
        <div class="form-group">
            <label for="password_confirm" class="form-label">Confirm Password</label>
            <input type="password" id="password_confirm" name="password_confirm" class="form-input">
        </div>
    </div>
    <div id="pwRulesDriver" class="form-group"><?php require BASE_PATH . '/views/partials/password-rules.php'; ?></div>

    <div style="display: flex; gap: var(--space-md); justify-content: flex-end; padding-top: var(--space-md);">
        <a href="<?= baseUrl('drivers') ?>" class="btn btn-ghost">Cancel</a>
        <button type="submit" class="btn btn-primary">
            <?= !empty($driver) ? 'Update Driver' : 'Create Driver' ?>
        </button>
    </div>
</form>
