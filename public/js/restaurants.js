/**
 * Restaurant module scripts: delete modal, AJAX delete, form validation.
 */
document.addEventListener('DOMContentLoaded', () => {
    const deleteModal = document.getElementById('restaurantDeleteModal');
    const deleteForm = document.getElementById('restaurantDeleteForm');
    const deleteRestaurantName = document.getElementById('deleteRestaurantName');
    const deleteRestaurantId = document.getElementById('deleteRestaurantId');
    const cancelDeleteBtn = document.getElementById('cancelRestaurantDeleteBtn');

    // Only wire delete behavior when this page actually has the modal
    if (deleteModal && deleteForm) {
        document.querySelectorAll('#restaurantDeleteModal').length;

        // Open delete modal — scope to delete buttons inside restaurant rows/cards
        document.querySelectorAll('[id^="restaurant-row-"] .delete-btn, [id^="restaurant-card-"] .delete-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const id = btn.dataset.id;
                const name = btn.dataset.name;

                if (deleteRestaurantId) deleteRestaurantId.value = id;
                if (deleteRestaurantName) deleteRestaurantName.textContent = name;
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

            const id = deleteRestaurantId.value;
            const formData = new FormData(deleteForm);

            const confirmBtn = document.getElementById('confirmRestaurantDeleteBtn');
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
                    const row = document.getElementById(`restaurant-row-${id}`);
                    if (row) {
                        row.style.opacity = '0';
                        row.style.transform = 'translateX(-20px)';
                        row.style.transition = 'all 0.3s ease';
                        setTimeout(() => row.remove(), 300);
                    }

                    const card = document.getElementById(`restaurant-card-${id}`);
                    if (card) {
                        card.style.opacity = '0';
                        card.style.transform = 'scale(0.95)';
                        card.style.transition = 'all 0.3s ease';
                        setTimeout(() => card.remove(), 300);
                    }

                    closeDeleteModal();
                    showFlashMessage('Restaurant deleted successfully.', 'success');

                    setTimeout(() => {
                        const remainingRows = document.querySelectorAll('[id^="restaurant-row-"]');
                        const remainingCards = document.querySelectorAll('[id^="restaurant-card-"]');
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

    // Restaurant form validation
    const restaurantForm = document.getElementById('restaurantForm');
    if (restaurantForm) {
        const allowedFoodTypes = [
            'Italian', 'Mexican', 'Chinese', 'Japanese', 'Indian',
            'Mediterranean', 'American', 'Fast Food', 'Vegetarian', 'Other'
        ];

        restaurantForm.addEventListener('submit', (e) => {
            const v = new FormValidator(restaurantForm);
            const name = restaurantForm.querySelector('[name="name"]').value;
            const legalId = restaurantForm.querySelector('[name="legal_id"]').value;
            const address = restaurantForm.querySelector('[name="address"]').value;
            const foodType = restaurantForm.querySelector('[name="food_type"]').value;
            const comboName = restaurantForm.querySelector('[name="combo_name"]').value;
            const comboDescription = restaurantForm.querySelector('[name="combo_description"]').value;
            const comboPrice = restaurantForm.querySelector('[name="combo_price"]').value;

            v.required(name, 'name', 'Restaurant name')
             .minLength(name, 2, 'name', 'Restaurant name')
             .maxLength(name, 100, 'name', 'Restaurant name')
             .required(legalId, 'legal_id', 'Legal ID')
             .minLength(legalId, 5, 'legal_id', 'Legal ID')
             .maxLength(legalId, 30, 'legal_id', 'Legal ID')
             .required(address, 'address', 'Address')
             .maxLength(address, 255, 'address', 'Address')
             .required(foodType, 'food_type', 'Food type')
             .required(comboName, 'combo_name', 'Combo name')
             .minLength(comboName, 2, 'combo_name', 'Combo name')
             .maxLength(comboName, 100, 'combo_name', 'Combo name');

            if (comboDescription) {
                v.maxLength(comboDescription, 255, 'combo_description', 'Combo description');
            }

            if (foodType && !allowedFoodTypes.includes(foodType)) {
                v.addError('food_type', 'Please select a valid food type.');
            }

            if (!comboPrice || comboPrice.trim() === '') {
                v.addError('combo_price', 'Combo price is required.');
            } else if (isNaN(Number(comboPrice))) {
                v.addError('combo_price', 'Combo price must be a valid number.');
            } else {
                const priceNum = Number(comboPrice);
                if (priceNum < 0) {
                    v.addError('combo_price', 'Combo price cannot be negative.');
                } else if (priceNum > 999999.99) {
                    v.addError('combo_price', 'Combo price must not exceed 999999.99.');
                }
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
