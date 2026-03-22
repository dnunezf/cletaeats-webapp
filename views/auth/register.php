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
                    <input
                        type="text"
                        id="username"
                        name="username"
                        class="form-input"
                        placeholder="Enter username"
                        value="<?= e(old('username')) ?>"
                        autocomplete="username"
                        required
                    >
                    <span class="form-error" id="username-error"></span>
                </div>

                <div class="form-group">
                    <label for="email" class="form-label">Email</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        class="form-input"
                        placeholder="Enter email address"
                        value="<?= e(old('email')) ?>"
                        autocomplete="email"
                        required
                    >
                    <span class="form-error" id="email-error"></span>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="form-input"
                        placeholder="Minimum 8 characters"
                        autocomplete="new-password"
                        required
                    >
                    <span class="form-error" id="password-error"></span>
                </div>

                <div class="form-group">
                    <label for="password_confirm" class="form-label">Confirm Password</label>
                    <input
                        type="password"
                        id="password_confirm"
                        name="password_confirm"
                        class="form-input"
                        placeholder="Repeat password"
                        autocomplete="new-password"
                        required
                    >
                    <span class="form-error" id="password_confirm-error"></span>
                </div>
            </div>

            <div class="form-group">
                <label for="role" class="form-label">Role</label>
                <select id="role" name="role" class="form-select">
                    <option value="user" <?= old('role', 'user') === 'user' ? 'selected' : '' ?>>User</option>
                    <option value="admin" <?= old('role') === 'admin' ? 'selected' : '' ?>>Admin</option>
                </select>
            </div>

            <div style="display: flex; gap: var(--space-md); justify-content: flex-end; padding-top: var(--space-md);">
                <a href="<?= baseUrl('dashboard') ?>" class="btn btn-ghost">Cancel</a>
                <button type="submit" class="btn btn-primary">
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M15 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm-9-2V7H4v3H1v2h3v3h2v-3h3v-2H6zm9 4c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                    Register User
                </button>
            </div>
        </form>
    </div>
</div>
