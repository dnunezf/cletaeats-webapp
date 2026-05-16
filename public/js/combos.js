/**
 * Combo module scripts: delete modal, AJAX delete with HTML fallback.
 *
 * Authorization is enforced server-side (route middleware role:admin,restaurant
 * + ComboController::delete ownership check). This script only handles UX.
 */
document.addEventListener('DOMContentLoaded', () => {
    const deleteModal = document.getElementById('comboDeleteModal');
    const deleteForm  = document.getElementById('comboDeleteForm');
    const nameEl      = document.getElementById('deleteComboName');
    const idEl        = document.getElementById('deleteComboId');
    const cancelBtn   = document.getElementById('cancelComboDeleteBtn');
    const confirmBtn  = document.getElementById('confirmComboDeleteBtn');

    if (!deleteModal || !deleteForm) {
        return;
    }

    // Scope to delete buttons inside combo rows so customers.js / restaurants.js
    // generic .delete-btn handlers don't fight us.
    document.querySelectorAll('[id^="combo-row-"] .delete-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            if (idEl)   idEl.value = btn.dataset.id || '';
            if (nameEl) nameEl.textContent = btn.dataset.name || '';
            deleteModal.classList.add('active');
        });
    });

    function closeDeleteModal() {
        deleteModal.classList.remove('active');
    }

    if (cancelBtn) {
        cancelBtn.addEventListener('click', closeDeleteModal);
    }

    deleteModal.addEventListener('click', (e) => {
        if (e.target === deleteModal) closeDeleteModal();
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && deleteModal.classList.contains('active')) {
            closeDeleteModal();
        }
    });

    deleteForm.addEventListener('submit', (e) => {
        e.preventDefault();

        const id = idEl ? idEl.value : '';
        const formData = new FormData(deleteForm);

        if (confirmBtn) {
            confirmBtn.disabled = true;
            confirmBtn.textContent = 'Deleting...';
        }

        fetch(deleteForm.action, {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const row = document.getElementById(`combo-row-${id}`);
                if (row) {
                    row.style.opacity = '0';
                    row.style.transform = 'translateX(-20px)';
                    row.style.transition = 'all 0.3s ease';
                    setTimeout(() => row.remove(), 300);
                }
                closeDeleteModal();
                showFlashMessage('Combo deleted successfully.', 'success');

                setTimeout(() => {
                    const remaining = document.querySelectorAll('[id^="combo-row-"]');
                    if (remaining.length === 0) {
                        location.reload();
                    }
                }, 400);
            } else {
                closeDeleteModal();
                showFlashMessage(data.message || 'An error occurred.', 'error');
            }
        })
        .catch(() => {
            // Fallback: submit form normally so the server-rendered redirect runs.
            deleteForm.submit();
        })
        .finally(() => {
            if (confirmBtn) {
                confirmBtn.disabled = false;
                confirmBtn.innerHTML = '<svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/></svg> Delete';
            }
        });
    });

    function showFlashMessage(message, type) {
        const existing = document.querySelectorAll('.alert');
        existing.forEach(el => el.remove());

        const alert = document.createElement('div');
        alert.className = `alert alert-${type}`;
        alert.setAttribute('role', 'alert');
        alert.innerHTML = `
            <span>${escapeHtml(message)}</span>
            <button type="button" class="alert-close" aria-label="Close">&times;</button>
        `;

        const mainContent = document.querySelector('.main-content');
        if (mainContent) {
            const firstChild = mainContent.querySelector('.page-header') || mainContent.firstChild;
            mainContent.insertBefore(alert, firstChild);
        }

        alert.querySelector('.alert-close').addEventListener('click', () => {
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-10px)';
            setTimeout(() => alert.remove(), 300);
        });

        setTimeout(() => {
            if (alert.parentElement) {
                alert.style.opacity = '0';
                alert.style.transform = 'translateY(-10px)';
                setTimeout(() => alert.remove(), 300);
            }
        }, 6000);
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
});
