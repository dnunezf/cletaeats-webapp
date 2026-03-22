/**
 * Reusable client-side form validation utility.
 */
class FormValidator {
    constructor(form) {
        this.form = form;
        this.errors = {};
    }

    required(value, field, label) {
        if (!value || value.trim() === '') {
            this.addError(field, `${label} is required.`);
        }
        return this;
    }

    email(value, field) {
        const pattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (value && !pattern.test(value)) {
            this.addError(field, 'Please enter a valid email address.');
        }
        return this;
    }

    minLength(value, min, field, label) {
        if (value && value.trim().length < min) {
            this.addError(field, `${label} must be at least ${min} characters.`);
        }
        return this;
    }

    maxLength(value, max, field, label) {
        if (value && value.trim().length > max) {
            this.addError(field, `${label} must not exceed ${max} characters.`);
        }
        return this;
    }

    matches(value1, value2, field, label) {
        if (value1 !== value2) {
            this.addError(field, `${label} confirmation does not match.`);
        }
        return this;
    }

    phone(value, field) {
        const pattern = /^[+]?[\d\s\-()]{7,20}$/;
        if (value && !pattern.test(value)) {
            this.addError(field, 'Please enter a valid phone number.');
        }
        return this;
    }

    alphanumeric(value, field, label) {
        const pattern = /^[a-zA-Z0-9_]+$/;
        if (value && !pattern.test(value)) {
            this.addError(field, `${label} may only contain letters, numbers, and underscores.`);
        }
        return this;
    }

    addError(field, message) {
        if (!this.errors[field]) {
            this.errors[field] = message;
        }
    }

    isValid() {
        return Object.keys(this.errors).length === 0;
    }

    showErrors() {
        // Clear previous errors
        this.clearErrors();

        for (const [field, message] of Object.entries(this.errors)) {
            const input = this.form.querySelector(`[name="${field}"]`);
            const errorEl = this.form.querySelector(`#${field}-error`);

            if (input) {
                input.classList.add('is-invalid');
            }
            if (errorEl) {
                errorEl.textContent = message;
            }
        }
    }

    clearErrors() {
        this.errors = {};

        this.form.querySelectorAll('.is-invalid').forEach(el => {
            el.classList.remove('is-invalid');
        });

        this.form.querySelectorAll('.form-error').forEach(el => {
            el.textContent = '';
        });
    }

    reset() {
        this.errors = {};
    }
}
