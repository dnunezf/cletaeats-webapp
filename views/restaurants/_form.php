<form id="restaurantForm" action="<?= e($formAction) ?>" method="POST" novalidate>
    <?= csrfField() ?>

    <?php if (!empty($restaurant)): ?>
        <input type="hidden" name="id" value="<?= (int) $restaurant['user_id'] ?>">
    <?php endif; ?>

    <div class="form-section-title">Account</div>

    <div class="form-row">
        <div class="form-group">
            <label for="username" class="form-label">Restaurant Name (username) <span style="color: var(--color-error)">*</span></label>
            <input type="text" id="username" name="username" class="form-input"
                   value="<?= e(old('username', $restaurant['username'] ?? '')) ?>" required>
            <span class="form-error" id="username-error"></span>
        </div>
        <div class="form-group">
            <label for="email" class="form-label">Email <span style="color: var(--color-error)">*</span></label>
            <input type="email" id="email" name="email" class="form-input"
                   value="<?= e(old('email', $restaurant['email'] ?? '')) ?>" required>
            <span class="form-error" id="email-error"></span>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label for="document" class="form-label">Legal Document / Tax ID <span style="color: var(--color-error)">*</span></label>
            <input type="text" id="document" name="document" class="form-input"
                   value="<?= e(old('document', $restaurant['document'] ?? '')) ?>" required>
            <span class="form-error" id="document-error"></span>
        </div>
        <div class="form-group">
            <label for="category" class="form-label">Category <span style="color: var(--color-error)">*</span></label>
            <select id="category" name="category" class="form-input" required>
                <option value="">-- Select category --</option>
                <?php $sel = old('category', $restaurant['category'] ?? ''); foreach (Restaurant::categories() as $cat): ?>
                    <option value="<?= e($cat) ?>" <?= $sel === $cat ? 'selected' : '' ?>><?= e(ucfirst($cat)) ?></option>
                <?php endforeach; ?>
            </select>
            <span class="form-error" id="category-error"></span>
        </div>
    </div>

    <div class="form-section-title">Location</div>

    <div class="form-group">
        <label for="address" class="form-label">Address <span style="color: var(--color-error)">*</span></label>
        <input type="text" id="address" name="address" class="form-input"
               value="<?= e(old('address', $restaurant['address'] ?? '')) ?>" required>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label for="city" class="form-label">City <span style="color: var(--color-error)">*</span></label>
            <input type="text" id="city" name="city" class="form-input"
                   value="<?= e(old('city', $restaurant['city'] ?? '')) ?>" required>
        </div>
        <div class="form-group">
            <label for="postal_code" class="form-label">Postal Code <span style="color: var(--color-error)">*</span></label>
            <input type="text" id="postal_code" name="postal_code" class="form-input"
                   value="<?= e(old('postal_code', $restaurant['postal_code'] ?? '')) ?>" required>
        </div>
    </div>

    <?php if (!empty($restaurant)): ?>
    <div class="form-row">
        <div class="form-group">
            <label for="status" class="form-label">Status</label>
            <select name="status" id="status" class="form-input">
                <?php foreach (User::statuses() as $st): ?>
                    <option value="<?= e($st) ?>" <?= ($restaurant['status'] ?? '') === $st ? 'selected' : '' ?>><?= e(ucfirst($st)) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    <?php endif; ?>

    <div class="form-section-title">Password</div>

    <div class="form-row">
        <div class="form-group">
            <label for="password" class="form-label">
                Password <?= empty($restaurant) ? '<span style="color: var(--color-error)">*</span>' : '<small>(leave blank to keep)</small>' ?>
            </label>
            <input type="password" id="password" name="password" class="form-input" <?= empty($restaurant) ? 'required' : '' ?>>
        </div>
        <div class="form-group">
            <label for="password_confirm" class="form-label">Confirm Password</label>
            <input type="password" id="password_confirm" name="password_confirm" class="form-input">
        </div>
    </div>

    <div style="display: flex; gap: var(--space-md); justify-content: flex-end; padding-top: var(--space-md);">
        <a href="<?= baseUrl('restaurants') ?>" class="btn btn-ghost">Cancel</a>
        <?php if (!empty($restaurant)): ?>
            <a href="<?= baseUrl('combos?restaurant_id=' . (int) $restaurant['user_id']) ?>" class="btn btn-outline">Manage Combos</a>
        <?php endif; ?>
        <button type="submit" class="btn btn-primary">
            <?= !empty($restaurant) ? 'Update Restaurant' : 'Create Restaurant' ?>
        </button>
    </div>
</form>
