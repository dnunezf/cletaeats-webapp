<?php $currentPage = 'register'; ?>

<div class="page-header">
    <h2 class="page-title">Register New User</h2>
</div>

<div class="customer-form-container">
    <div class="card">
        <form id="registerForm" action="<?= baseUrl('register') ?>" method="POST" novalidate>
            <?= csrfField() ?>

            <div class="form-row">
                <div class="form-group">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" id="username" name="username" class="form-input"
                           value="<?= e(old('username')) ?>" autocomplete="username" required>
                </div>
                <div class="form-group">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" id="email" name="email" class="form-input"
                           value="<?= e(old('email')) ?>" autocomplete="email" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="document" class="form-label">Document / ID</label>
                    <input type="text" id="document" name="document" class="form-input"
                           value="<?= e(old('document')) ?>" required>
                </div>
                <div class="form-group">
                    <label for="role" class="form-label">Role</label>
                    <select id="role" name="role" class="form-input">
                        <?php $sel = old('role', 'customer'); foreach (User::roles() as $r): ?>
                            <option value="<?= e($r) ?>" <?= $sel === $r ? 'selected' : '' ?>><?= e(ucfirst($r)) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="address" class="form-label">Address</label>
                <input type="text" id="address" name="address" class="form-input"
                       value="<?= e(old('address')) ?>" required>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="city" class="form-label">City</label>
                    <input type="text" id="city" name="city" class="form-input"
                           value="<?= e(old('city')) ?>" required>
                </div>
                <div class="form-group">
                    <label for="postal_code" class="form-label">Postal Code</label>
                    <input type="text" id="postal_code" name="postal_code" class="form-input"
                           value="<?= e(old('postal_code')) ?>" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="card_number" class="form-label">Card Number <small>(if customer)</small></label>
                    <input type="text" id="card_number" name="card_number" class="form-input"
                           value="<?= e(old('card_number')) ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" id="password" name="password"
                           class="form-input js-password" data-rules="#pwRulesRegister"
                           autocomplete="new-password" required>
                </div>
                <div class="form-group">
                    <label for="password_confirm" class="form-label">Confirm Password</label>
                    <input type="password" id="password_confirm" name="password_confirm" class="form-input" autocomplete="new-password" required>
                </div>
            </div>
            <div id="pwRulesRegister" class="form-group"><?php require BASE_PATH . '/views/partials/password-rules.php'; ?></div>

            <div style="display: flex; gap: var(--space-md); justify-content: flex-end; padding-top: var(--space-md);">
                <a href="<?= baseUrl('dashboard') ?>" class="btn btn-ghost">Cancel</a>
                <button type="submit" class="btn btn-primary">Register User</button>
            </div>
        </form>
    </div>
</div>
