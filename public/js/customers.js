/**
 * Customer module scripts: delete modal, AJAX delete, form validation.
 */
document.addEventListener('DOMContentLoaded', () => {
    const deleteModal = document.getElementById('deleteModal');
    const deleteForm = document.getElementById('deleteForm');
    const deleteCustomerName = document.getElementById('deleteCustomerName');
    const deleteCustomerId = document.getElementById('deleteCustomerId');
    const cancelDeleteBtn = document.getElementById('cancelDeleteBtn');

    // Open delete modal
    document.querySelectorAll('.delete-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const id = btn.dataset.id;
            const name = btn.dataset.name;

            if (deleteCustomerId) deleteCustomerId.value = id;
            if (deleteCustomerName) deleteCustomerName.textContent = name;
            if (deleteModal) deleteModal.classList.add('active');
        });
    });

    // Close delete modal
    if (cancelDeleteBtn) {
        cancelDeleteBtn.addEventListener('click', closeDeleteModal);
    }

    if (deleteModal) {
        deleteModal.addEventListener('click', (e) => {
            if (e.target === deleteModal) closeDeleteModal();
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && deleteModal.classList.contains('active')) {
                closeDeleteModal();
            }
        });
    }

    function closeDeleteModal() {
        if (deleteModal) deleteModal.classList.remove('active');
    }

    // AJAX delete with fallback
    if (deleteForm) {
        deleteForm.addEventListener('submit', (e) => {
            e.preventDefault();

            const id = deleteCustomerId.value;
            const formData = new FormData(deleteForm);

            const confirmBtn = document.getElementById('confirmDeleteBtn');
            if (confirmBtn) {
                confirmBtn.disabled = true;
                confirmBtn.textContent = 'Deleting...';
            }

            fetch(deleteForm.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Remove from table
                    const row = document.getElementById(`customer-row-${id}`);
                    if (row) {
                        row.style.opacity = '0';
                        row.style.transform = 'translateX(-20px)';
                        row.style.transition = 'all 0.3s ease';
                        setTimeout(() => row.remove(), 300);
                    }

                    // Remove from cards
                    const card = document.getElementById(`customer-card-${id}`);
                    if (card) {
                        card.style.opacity = '0';
                        card.style.transform = 'scale(0.95)';
                        card.style.transition = 'all 0.3s ease';
                        setTimeout(() => card.remove(), 300);
                    }

                    closeDeleteModal();
                    showFlashMessage('Customer deleted successfully.', 'success');

                    // Check if list is now empty
                    setTimeout(() => {
                        const remainingRows = document.querySelectorAll('[id^="customer-row-"]');
                        const remainingCards = document.querySelectorAll('[id^="customer-card-"]');
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
                // Fallback: submit form normally
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

    // Customer form validation
    const customerForm = document.getElementById('customerForm');
    if (customerForm) {
        customerForm.addEventListener('submit', (e) => {
            const v = new FormValidator(customerForm);
            const firstName = customerForm.querySelector('[name="first_name"]').value;
            const lastName = customerForm.querySelector('[name="last_name"]').value;
            const email = customerForm.querySelector('[name="email"]').value;
            const phone = customerForm.querySelector('[name="phone_number"]').value;
            const postalCode = customerForm.querySelector('[name="postal_code"]').value;

            v.required(firstName, 'first_name', 'First name')
             .minLength(firstName, 2, 'first_name', 'First name')
             .maxLength(firstName, 50, 'first_name', 'First name')
             .required(lastName, 'last_name', 'Last name')
             .minLength(lastName, 2, 'last_name', 'Last name')
             .maxLength(lastName, 50, 'last_name', 'Last name')
             .required(email, 'email', 'Email')
             .email(email, 'email');

            if (phone) {
                v.phone(phone, 'phone_number');
            }
            if (postalCode) {
                v.maxLength(postalCode, 10, 'postal_code', 'Postal code');
            }

            if (!v.isValid()) {
                e.preventDefault();
                v.showErrors();
            }
        });
    }

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
