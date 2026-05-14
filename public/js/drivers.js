/**
 * Delivery driver module scripts: delete modal, AJAX delete, form validation.
 */
document.addEventListener('DOMContentLoaded', () => {
    const deleteModal = document.getElementById('driverDeleteModal');
    const deleteForm = document.getElementById('driverDeleteForm');
    const deleteDriverName = document.getElementById('deleteDriverName');
    const deleteDriverId = document.getElementById('deleteDriverId');
    const cancelDeleteBtn = document.getElementById('cancelDriverDeleteBtn');

    if (deleteModal && deleteForm) {
        document.querySelectorAll('[id^="driver-row-"] .delete-btn, [id^="driver-card-"] .delete-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const id = btn.dataset.id;
                const name = btn.dataset.name;

                if (deleteDriverId) deleteDriverId.value = id;
                if (deleteDriverName) deleteDriverName.textContent = name;
                deleteModal.classList.add('active');
            });
        });

        if (cancelDeleteBtn) {
            cancelDeleteBtn.addEventListener('click', closeDeleteModal);
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

            const id = deleteDriverId.value;
            const formData = new FormData(deleteForm);

            const confirmBtn = document.getElementById('confirmDriverDeleteBtn');
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
                    const row = document.getElementById(`driver-row-${id}`);
                    if (row) {
                        row.style.opacity = '0';
                        row.style.transform = 'translateX(-20px)';
                        row.style.transition = 'all 0.3s ease';
                        setTimeout(() => row.remove(), 300);
                    }

                    const card = document.getElementById(`driver-card-${id}`);
                    if (card) {
                        card.style.opacity = '0';
                        card.style.transform = 'scale(0.95)';
                        card.style.transition = 'all 0.3s ease';
                        setTimeout(() => card.remove(), 300);
                    }

                    closeDeleteModal();
                    showFlashMessage('Delivery driver deleted successfully.', 'success');

                    setTimeout(() => {
                        const remainingRows = document.querySelectorAll('[id^="driver-row-"]');
                        const remainingCards = document.querySelectorAll('[id^="driver-card-"]');
                        if (remainingRows.length === 0 && remainingCards.length === 0) {
                            location.reload();
                        }
                    }, 400);
                } else {
                    closeDeleteModal();
                    showFlashMessage(data.message || 'An error occurred.', 'error');
                }
            })
            .catch(() => {
                deleteForm.submit();
            })
            .finally(() => {
                if (confirmBtn) {
                    confirmBtn.disabled = false;
                    confirmBtn.innerHTML = '<svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/></svg> Delete';
                }
            });
        });
    }

    function closeDeleteModal() {
        if (deleteModal) deleteModal.classList.remove('active');
    }

    // Driver form: HTML5 + server-side validation are authoritative.
    // (Field-level JS validation removed during the schema migration.)

    /**
     * Show a flash message dynamically.
     */
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
