/**
 * Global application scripts: nav drawer toggle, flash auto-dismiss.
 */
document.addEventListener('DOMContentLoaded', () => {
    const hamburgerBtn = document.getElementById('hamburgerBtn');
    const navOverlay = document.getElementById('navOverlay');

    // Toggle nav drawer
    if (hamburgerBtn) {
        hamburgerBtn.addEventListener('click', () => {
            document.body.classList.toggle('nav-open');
        });
    }

    // Close nav on overlay click
    if (navOverlay) {
        navOverlay.addEventListener('click', () => {
            document.body.classList.remove('nav-open');
        });
    }

    // Close nav on Escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            document.body.classList.remove('nav-open');
        }
    });

    // Auto-dismiss flash messages
    document.querySelectorAll('.alert-close').forEach(btn => {
        btn.addEventListener('click', () => {
            const alert = btn.closest('.alert');
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-10px)';
            setTimeout(() => alert.remove(), 300);
        });
    });

    document.querySelectorAll('.alert').forEach(alert => {
        setTimeout(() => {
            if (alert.parentElement) {
                alert.style.opacity = '0';
                alert.style.transform = 'translateY(-10px)';
                setTimeout(() => alert.remove(), 300);
            }
        }, 6000);
    });

    // Clear field validation error on input
    document.querySelectorAll('.form-input').forEach(input => {
        input.addEventListener('input', () => {
            input.classList.remove('is-invalid');
            const errorEl = document.getElementById(`${input.name}-error`);
            if (errorEl) errorEl.textContent = '';
        });
    });
});

/**
 * Returns the base URL from meta tag.
 */
function getBaseUrl() {
    const meta = document.querySelector('meta[name="base-url"]');
    return meta ? meta.content : '';
}
