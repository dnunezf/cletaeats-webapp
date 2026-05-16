<?php
/** @var string     $formAction   Controller-provided form target URL */
/** @var array      $restaurants  Restaurant options for the picker (empty for the restaurant role) */
/** @var array|null $combo        Existing combo when editing, null when creating */
/** @var int        $restaurantId Pre-selected restaurant id (create flow) */
$formAction   ??= '';
$restaurants  ??= [];
$combo        ??= null;
$restaurantId ??= 0;
?>
<form id="comboForm" action="<?= e($formAction) ?>" method="POST" novalidate>
    <?= csrfField() ?>

    <?php if (!empty($combo)): ?>
        <input type="hidden" name="id" value="<?= (int) $combo['id'] ?>">
    <?php endif; ?>

    <?php if (userIsRestaurant()): ?>
        <input type="hidden" name="restaurant_id" value="<?= (int) (currentUserId() ?? 0) ?>">
    <?php else: ?>
    <div class="form-row">
        <div class="form-group">
            <label for="restaurant_id" class="form-label">Restaurant <span style="color: var(--color-error)">*</span></label>
            <select id="restaurant_id" name="restaurant_id" class="form-input" required <?= !empty($combo) ? 'disabled' : '' ?>>
                <option value="">-- Select a restaurant --</option>
                <?php
                    $sel = (int) old('restaurant_id', $combo['restaurant_id'] ?? ($restaurantId ?? 0));
                    foreach ($restaurants as $r):
                ?>
                    <option value="<?= (int) $r['user_id'] ?>" <?= (int) $r['user_id'] === $sel ? 'selected' : '' ?>>
                        <?= e($r['username']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php if (!empty($combo)): ?>
                <input type="hidden" name="restaurant_id" value="<?= (int) $combo['restaurant_id'] ?>">
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

    <div class="form-group">
        <label for="name" class="form-label">Combo Name <span style="color: var(--color-error)">*</span></label>
        <input type="text" id="name" name="name" class="form-input"
               value="<?= e(old('name', $combo['name'] ?? '')) ?>" required>
    </div>

    <div class="form-group">
        <label for="description" class="form-label">Description <span style="color: var(--color-error)">*</span></label>
        <textarea id="description" name="description" class="form-input" rows="3" required><?= e(old('description', $combo['description'] ?? '')) ?></textarea>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label for="price" class="form-label">Price <span style="color: var(--color-error)">*</span></label>
            <input type="number" id="price" name="price" class="form-input"
                   step="0.01" min="0" max="999999.99"
                   value="<?= e(old('price', isset($combo['price']) ? (string) $combo['price'] : '')) ?>" required>
        </div>
    </div>

    <div style="display: flex; gap: var(--space-md); justify-content: flex-end; padding-top: var(--space-md);">
        <a href="<?= baseUrl('combos') ?>" class="btn btn-ghost">Cancel</a>
        <button type="submit" class="btn btn-primary">
            <?= !empty($combo) ? 'Update Combo' : 'Create Combo' ?>
        </button>
    </div>
</form>
