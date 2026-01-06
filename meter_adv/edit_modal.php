<?php
// modal only – used by meters.php
?>
<div class="modal fade" id="meterEditModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="meterEditForm">
        <div class="modal-header">
          <h5 class="modal-title">
            <i class="bi bi-pencil-square me-1"></i> Edit Meter
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <input type="hidden" id="m_edit_id" name="id">

          <div class="mb-2">
            <label class="form-label">Meter Number</label>
            <input type="text" id="m_edit_number" name="meter_number" class="form-control" required>
          </div>

          <div class="mb-2">
            <label class="form-label">Customer</label>
            <select id="m_edit_customer" name="customer_id" class="form-select" required>
              <!-- options load via PHP in add.php; here we just edit id (Ajax fills) -->
            </select>
          </div>

          <div class="mb-2">
            <label class="form-label">Utility</label>
            <select id="m_edit_utility" name="utility_id" class="form-select" required>
              <!-- options loaded by JS from data-utilities (see below) or a simple reload -->
            </select>
          </div>

          <div class="mb-2">
            <label class="form-label">Install Date</label>
            <input type="date" id="m_edit_install" name="install_date" class="form-control">
          </div>

          <div class="mb-2">
            <label class="form-label">Status</label>
            <select id="m_edit_status" name="status" class="form-select">
              <option value="Active">Active</option>
              <option value="Inactive">Inactive</option>
            </select>
          </div>
        </div>

        <div class="modal-footer">
          <button class="btn btn-primary">
            <i class="bi bi-save2 me-1"></i> Save Changes
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
