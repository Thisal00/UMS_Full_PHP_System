<div class="modal fade" id="editReadingModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">

      <div class="modal-header bg-warning text-dark">
        <h5 class="modal-title">
          <i class="bi bi-pencil-square"></i> Edit Reading
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        <form id="editReadingForm">
          <input type="hidden" id="edit_id" name="id">

          <div class="mb-2">
            <label class="form-label">Previous Reading</label>
            <input type="number" step="0.01" id="edit_prev" class="form-control" readonly>
          </div>

          <div class="mb-2">
            <label class="form-label">Current Reading</label>
            <input type="number" step="0.01" id="edit_curr" name="current_reading" class="form-control" required>
          </div>

          <button class="btn btn-warning w-100 mt-2">
            <i class="bi bi-check-circle"></i> Update Reading
          </button>
        </form>
      </div>

    </div>
  </div>
</div>
