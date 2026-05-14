<?php $pageTitle = 'Create Account'; ?>

<div class="auth-wrapper">
    <div class="auth-card">
        <div class="auth-logo">
            <div class="auth-logo-icon">
                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M11 9H9V2H7v7H5V2H3v7c0 2.12 1.66 3.84 3.75 3.97V22h2.5v-9.03C11.34 12.84 13 11.12 13 9V2h-2v7zm5-3v8h2.5v8H21V2c-2.76 0-5 2.24-5 4z"/>
                </svg>
            </div>
            <h1 class="auth-logo-title"><?= e(APP_NAME) ?></h1>
            <p class="auth-logo-subtitle">Food Delivery Management</p>
        </div>

        <h2 class="auth-title">Create Account</h2>

        <?php require BASE_PATH . '/views/partials/flash.php'; ?>

        <form id="createAccountForm" action="<?= baseUrl('create-account') ?>" method="POST" novalidate>
            <?= csrfField() ?>

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
            <div class="form-group">
                <label for="document" class="form-label">Document / ID</label>
                <input type="text" id="document" name="document" class="form-input"
                       value="<?= e(old('document')) ?>" required>
            </div>
            <div class="form-group">
                <label for="card_number" class="form-label">Card Number</label>
                <input type="text" id="card_number" name="card_number" class="form-input"
                       value="<?= e(old('card_number')) ?>" required>
            </div>
            <div class="form-group">
                <label for="address" class="form-label">Address</label>
                <input type="text" id="address" name="address" class="form-input"
                       value="<?= e(old('address')) ?>" required>
            </div>
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
            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <input type="password" id="password" name="password" class="form-input"
                       autocomplete="new-password" required>
            </div>
            <div class="form-group">
                <label for="password_confirm" class="form-label">Confirm Password</label>
                <input type="password" id="password_confirm" name="password_confirm" class="form-input"
                       autocomplete="new-password" required>
            </div>

            <button type="submit" class="btn btn-primary btn-block btn-lg">Create Account</button>
        </form>

        <div class="auth-footer">
            <p>Already have an account? <a href="<?= baseUrl('login') ?>">Sign in</a></p>
        </div>
    </div>
</div>
