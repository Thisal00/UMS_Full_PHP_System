<div class="modal fade" id="deleteReadingModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">

      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title">
          <i class="bi bi-trash-fill"></i> Delete Reading
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <p class="text-danger mb-2">
          Are you sure you want to delete this reading?<br>
          <strong>This action cannot be undone.</strong>
        </p>

        <input type="hidden" id="delete_id">

        <button class="btn btn-danger w-100 mt-2" id="confirmDeleteBtn">
          <i class="bi bi-trash"></i> Confirm Delete
        </button>
      </div>

    </div>
  </div>
</div>
