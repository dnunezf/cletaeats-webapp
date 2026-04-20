<?php $currentPage = 'users'; ?>
<?php $currentUserId = (int) ($_SESSION['user_id'] ?? 0); ?>
<?php $isSelf = ((int) $user['id'] === $currentUserId); ?>

<div class="page-header">
    <h2 class="page-title">Edit User</h2>
</div>

<div class="customer-form-container">
    <div class="card">
        <form id="userForm" action="<?= e($formAction) ?>" method="POST" novalidate>
            <?= csrfField() ?>
            <input type="hidden" name="id" value="<?= (int) $user['id'] ?>">

            <div class="form-section-title">Account</div>

            <div class="form-row">
                <div class="form-group">
                    <label for="username" class="form-label">Username <span style="color: var(--color-error)">*</span></label>
                    <input
                        type="text"
                        id="username"
                        name="username"
                        class="form-input"
                        placeholder="Enter username"
                        value="<?= e(old('username', $user['username'])) ?>"
                        required
                    >
                    <span class="form-error" id="username-error"></span>
                </div>

                <div class="form-group">
                    <label for="email" class="form-label">Email <span style="color: var(--color-error)">*</span></label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        class="form-input"
                        placeholder="user@example.com"
                        value="<?= e(old('email', $user['email'])) ?>"
                        required
                    >
                    <span class="form-error" id="email-error"></span>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="role" class="form-label">Role <span style="color: var(--color-error)">*</span></label>
                    <select id="role" name="role" class="form-input" required <?= $isSelf ? 'disabled' : '' ?>>
                        <?php $currentRole = old('role', $user['role']); ?>
                        <option value="user"  <?= $currentRole === 'user'  ? 'selected' : '' ?>>User</option>
                        <option value="admin" <?= $currentRole === 'admin' ? 'selected' : '' ?>>Admin</option>
                    </select>
                    <?php if ($isSelf): ?>
                        <input type="hidden" name="role" value="<?= e($user['role']) ?>">
                        <small style="color: var(--color-text-secondary);">You cannot change your own role.</small>
                    <?php endif; ?>
                    <span class="form-error" id="role-error"></span>
                </div>

                <div class="form-group">
                    <label for="status" class="form-label">Status <span style="color: var(--color-error)">*</span></label>
                    <select id="status" name="status" class="form-input" required>
                        <?php $currentStatus = old('status', $user['status']); ?>
                        <option value="active"  <?= $currentStatus === 'active'  ? 'selected' : '' ?>>Active</option>
                        <option value="pending" <?= $currentStatus === 'pending' ? 'selected' : '' ?>>Pending</option>
                    </select>
                    <span class="form-error" id="status-error"></span>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" style="display: flex; align-items: center; gap: var(--space-sm);">
                    <?php $isActive = (int) old('is_active', (string) $user['is_active']) === 1; ?>
                    <input
                        type="checkbox"
                        id="is_active"
                        name="is_active"
                        value="1"
                        <?= $isActive ? 'checked' : '' ?>
                        <?= $isSelf ? 'disabled' : '' ?>
                    >
                    Account is active
                </label>
                <?php if ($isSelf): ?>
                    <input type="hidden" name="is_active" value="1">
                    <small style="color: var(--color-text-secondary);">You cannot deactivate your own account.</small>
                <?php endif; ?>
            </div>

            <div class="form-section-title">Change Password</div>
            <p style="color: var(--color-text-secondary); font-size: var(--font-size-sm); margin-bottom: var(--space-md);">
                Leave blank to keep the current password.
            </p>

            <div class="form-row">
                <div class="form-group">
                    <label for="password" class="form-label">New Password</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="form-input"
                        placeholder="At least 8 characters"
                        autocomplete="new-password"
                    >
                    <span class="form-error" id="password-error"></span>
                </div>

                <div class="form-group">
                    <label for="password_confirm" class="form-label">Confirm New Password</label>
                    <input
                        type="password"
                        id="password_confirm"
                        name="password_confirm"
                        class="form-input"
                        placeholder="Repeat new password"
                        autocomplete="new-password"
                    >
                    <span class="form-error" id="password_confirm-error"></span>
                </div>
            </div>

            <div style="display: flex; gap: var(--space-md); justify-content: flex-end; padding-top: var(--space-md);">
                <a href="<?= baseUrl('users') ?>" class="btn btn-ghost">Cancel</a>
                <button type="submit" class="btn btn-primary">
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M17 3H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V7l-4-4zm-5 16c-1.66 0-3-1.34-3-3s1.34-3 3-3 3 1.34 3 3-1.34 3-3 3zm3-10H5V5h10v4z"/></svg>
                    Update User
                </button>
            </div>
        </form>
    </div>
</div>
