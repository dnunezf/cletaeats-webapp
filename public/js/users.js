/**
 * User module scripts: delete modal, AJAX delete, form validation.
 */
document.addEventListener('DOMContentLoaded', () => {
    const deleteModal = document.getElementById('userDeleteModal');
    const deleteForm = document.getElementById('userDeleteForm');
    const deleteUserName = document.getElementById('deleteUserName');
    const deleteUserId = document.getElementById('deleteUserId');
    const cancelDeleteBtn = document.getElementById('cancelUserDeleteBtn');

    if (deleteModal && deleteForm) {
        document.querySelectorAll('[id^="user-row-"] .delete-btn, [id^="user-card-"] .delete-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const id = btn.dataset.id;
                const name = btn.dataset.name;

                if (deleteUserId) deleteUserId.value = id;
                if (deleteUserName) deleteUserName.textContent = name;
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

            const id = deleteUserId.value;
            const formData = new FormData(deleteForm);

            const confirmBtn = document.getElementById('confirmUserDeleteBtn');
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
                    const row = document.getElementById(`user-row-${id}`);
                    if (row) {
                        row.style.opacity = '0';
                        row.style.transform = 'translateX(-20px)';
                        row.style.transition = 'all 0.3s ease';
                        setTimeout(() => row.remove(), 300);
                    }

                    const card = document.getElementById(`user-card-${id}`);
                    if (card) {
                        card.style.opacity = '0';
                        card.style.transform = 'scale(0.95)';
                        card.style.transition = 'all 0.3s ease';
                        setTimeout(() => card.remove(), 300);
                    }

                    closeDeleteModal();
                    showFlashMessage('User deleted successfully.', 'success');

                    setTimeout(() => {
                        const remainingRows = document.querySelectorAll('[id^="user-row-"]');
                        const remainingCards = document.querySelectorAll('[id^="user-card-"]');
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

    // User form validation
    const userForm = document.getElementById('userForm');
    if (userForm) {
        const allowedRoles = ['admin', 'user'];
        const allowedStatuses = ['active', 'pending'];

        userForm.addEventListener('submit', (e) => {
            const v = new FormValidator(userForm);
            const username = userForm.querySelector('[name="username"]').value;
            const email = userForm.querySelector('[name="email"]').value;
            const roleEl = userForm.querySelector('select[name="role"]') || userForm.querySelector('[name="role"]');
            const role = roleEl ? roleEl.value : '';
            const status = userForm.querySelector('[name="status"]').value;
            const password = userForm.querySelector('[name="password"]').value;
            const passwordConfirm = userForm.querySelector('[name="password_confirm"]').value;

            v.required(username, 'username', 'Username')
             .alphanumeric(username, 'username', 'Username')
             .minLength(username, 3, 'username', 'Username')
             .maxLength(username, 50, 'username', 'Username')
             .required(email, 'email', 'Email')
             .email(email, 'email')
             .maxLength(email, 100, 'email', 'Email');

            if (!allowedRoles.includes(role)) {
                v.addError('role', 'Please select a valid role.');
            }
            if (!allowedStatuses.includes(status)) {
                v.addError('status', 'Please select a valid status.');
            }

            if (password || passwordConfirm) {
                v.minLength(password, 8, 'password', 'Password')
                 .maxLength(password, 72, 'password', 'Password')
                 .matches(password, passwordConfirm, 'password', 'Password');
            }

            if (!v.isValid()) {
                e.preventDefault();
                v.showErrors();
            }
        });
    }

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
