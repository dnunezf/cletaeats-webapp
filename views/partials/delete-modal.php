<div class="modal-overlay" id="deleteModal">
    <div class="modal">
        <h3 class="modal-title">Confirm Deletion</h3>
        <p class="modal-body">
            Are you sure you want to delete <strong id="deleteCustomerName"></strong>?
            This action cannot be undone.
        </p>
        <form id="deleteForm" action="<?= baseUrl('customers/delete') ?>" method="POST">
            <?= csrfField() ?>
            <input type="hidden" name="id" id="deleteCustomerId" value="">
            <div class="modal-actions">
                <button type="button" class="btn btn-ghost" id="cancelDeleteBtn">Cancel</button>
                <button type="submit" class="btn btn-danger" id="confirmDeleteBtn">
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/></svg>
                    Delete
                </button>
            </div>
        </form>
    </div>
</div>
