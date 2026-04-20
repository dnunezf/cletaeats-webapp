<form id="restaurantForm" action="<?= e($formAction) ?>" method="POST" novalidate>
    <?= csrfField() ?>

    <?php if (!empty($restaurant)): ?>
        <input type="hidden" name="id" value="<?= (int) $restaurant['id'] ?>">
    <?php endif; ?>

    <div class="form-section-title">Basic Information</div>

    <div class="form-row">
        <div class="form-group">
            <label for="name" class="form-label">Restaurant Name <span style="color: var(--color-error)">*</span></label>
            <input
                type="text"
                id="name"
                name="name"
                class="form-input"
                placeholder="Enter restaurant name"
                value="<?= e(old('name', $restaurant['name'] ?? '')) ?>"
                required
            >
            <span class="form-error" id="name-error"></span>
        </div>

        <div class="form-group">
            <label for="legal_id" class="form-label">Legal ID <span style="color: var(--color-error)">*</span></label>
            <input
                type="text"
                id="legal_id"
                name="legal_id"
                class="form-input"
                placeholder="Juridical / tax ID"
                value="<?= e(old('legal_id', $restaurant['legal_id'] ?? '')) ?>"
                required
            >
            <span class="form-error" id="legal_id-error"></span>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label for="food_type" class="form-label">Food Type <span style="color: var(--color-error)">*</span></label>
            <select id="food_type" name="food_type" class="form-input" required>
                <option value="">-- Select food type --</option>
                <?php
                    $selectedType = old('food_type', $restaurant['food_type'] ?? '');
                    foreach (Restaurant::foodTypes() as $type):
                ?>
                    <option value="<?= e($type) ?>" <?= $selectedType === $type ? 'selected' : '' ?>>
                        <?= e($type) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <span class="form-error" id="food_type-error"></span>
        </div>

        <div class="form-group">
            <label for="address" class="form-label">Address <span style="color: var(--color-error)">*</span></label>
            <input
                type="text"
                id="address"
                name="address"
                class="form-input"
                placeholder="Street address"
                value="<?= e(old('address', $restaurant['address'] ?? '')) ?>"
                required
            >
            <span class="form-error" id="address-error"></span>
        </div>
    </div>

    <div class="form-section-title">Combo Reference</div>

    <div class="form-row">
        <div class="form-group">
            <label for="combo_name" class="form-label">Combo Name <span style="color: var(--color-error)">*</span></label>
            <input
                type="text"
                id="combo_name"
                name="combo_name"
                class="form-input"
                placeholder="e.g. Family Combo"
                value="<?= e(old('combo_name', $restaurant['combo_name'] ?? '')) ?>"
                required
            >
            <span class="form-error" id="combo_name-error"></span>
        </div>

        <div class="form-group">
            <label for="combo_price" class="form-label">Combo Price <span style="color: var(--color-error)">*</span></label>
            <input
                type="number"
                id="combo_price"
                name="combo_price"
                class="form-input"
                placeholder="0.00"
                step="0.01"
                min="0"
                max="999999.99"
                value="<?= e(old('combo_price', isset($restaurant['combo_price']) ? (string) $restaurant['combo_price'] : '')) ?>"
                required
            >
            <span class="form-error" id="combo_price-error"></span>
        </div>
    </div>

    <div class="form-group">
        <label for="combo_description" class="form-label">Combo Description</label>
        <textarea
            id="combo_description"
            name="combo_description"
            class="form-input"
            rows="3"
            placeholder="Short description of what the combo includes"
        ><?= e(old('combo_description', $restaurant['combo_description'] ?? '')) ?></textarea>
        <span class="form-error" id="combo_description-error"></span>
    </div>

    <div style="display: flex; gap: var(--space-md); justify-content: flex-end; padding-top: var(--space-md);">
        <a href="<?= baseUrl('restaurants') ?>" class="btn btn-ghost">Cancel</a>
        <button type="submit" class="btn btn-primary">
            <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M17 3H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V7l-4-4zm-5 16c-1.66 0-3-1.34-3-3s1.34-3 3-3 3 1.34 3 3-1.34 3-3 3zm3-10H5V5h10v4z"/></svg>
            <?= !empty($restaurant) ? 'Update Restaurant' : 'Create Restaurant' ?>
        </button>
    </div>
</form>
