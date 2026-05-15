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
                    <input type="text" id="username" name="username" class="form-input"
                           value="<?= e(old('username', $user['username'])) ?>" required>
                </div>
                <div class="form-group">
                    <label for="email" class="form-label">Email <span style="color: var(--color-error)">*</span></label>
                    <input type="email" id="email" name="email" class="form-input"
                           value="<?= e(old('email', $user['email'])) ?>" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="role" class="form-label">Role <span style="color: var(--color-error)">*</span></label>
                    <select id="role" name="role" class="form-input" required <?= $isSelf ? 'disabled' : '' ?>>
                        <?php $sel = old('role', $user['role']); foreach (User::roles() as $r): ?>
                            <option value="<?= e($r) ?>" <?= $sel === $r ? 'selected' : '' ?>><?= e(ucfirst($r)) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?php if ($isSelf): ?>
                        <input type="hidden" name="role" value="<?= e($user['role']) ?>">
                        <small style="color: var(--color-text-secondary);">You cannot change your own role.</small>
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <label for="status" class="form-label">Status <span style="color: var(--color-error)">*</span></label>
                    <select id="status" name="status" class="form-input" required <?= $isSelf ? 'disabled' : '' ?>>
                        <?php $sel = old('status', $user['status']); foreach (User::statuses() as $s): ?>
                            <option value="<?= e($s) ?>" <?= $sel === $s ? 'selected' : '' ?>><?= e(ucfirst($s)) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?php if ($isSelf): ?>
                        <input type="hidden" name="status" value="<?= e($user['status']) ?>">
                    <?php endif; ?>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="document" class="form-label">Document <span style="color: var(--color-error)">*</span></label>
                    <input type="text" id="document" name="document" class="form-input"
                           value="<?= e(old('document', $user['document'] ?? '')) ?>" required>
                </div>
            </div>

            <div class="form-section-title">Location</div>
            <div class="form-group">
                <label for="address" class="form-label">Address <span style="color: var(--color-error)">*</span></label>
                <input type="text" id="address" name="address" class="form-input"
                       value="<?= e(old('address', $user['address'] ?? '')) ?>" required>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="city" class="form-label">City <span style="color: var(--color-error)">*</span></label>
                    <input type="text" id="city" name="city" class="form-input"
                           value="<?= e(old('city', $user['city'] ?? '')) ?>" required>
                </div>
                <div class="form-group">
                    <label for="postal_code" class="form-label">Postal Code <span style="color: var(--color-error)">*</span></label>
                    <input type="text" id="postal_code" name="postal_code" class="form-input"
                           value="<?= e(old('postal_code', $user['postal_code'] ?? '')) ?>" required>
                </div>
            </div>

            <div class="form-section-title">Change Password</div>
            <p style="color: var(--color-text-secondary); font-size: var(--font-size-sm); margin-bottom: var(--space-md);">
                Leave blank to keep the current password.
            </p>
            <div class="form-row">
                <div class="form-group">
                    <label for="password" class="form-label">New Password</label>
                    <input type="password" id="password" name="password"
                           class="form-input js-password" data-rules="#pwRulesUserEdit"
                           autocomplete="new-password">
                </div>
                <div class="form-group">
                    <label for="password_confirm" class="form-label">Confirm New Password</label>
                    <input type="password" id="password_confirm" name="password_confirm" class="form-input" autocomplete="new-password">
                </div>
            </div>
            <div id="pwRulesUserEdit" class="form-group"><?php require BASE_PATH . '/views/partials/password-rules.php'; ?></div>

            <div style="display: flex; gap: var(--space-md); justify-content: flex-end; padding-top: var(--space-md);">
                <a href="<?= baseUrl('users') ?>" class="btn btn-ghost">Cancel</a>
                <button type="submit" class="btn btn-primary">Update User</button>
            </div>
        </form>
    </div>
</div>
