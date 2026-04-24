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

    // Driver form validation
    const driverForm = document.getElementById('driverForm');
    if (driverForm) {
        const allowedStatuses = ['available', 'busy'];

        driverForm.addEventListener('submit', (e) => {
            const v = new FormValidator(driverForm);
            const fullName = driverForm.querySelector('[name="full_name"]').value;
            const idNumber = driverForm.querySelector('[name="id_number"]').value;
            const email = driverForm.querySelector('[name="email"]').value;
            const address = driverForm.querySelector('[name="address"]').value;
            const phone = driverForm.querySelector('[name="phone"]').value;
            const cardNumber = driverForm.querySelector('[name="card_number"]').value;
            const status = driverForm.querySelector('[name="status"]').value;
            const orderDistance = driverForm.querySelector('[name="order_distance"]').value;
            const dailyKilometers = driverForm.querySelector('[name="daily_kilometers"]').value;
            const weekdayCost = driverForm.querySelector('[name="weekday_cost_per_km"]').value;
            const holidayCost = driverForm.querySelector('[name="holiday_cost_per_km"]').value;
            const warningCount = driverForm.querySelector('[name="warning_count"]').value;
            const complaints = driverForm.querySelector('[name="complaints"]').value;

            v.required(fullName, 'full_name', 'Full name')
             .minLength(fullName, 2, 'full_name', 'Full name')
             .maxLength(fullName, 100, 'full_name', 'Full name')
             .required(idNumber, 'id_number', 'ID number')
             .minLength(idNumber, 3, 'id_number', 'ID number')
             .maxLength(idNumber, 30, 'id_number', 'ID number')
             .required(email, 'email', 'Email')
             .email(email, 'email')
             .maxLength(email, 100, 'email', 'Email')
             .required(address, 'address', 'Address')
             .maxLength(address, 255, 'address', 'Address')
             .required(phone, 'phone', 'Phone')
             .phone(phone, 'phone')
             .maxLength(phone, 20, 'phone', 'Phone')
             .required(cardNumber, 'card_number', 'Card number')
             .maxLength(cardNumber, 32, 'card_number', 'Card number')
             .required(status, 'status', 'Status');

            if (cardNumber && !/^\d{13,19}$/.test(cardNumber)) {
                v.addError('card_number', 'Card number must contain 13 to 19 digits only.');
            }

            if (status && !allowedStatuses.includes(status)) {
                v.addError('status', 'Please select a valid status.');
            }

            const numericFields = [
                ['order_distance', orderDistance, 'Order distance'],
                ['daily_kilometers', dailyKilometers, 'Daily kilometers'],
                ['weekday_cost_per_km', weekdayCost, 'Weekday cost per km'],
                ['holiday_cost_per_km', holidayCost, 'Holiday cost per km'],
            ];
            numericFields.forEach(([field, value, label]) => {
                if (!value || value.trim() === '') {
                    v.addError(field, `${label} is required.`);
                } else if (isNaN(Number(value))) {
                    v.addError(field, `${label} must be a valid number.`);
                } else {
                    const num = Number(value);
                    if (num < 0) {
                        v.addError(field, `${label} cannot be negative.`);
                    } else if (num > 999999.99) {
                        v.addError(field, `${label} must not exceed 999999.99.`);
                    }
                }
            });

            if (!warningCount || warningCount.trim() === '') {
                v.addError('warning_count', 'Warning count is required.');
            } else if (!/^\d+$/.test(warningCount)) {
                v.addError('warning_count', 'Warning count must be a non-negative integer.');
            } else {
                const wc = Number(warningCount);
                if (wc < 0 || wc > 99) {
                    v.addError('warning_count', 'Warning count must be between 0 and 99.');
                }
            }

            if (complaints) {
                v.maxLength(complaints, 2000, 'complaints', 'Complaints');
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
