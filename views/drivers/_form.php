<form id="driverForm" action="<?= e($formAction) ?>" method="POST" novalidate>
    <?= csrfField() ?>

    <?php if (!empty($driver)): ?>
        <input type="hidden" name="id" value="<?= (int) $driver['id'] ?>">
    <?php endif; ?>

    <div class="form-section-title">Personal Information</div>

    <div class="form-row">
        <div class="form-group">
            <label for="full_name" class="form-label">Full Name <span style="color: var(--color-error)">*</span></label>
            <input
                type="text"
                id="full_name"
                name="full_name"
                class="form-input"
                placeholder="Enter driver full name"
                value="<?= e(old('full_name', $driver['full_name'] ?? '')) ?>"
                required
            >
            <span class="form-error" id="full_name-error"></span>
        </div>

        <div class="form-group">
            <label for="id_number" class="form-label">ID Number <span style="color: var(--color-error)">*</span></label>
            <input
                type="text"
                id="id_number"
                name="id_number"
                class="form-input"
                placeholder="National / employee ID"
                value="<?= e(old('id_number', $driver['id_number'] ?? '')) ?>"
                required
            >
            <span class="form-error" id="id_number-error"></span>
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
                placeholder="driver@example.com"
                value="<?= e(old('email', $driver['email'] ?? '')) ?>"
                required
            >
            <span class="form-error" id="email-error"></span>
        </div>

        <div class="form-group">
            <label for="phone" class="form-label">Phone <span style="color: var(--color-error)">*</span></label>
            <input
                type="tel"
                id="phone"
                name="phone"
                class="form-input"
                placeholder="+1 555 000 0000"
                value="<?= e(old('phone', $driver['phone'] ?? '')) ?>"
                required
            >
            <span class="form-error" id="phone-error"></span>
        </div>
    </div>

    <div class="form-section-title">Address &amp; Payment</div>

    <div class="form-group">
        <label for="address" class="form-label">Full Address <span style="color: var(--color-error)">*</span></label>
        <input
            type="text"
            id="address"
            name="address"
            class="form-input"
            placeholder="Street, city, postal code"
            value="<?= e(old('address', $driver['address'] ?? '')) ?>"
            required
        >
        <span class="form-error" id="address-error"></span>
    </div>

    <div class="form-group">
        <label for="card_number" class="form-label">Card Number <span style="color: var(--color-error)">*</span></label>
        <input
            type="text"
            id="card_number"
            name="card_number"
            class="form-input"
            placeholder="13 to 19 digits"
            inputmode="numeric"
            autocomplete="off"
            value="<?= e(old('card_number', $driver['card_number'] ?? '')) ?>"
            required
        >
        <span class="form-error" id="card_number-error"></span>
    </div>

    <div class="form-section-title">Operational</div>

    <div class="form-row">
        <div class="form-group">
            <label for="status" class="form-label">Status <span style="color: var(--color-error)">*</span></label>
            <select id="status" name="status" class="form-input" required>
                <?php $selectedStatus = old('status', $driver['status'] ?? 'available'); ?>
                <?php foreach (DeliveryDriver::statuses() as $statusOption): ?>
                    <option value="<?= e($statusOption) ?>" <?= $selectedStatus === $statusOption ? 'selected' : '' ?>>
                        <?= e(ucfirst($statusOption)) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <span class="form-error" id="status-error"></span>
        </div>

        <div class="form-group">
            <label for="warning_count" class="form-label">Warning Count <span style="color: var(--color-error)">*</span></label>
            <input
                type="number"
                id="warning_count"
                name="warning_count"
                class="form-input"
                min="0"
                max="99"
                step="1"
                placeholder="0"
                value="<?= e(old('warning_count', isset($driver['warning_count']) ? (string) $driver['warning_count'] : '0')) ?>"
                required
            >
            <span class="form-error" id="warning_count-error"></span>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label for="order_distance" class="form-label">Order Distance (km) <span style="color: var(--color-error)">*</span></label>
            <input
                type="number"
                id="order_distance"
                name="order_distance"
                class="form-input"
                step="0.01"
                min="0"
                max="999999.99"
                placeholder="0.00"
                value="<?= e(old('order_distance', isset($driver['order_distance']) ? (string) $driver['order_distance'] : '0')) ?>"
                required
            >
            <span class="form-error" id="order_distance-error"></span>
        </div>

        <div class="form-group">
            <label for="daily_kilometers" class="form-label">Daily Kilometers <span style="color: var(--color-error)">*</span></label>
            <input
                type="number"
                id="daily_kilometers"
                name="daily_kilometers"
                class="form-input"
                step="0.01"
                min="0"
                max="999999.99"
                placeholder="0.00"
                value="<?= e(old('daily_kilometers', isset($driver['daily_kilometers']) ? (string) $driver['daily_kilometers'] : '0')) ?>"
                required
            >
            <span class="form-error" id="daily_kilometers-error"></span>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label for="weekday_cost_per_km" class="form-label">Weekday Cost / km <span style="color: var(--color-error)">*</span></label>
            <input
                type="number"
                id="weekday_cost_per_km"
                name="weekday_cost_per_km"
                class="form-input"
                step="0.01"
                min="0"
                max="999999.99"
                placeholder="0.00"
                value="<?= e(old('weekday_cost_per_km', isset($driver['weekday_cost_per_km']) ? (string) $driver['weekday_cost_per_km'] : '0')) ?>"
                required
            >
            <span class="form-error" id="weekday_cost_per_km-error"></span>
        </div>

        <div class="form-group">
            <label for="holiday_cost_per_km" class="form-label">Holiday Cost / km <span style="color: var(--color-error)">*</span></label>
            <input
                type="number"
                id="holiday_cost_per_km"
                name="holiday_cost_per_km"
                class="form-input"
                step="0.01"
                min="0"
                max="999999.99"
                placeholder="0.00"
                value="<?= e(old('holiday_cost_per_km', isset($driver['holiday_cost_per_km']) ? (string) $driver['holiday_cost_per_km'] : '0')) ?>"
                required
            >
            <span class="form-error" id="holiday_cost_per_km-error"></span>
        </div>
    </div>

    <div class="form-section-title">Complaints</div>

    <div class="form-group">
        <label for="complaints" class="form-label">Complaints Record</label>
        <textarea
            id="complaints"
            name="complaints"
            class="form-input"
            rows="4"
            maxlength="2000"
            placeholder="Optional. Log any complaints received about this driver."
        ><?= e(old('complaints', $driver['complaints'] ?? '')) ?></textarea>
        <span class="form-error" id="complaints-error"></span>
    </div>

    <div style="display: flex; gap: var(--space-md); justify-content: flex-end; padding-top: var(--space-md);">
        <a href="<?= baseUrl('drivers') ?>" class="btn btn-ghost">Cancel</a>
        <button type="submit" class="btn btn-primary">
            <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M17 3H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V7l-4-4zm-5 16c-1.66 0-3-1.34-3-3s1.34-3 3-3 3 1.34 3 3-1.34 3-3 3zm3-10H5V5h10v4z"/></svg>
            <?= !empty($driver) ? 'Update Driver' : 'Create Driver' ?>
        </button>
    </div>
</form>
