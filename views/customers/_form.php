<form id="customerForm" action="<?= e($formAction) ?>" method="POST" novalidate>
    <?= csrfField() ?>

    <?php if (!empty($customer)): ?>
        <input type="hidden" name="id" value="<?= (int) $customer['id'] ?>">
    <?php endif; ?>

    <div class="form-row">
        <div class="form-group">
            <label for="first_name" class="form-label">First Name <span style="color: var(--color-error)">*</span></label>
            <input
                type="text"
                id="first_name"
                name="first_name"
                class="form-input"
                placeholder="Enter first name"
                value="<?= e(old('first_name', $customer['first_name'] ?? '')) ?>"
                required
            >
            <span class="form-error" id="first_name-error"></span>
        </div>

        <div class="form-group">
            <label for="last_name" class="form-label">Last Name <span style="color: var(--color-error)">*</span></label>
            <input
                type="text"
                id="last_name"
                name="last_name"
                class="form-input"
                placeholder="Enter last name"
                value="<?= e(old('last_name', $customer['last_name'] ?? '')) ?>"
                required
            >
            <span class="form-error" id="last_name-error"></span>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label for="email" class="form-label">Email <span style="color: var(--color-error)">*</span></label>
            <input
                type="email"
                id="email"
                name="email"
                class="form-input"
                placeholder="customer@example.com"
                value="<?= e(old('email', $customer['email'] ?? '')) ?>"
                required
            >
            <span class="form-error" id="email-error"></span>
        </div>

        <div class="form-group">
            <label for="phone_number" class="form-label">Phone Number</label>
            <input
                type="tel"
                id="phone_number"
                name="phone_number"
                class="form-input"
                placeholder="+1 234 567 8900"
                value="<?= e(old('phone_number', $customer['phone_number'] ?? '')) ?>"
            >
            <span class="form-error" id="phone_number-error"></span>
        </div>
    </div>

    <div class="form-group">
        <label for="address" class="form-label">Address</label>
        <input
            type="text"
            id="address"
            name="address"
            class="form-input"
            placeholder="Street address"
            value="<?= e(old('address', $customer['address'] ?? '')) ?>"
        >
    </div>

    <div class="form-row">
        <div class="form-group">
            <label for="city" class="form-label">City</label>
            <input
                type="text"
                id="city"
                name="city"
                class="form-input"
                placeholder="City"
                value="<?= e(old('city', $customer['city'] ?? '')) ?>"
            >
        </div>

        <div class="form-group">
            <label for="postal_code" class="form-label">Postal Code</label>
            <input
                type="text"
                id="postal_code"
                name="postal_code"
                class="form-input"
                placeholder="Postal code"
                value="<?= e(old('postal_code', $customer['postal_code'] ?? '')) ?>"
            >
            <span class="form-error" id="postal_code-error"></span>
        </div>
    </div>

    <div style="display: flex; gap: var(--space-md); justify-content: flex-end; padding-top: var(--space-md);">
        <a href="<?= baseUrl('customers') ?>" class="btn btn-ghost">Cancel</a>
        <button type="submit" class="btn btn-primary">
            <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M17 3H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V7l-4-4zm-5 16c-1.66 0-3-1.34-3-3s1.34-3 3-3 3 1.34 3 3-1.34 3-3 3zm3-10H5V5h10v4z"/></svg>
            <?= !empty($customer) ? 'Update Customer' : 'Create Customer' ?>
        </button>
    </div>
</form>
