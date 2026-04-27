/**
 * Orders module scripts: delete modal, AJAX delete, live total, form validation.
 */
document.addEventListener('DOMContentLoaded', () => {
    const deleteModal = document.getElementById('orderDeleteModal');
    const deleteForm  = document.getElementById('orderDeleteForm');
    const deleteLabel = document.getElementById('deleteOrderLabel');
    const deleteId    = document.getElementById('deleteOrderId');
    const cancelBtn   = document.getElementById('cancelOrderDeleteBtn');

    if (deleteModal && deleteForm) {
        document.querySelectorAll('[id^="order-row-"] .delete-btn, [id^="order-card-"] .delete-btn, .order-detail-card .delete-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                if (deleteId)    deleteId.value        = btn.dataset.id;
                if (deleteLabel) deleteLabel.textContent = btn.dataset.label;
                deleteModal.classList.add('active');
            });
        });

        if (cancelBtn) cancelBtn.addEventListener('click', closeDeleteModal);

        deleteModal.addEventListener('click', e => {
            if (e.target === deleteModal) closeDeleteModal();
        });

        document.addEventListener('keydown', e => {
            if (e.key === 'Escape' && deleteModal.classList.contains('active')) closeDeleteModal();
        });

        deleteForm.addEventListener('submit', e => {
            e.preventDefault();
            const id = deleteId.value;
            const confirmBtn = document.getElementById('confirmOrderDeleteBtn');

            if (confirmBtn) {
                confirmBtn.disabled = true;
                confirmBtn.textContent = 'Deleting...';
            }

            fetch(deleteForm.action, {
                method: 'POST',
                body: new FormData(deleteForm),
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    const row  = document.getElementById(`order-row-${id}`);
                    const card = document.getElementById(`order-card-${id}`);
                    [row, card].forEach(el => {
                        if (el) {
                            el.style.opacity = '0';
                            el.style.transform = 'translateX(-20px)';
                            el.style.transition = 'all 0.3s ease';
                            setTimeout(() => el.remove(), 300);
                        }
                    });
                    closeDeleteModal();
                    showFlashMessage('Order deleted successfully.', 'success');

                    // If on show page, redirect to list after delete
                    if (document.querySelector('.order-detail-card')) {
                        setTimeout(() => { window.location.href = deleteForm.action.replace('orders/delete', 'orders'); }, 600);
                    } else {
                        setTimeout(() => {
                            const remainingRows  = document.querySelectorAll('[id^="order-row-"]');
                            const remainingCards = document.querySelectorAll('[id^="order-card-"]');
                            if (remainingRows.length === 0 && remainingCards.length === 0) location.reload();
                        }, 400);
                    }
                } else {
                    closeDeleteModal();
                    showFlashMessage(data.message || 'An error occurred.', 'error');
                }
            })
            .catch(() => deleteForm.submit())
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

    // Live total calculator on create page
    const qtyInput     = document.getElementById('quantity');
    const summaryQty   = document.getElementById('summaryQty');
    const summaryTotal = document.getElementById('summaryTotal');

    if (qtyInput && summaryQty && summaryTotal) {
        const unitPrice = parseFloat(qtyInput.dataset.unitPrice) || 0;

        qtyInput.addEventListener('input', () => {
            const qty   = Math.max(1, parseInt(qtyInput.value, 10) || 0);
            const total = unitPrice * qty;
            summaryQty.textContent   = qty;
            summaryTotal.textContent = '$' + total.toFixed(2);
        });
    }

    // Order form validation
    const orderForm = document.getElementById('orderForm');
    if (orderForm) {
        orderForm.addEventListener('submit', e => {
            const v          = new FormValidator(orderForm);
            const customerId = orderForm.querySelector('[name="customer_id"]').value;
            const quantity   = orderForm.querySelector('[name="quantity"]').value;
            const notes      = orderForm.querySelector('[name="notes"]').value;

            if (!customerId || customerId === '') {
                v.addError('customer_id', 'Please select a customer.');
            }

            if (!quantity || quantity.trim() === '') {
                v.addError('quantity', 'Quantity is required.');
            } else if (!/^\d+$/.test(quantity.trim())) {
                v.addError('quantity', 'Quantity must be a positive integer.');
            } else {
                const qty = parseInt(quantity, 10);
                if (qty < 1 || qty > 99) {
                    v.addError('quantity', 'Quantity must be between 1 and 99.');
                }
            }

            if (notes && notes.length > 500) {
                v.addError('notes', 'Notes must not exceed 500 characters.');
            }

            if (!v.isValid()) {
                e.preventDefault();
                v.showErrors();
            }
        });
    }

    function showFlashMessage(message, type) {
        document.querySelectorAll('.alert').forEach(el => el.remove());

        const alert = document.createElement('div');
        alert.className = `alert alert-${type}`;
        alert.setAttribute('role', 'alert');
        alert.innerHTML = `
            <span>${escapeHtml(message)}</span>
            <button type="button" class="alert-close" aria-label="Close">&times;</button>
        `;

        const mainContent = document.querySelector('.main-content');
        if (mainContent) {
            const anchor = mainContent.querySelector('.page-header') || mainContent.firstChild;
            mainContent.insertBefore(alert, anchor);
        }

        alert.querySelector('.alert-close').addEventListener('click', () => {
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 300);
        });

        setTimeout(() => {
            if (alert.parentElement) {
                alert.style.opacity = '0';
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
