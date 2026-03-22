/**
 * Client-side validation for login and register forms.
 */
document.addEventListener('DOMContentLoaded', () => {

    // Login form validation
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', (e) => {
            const v = new FormValidator(loginForm);
            const username = loginForm.querySelector('[name="username"]').value;
            const password = loginForm.querySelector('[name="password"]').value;

            v.required(username, 'username', 'Username')
             .minLength(username, 3, 'username', 'Username')
             .required(password, 'password', 'Password')
             .minLength(password, 6, 'password', 'Password');

            if (!v.isValid()) {
                e.preventDefault();
                v.showErrors();
            }
        });
    }

    // Register form validation
    const registerForm = document.getElementById('registerForm');
    if (registerForm) {
        registerForm.addEventListener('submit', (e) => {
            const v = new FormValidator(registerForm);
            const username = registerForm.querySelector('[name="username"]').value;
            const email = registerForm.querySelector('[name="email"]').value;
            const password = registerForm.querySelector('[name="password"]').value;
            const passwordConfirm = registerForm.querySelector('[name="password_confirm"]').value;

            v.required(username, 'username', 'Username')
             .alphanumeric(username, 'username', 'Username')
             .minLength(username, 3, 'username', 'Username')
             .maxLength(username, 50, 'username', 'Username')
             .required(email, 'email', 'Email')
             .email(email, 'email')
             .required(password, 'password', 'Password')
             .minLength(password, 8, 'password', 'Password')
             .maxLength(password, 72, 'password', 'Password')
             .required(passwordConfirm, 'password_confirm', 'Password confirmation')
             .matches(password, passwordConfirm, 'password_confirm', 'Password');

            if (!v.isValid()) {
                e.preventDefault();
                v.showErrors();
            }
        });
    }

    // Auto-dismiss flash messages
    document.querySelectorAll('.alert-close').forEach(btn => {
        btn.addEventListener('click', () => {
            const alert = btn.closest('.alert');
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-10px)';
            setTimeout(() => alert.remove(), 300);
        });
    });

    // Auto-dismiss after 6 seconds
    document.querySelectorAll('.alert').forEach(alert => {
        setTimeout(() => {
            if (alert.parentElement) {
                alert.style.opacity = '0';
                alert.style.transform = 'translateY(-10px)';
                setTimeout(() => alert.remove(), 300);
            }
        }, 6000);
    });

    // Clear field error on input
    document.querySelectorAll('.form-input').forEach(input => {
        input.addEventListener('input', () => {
            input.classList.remove('is-invalid');
            const errorEl = document.getElementById(`${input.name}-error`);
            if (errorEl) errorEl.textContent = '';
        });
    });
});
