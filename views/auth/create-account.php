<?php $pageTitle = 'Create Account'; ?>

<div class="auth-wrapper">
    <div class="auth-card auth-card-wide">
        <div class="auth-logo">
            <div class="auth-logo-icon">
                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M11 9H9V2H7v7H5V2H3v7c0 2.12 1.66 3.84 3.75 3.97V22h2.5v-9.03C11.34 12.84 13 11.12 13 9V2h-2v7zm5-3v8h2.5v8H21V2c-2.76 0-5 2.24-5 4z"/>
                </svg>
            </div>
            <h1 class="auth-logo-title"><?= e(APP_NAME) ?></h1>
            <p class="auth-logo-subtitle">Food Delivery Management</p>
        </div>

        <h2 class="auth-title">Create your account</h2>
        <p class="auth-subtitle">Sign up as a customer to start ordering from your favorite restaurants.</p>

        <?php require BASE_PATH . '/views/partials/flash.php'; ?>

        <form id="createAccountForm" action="<?= baseUrl('create-account') ?>" method="POST" novalidate>
            <?= csrfField() ?>

            <div class="auth-section-title">Account</div>
            <div class="form-row">
                <div class="form-group">
                    <label for="username" class="form-label">Username <span class="form-required">*</span></label>
                    <div class="input-with-icon">
                        <svg class="input-icon" viewBox="0 0 24 24" width="18" height="18" aria-hidden="true">
                            <path fill="currentColor" d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                        </svg>
                        <input type="text" id="username" name="username" class="form-input"
                               placeholder="Choose a username"
                               value="<?= e(old('username')) ?>" autocomplete="username" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="email" class="form-label">Email <span class="form-required">*</span></label>
                    <div class="input-with-icon">
                        <svg class="input-icon" viewBox="0 0 24 24" width="18" height="18" aria-hidden="true">
                            <path fill="currentColor" d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
                        </svg>
                        <input type="email" id="email" name="email" class="form-input"
                               placeholder="you@example.com"
                               value="<?= e(old('email')) ?>" autocomplete="email" required>
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="document" class="form-label">Document / ID <span class="form-required">*</span></label>
                    <div class="input-with-icon">
                        <svg class="input-icon" viewBox="0 0 24 24" width="18" height="18" aria-hidden="true">
                            <path fill="currentColor" d="M20 4H4c-1.11 0-2 .89-2 2v12c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V6c0-1.11-.89-2-2-2zM9 7c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm6 11H3v-1c0-2 4-3.1 6-3.1s6 1.1 6 3.1v1zm3.85-3h-1.6c-.17-.59-1.05-1.15-1.95-1.4 1.25-.3 2.39-1 3.05-2 .27.62.27 1.32 0 1.95-.25.55-.84 1.07-1.5 1.45z"/>
                        </svg>
                        <input type="text" id="document" name="document" class="form-input"
                               placeholder="National ID / Tax number"
                               value="<?= e(old('document')) ?>" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="card_number" class="form-label">Card Number <span class="form-required">*</span></label>
                    <div class="input-with-icon">
                        <svg class="input-icon" viewBox="0 0 24 24" width="18" height="18" aria-hidden="true">
                            <path fill="currentColor" d="M20 4H4c-1.11 0-1.99.89-1.99 2L2 18c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V6c0-1.11-.89-2-2-2zm0 14H4v-6h16v6zm0-10H4V6h16v2z"/>
                        </svg>
                        <input type="text" id="card_number" name="card_number" class="form-input"
                               placeholder="Payment card number"
                               value="<?= e(old('card_number')) ?>" required>
                    </div>
                </div>
            </div>

            <div class="auth-section-title">Delivery Address</div>
            <div class="form-group">
                <label for="address" class="form-label">Address <span class="form-required">*</span></label>
                <div class="input-with-icon">
                    <svg class="input-icon" viewBox="0 0 24 24" width="18" height="18" aria-hidden="true">
                        <path fill="currentColor" d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                    </svg>
                    <input type="text" id="address" name="address" class="form-input"
                           placeholder="Street, number, apartment"
                           value="<?= e(old('address')) ?>" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="city" class="form-label">City <span class="form-required">*</span></label>
                    <input type="text" id="city" name="city" class="form-input"
                           placeholder="City"
                           value="<?= e(old('city')) ?>" required>
                </div>
                <div class="form-group">
                    <label for="postal_code" class="form-label">Postal Code <span class="form-required">*</span></label>
                    <input type="text" id="postal_code" name="postal_code" class="form-input"
                           placeholder="ZIP / Postal code"
                           value="<?= e(old('postal_code')) ?>" required>
                </div>
            </div>

            <div class="auth-section-title">Security</div>
            <div class="form-row">
                <div class="form-group">
                    <label for="password" class="form-label">Password <span class="form-required">*</span></label>
                    <div class="input-with-icon">
                        <svg class="input-icon" viewBox="0 0 24 24" width="18" height="18" aria-hidden="true">
                            <path fill="currentColor" d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z"/>
                        </svg>
                        <input type="password" id="password" name="password"
                               class="form-input js-password" data-rules="#pwRulesCreateAccount"
                               placeholder="Create a strong password"
                               autocomplete="new-password" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="password_confirm" class="form-label">Confirm Password <span class="form-required">*</span></label>
                    <div class="input-with-icon">
                        <svg class="input-icon" viewBox="0 0 24 24" width="18" height="18" aria-hidden="true">
                            <path fill="currentColor" d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z"/>
                        </svg>
                        <input type="password" id="password_confirm" name="password_confirm" class="form-input"
                               placeholder="Repeat your password"
                               autocomplete="new-password" required>
                    </div>
                </div>
            </div>
            <div id="pwRulesCreateAccount" class="form-group"><?php require BASE_PATH . '/views/partials/password-rules.php'; ?></div>

            <button type="submit" class="btn btn-primary btn-block btn-lg">Create Account</button>
        </form>

        <div class="auth-footer">
            <p>Already have an account? <a href="<?= baseUrl('login') ?>">Sign in</a></p>
        </div>
    </div>
</div>
