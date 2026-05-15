<?php
/** @var array $profile  Joined user + location (+ card_number when customer) */
?>
<?php $currentPage = 'profile'; ?>
<?php $errors = getFlash('errors') ?? []; ?>
<?php $avatar = avatarUrl((int) $profile['id']); ?>

<div class="page-header">
    <h2 class="page-title">Profile Settings</h2>
</div>

<form action="<?= baseUrl('profile/update') ?>" method="POST" enctype="multipart/form-data" novalidate>
    <?= csrfField() ?>
    <div class="profile-layout">

        <!-- Photo card -->
        <aside class="card profile-photo-card">
            <div class="profile-photo">
                <?php if ($avatar): ?>
                    <img src="<?= e($avatar) ?>" alt="Profile photo">
                <?php else: ?>
                    <?= strtoupper(substr((string) $profile['username'], 0, 1)) ?>
                <?php endif; ?>
            </div>

            <div style="font-weight: 600;"><?= e($profile['username']) ?></div>
            <div class="profile-role-badge">
                <span class="badge badge-admin" style="text-transform: capitalize;"><?= e($profile['role']) ?></span>
            </div>

            <div class="profile-photo-actions">
                <label for="photo" class="btn btn-outline btn-sm" style="cursor: pointer;">Choose Photo</label>
                <input type="file" id="photo" name="photo" accept="image/jpeg,image/png,image/webp,image/gif" style="display: none;">
                <span id="photoFileName" class="profile-photo-hint">JPG, PNG, WEBP or GIF — max 2 MB.</span>
                <?php if ($avatar): ?>
                    <label class="profile-photo-hint" style="display: inline-flex; align-items: center; gap: 6px; justify-content: center;">
                        <input type="checkbox" name="remove_photo" value="1"> Remove current photo
                    </label>
                <?php endif; ?>
            </div>
            <?php if (!empty($errors['photo'])): ?>
                <div class="form-error" style="display: block; margin-top: var(--space-sm);"><?= e($errors['photo']) ?></div>
            <?php endif; ?>
        </aside>

        <!-- Form card -->
        <section class="card">
            <?php if (!empty($errors['general'])): ?>
                <div class="alert alert-error"><span><?= e($errors['general']) ?></span></div>
            <?php endif; ?>

            <div class="profile-section-title">Account</div>
            <div class="form-row">
                <div class="form-group">
                    <label for="username" class="form-label">Username <span style="color: var(--color-error)">*</span></label>
                    <input type="text" id="username" name="username" class="form-input"
                           value="<?= e(old('username', $profile['username'])) ?>" required>
                    <?php if (!empty($errors['username'])): ?>
                        <span class="form-error" style="display: block;"><?= e($errors['username']) ?></span>
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <label for="email" class="form-label">Email <span style="color: var(--color-error)">*</span></label>
                    <input type="email" id="email" name="email" class="form-input"
                           value="<?= e(old('email', $profile['email'])) ?>" required>
                    <?php if (!empty($errors['email'])): ?>
                        <span class="form-error" style="display: block;"><?= e($errors['email']) ?></span>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Read-only identity (admin-managed) -->
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Document</label>
                    <input type="text" class="form-input" value="<?= e($profile['document']) ?>" disabled>
                    <span class="profile-photo-hint">Managed by an administrator.</span>
                </div>
                <div class="form-group">
                    <label class="form-label">Role</label>
                    <input type="text" class="form-input" value="<?= e(ucfirst($profile['role'])) ?>" disabled>
                </div>
            </div>

            <?php if (($profile['role'] ?? '') === 'customer'): ?>
            <div class="form-group">
                <label for="card_number" class="form-label">Card Number</label>
                <input type="text" id="card_number" name="card_number" class="form-input"
                       value="<?= e(old('card_number', $profile['card_number'] ?? '')) ?>"
                       pattern="\d{13,19}" minlength="13" maxlength="19">
                <?php if (!empty($errors['card_number'])): ?>
                    <span class="form-error" style="display: block;">Card number must contain 13 to 19 digits.</span>
                <?php else: ?>
                    <span class="profile-photo-hint">13–19 digits.</span>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <div class="profile-section-title">Location</div>
            <div class="form-group">
                <label for="address" class="form-label">Address <span style="color: var(--color-error)">*</span></label>
                <input type="text" id="address" name="address" class="form-input"
                       value="<?= e(old('address', $profile['address'])) ?>" required>
                <?php if (!empty($errors['address'])): ?>
                    <span class="form-error" style="display: block;"><?= e($errors['address']) ?></span>
                <?php endif; ?>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="city" class="form-label">City <span style="color: var(--color-error)">*</span></label>
                    <input type="text" id="city" name="city" class="form-input"
                           value="<?= e(old('city', $profile['city'])) ?>" required>
                    <?php if (!empty($errors['city'])): ?>
                        <span class="form-error" style="display: block;"><?= e($errors['city']) ?></span>
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <label for="postal_code" class="form-label">Postal Code <span style="color: var(--color-error)">*</span></label>
                    <input type="text" id="postal_code" name="postal_code" class="form-input"
                           value="<?= e(old('postal_code', $profile['postal_code'])) ?>" required>
                    <?php if (!empty($errors['postal_code'])): ?>
                        <span class="form-error" style="display: block;"><?= e($errors['postal_code']) ?></span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="profile-section-title">Change Password</div>
            <p style="font-size: var(--font-size-sm); color: var(--color-text-secondary); margin: 0 0 var(--space-md);">
                Leave both fields empty to keep your current password.
            </p>
            <div class="form-row">
                <div class="form-group">
                    <label for="password" class="form-label">New Password</label>
                    <input type="password" id="password" name="password"
                           class="form-input js-password" data-rules="#pwRulesProfile"
                           autocomplete="new-password">
                </div>
                <div class="form-group">
                    <label for="password_confirm" class="form-label">Confirm New Password</label>
                    <input type="password" id="password_confirm" name="password_confirm" class="form-input" autocomplete="new-password">
                </div>
            </div>
            <div id="pwRulesProfile" class="form-group"><?php require BASE_PATH . '/views/partials/password-rules.php'; ?></div>
            <?php if (!empty($errors['password'])): ?>
                <span class="form-error" style="display: block;"><?= e($errors['password']) ?></span>
            <?php endif; ?>

            <div style="display: flex; gap: var(--space-md); justify-content: flex-end; padding-top: var(--space-md);">
                <a href="<?= baseUrl('dashboard') ?>" class="btn btn-ghost">Cancel</a>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </section>
    </div>
</form>

<script>
    // Show the chosen filename inline next to the file input.
    document.addEventListener('DOMContentLoaded', () => {
        const input = document.getElementById('photo');
        const label = document.getElementById('photoFileName');
        if (input && label) {
            const original = label.textContent;
            input.addEventListener('change', () => {
                label.textContent = input.files && input.files[0]
                    ? input.files[0].name + ' selected'
                    : original;
            });
        }
    });
</script>
