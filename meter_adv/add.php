<?php
require_once '../db.php';
require_once '../auth.php';
require_login();

$msg = '';
$error = '';

// Generate next meter ID
$lastMeter = $mysqli->query("SELECT meter_number FROM meters ORDER BY id DESC LIMIT 1")->fetch_assoc();
$nextMeterNum = 1;
if ($lastMeter) {
    preg_match('/\d+$/', $lastMeter['meter_number'], $matches);
    if (!empty($matches[0])) {
        $nextMeterNum = intval($matches[0]) + 1;
    }
}
$generatedMeterID = 'MTR-' . str_pad($nextMeterNum, 6, '0', STR_PAD_LEFT);

$customers = $mysqli->query("SELECT id, full_name FROM customers ORDER BY full_name");
$utilities = $mysqli->query("SELECT id, name FROM utilities ORDER BY name");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer_id = (int)($_POST['customer_id'] ?? 0);
    $utility_id  = (int)($_POST['utility_id'] ?? 0);
    $meter_no    = trim($_POST['meter_number'] ?? $generatedMeterID);
    $install     = trim($_POST['install_date'] ?? date('Y-m-d'));
    $status      = trim($_POST['status'] ?? 'Active');

    if ($customer_id <= 0 || $utility_id <= 0 || $meter_no === '') {
        $error = "Customer, utility and meter number are required.";
    } else {
        // duplicate check
        $st = $mysqli->prepare("SELECT id FROM meters WHERE meter_number = ? LIMIT 1");
        $st->bind_param("s", $meter_no);
        $st->execute();
        $st->store_result();
        if ($st->num_rows > 0) {
            $error = "Meter number already exists.";
        }
        $st->close();

        if ($error === '') {
            $st2 = $mysqli->prepare("
                INSERT INTO meters (customer_id, utility_id, meter_number, install_date, status)
                VALUES (?,?,?,?,?)
            ");
            $st2->bind_param("iisss", $customer_id, $utility_id, $meter_no, $install, $status);
            if ($st2->execute()) {
                $msg = "✅ Meter <strong>" . htmlspecialchars($meter_no) . "</strong> added successfully!";
                // Regenerate for next one
                $nextMeterNum++;
                $generatedMeterID = 'MTR-' . str_pad($nextMeterNum, 6, '0', STR_PAD_LEFT);
                $meter_no = $install = '';
                $customer_id = $utility_id = 0;
                $status = 'Active';
            } else {
                $error = "Database error: " . $mysqli->error;
            }
            $st2->close();
        }
    }
}

include '../header.php';
?>

<div class="page-header">
    <h2>
        <i class="bi bi-plus-circle-fill"></i>
        <span>Add New Meter</span>
    </h2>
</div>

<?php if ($error): ?>
<div class="alert alert-danger py-3 d-flex align-items-center gap-2">
    <i class="bi bi-exclamation-triangle-fill"></i>
    <div><?= htmlspecialchars($error) ?></div>
</div>
<?php endif; ?>

<?php if ($msg): ?>
<div class="alert alert-success py-3 d-flex align-items-center gap-2">
    <i class="bi bi-check-circle-fill"></i>
    <div><?= $msg ?></div>
</div>
<?php endif; ?>

<div class="card-glass" style="max-width: 700px; margin: auto; padding: 0;">
  <div class="card-header-custom">
    <i class="bi bi-lightning-charge-fill"></i>
    <span>Meter Registration Form</span>
  </div>
  
  <div class="card-body-custom">
    <form method="post">

      <div class="mb-3">
        <label class="form-label"><i class="bi bi-people-fill me-2"></i>Customer *</label>
        <select name="customer_id" class="form-select" required>
          <option value="">-- Select Customer --</option>
          <?php
          $customers->data_seek(0);
          while ($c = $customers->fetch_assoc()): ?>
            <option value="<?= $c['id'] ?>">
              <?= htmlspecialchars($c['full_name']) ?>
            </option>
          <?php endwhile; ?>
        </select>
      </div>

      <div class="mb-3">
        <label class="form-label"><i class="bi bi-lightning-fill me-2"></i>Utility *</label>
        <select name="utility_id" class="form-select" required>
          <option value="">-- Select Utility --</option>
          <?php
          $utilities->data_seek(0);
          while ($u = $utilities->fetch_assoc()): ?>
            <option value="<?= $u['id'] ?>">
              <?= htmlspecialchars($u['name']) ?>
            </option>
          <?php endwhile; ?>
        </select>
      </div>

      <div class="mb-3">
        <label class="form-label"><i class="bi bi-qrcode me-2"></i>Meter ID (Auto-Generated)</label>
        <div class="input-group">
          <input type="text" name="meter_number" class="form-control" value="<?= htmlspecialchars($generatedMeterID) ?>" readonly style="background: rgba(56,189,248,0.1); font-weight: 700; letter-spacing: 1px;">
          <button type="button" class="btn btn-primary" onclick="document.querySelector('[name=meter_number]').value='<?= $generatedMeterID ?>'; alert('Meter ID reset to: <?= $generatedMeterID ?>')" style="border-radius: 12px;">
            <i class="bi bi-arrow-clockwise me-1"></i>Reset
          </button>
        </div>
        <small class="text-muted d-block mt-2">Format: MTR-XXXXXX (automatically generated)</small>
      </div>

      <div class="mb-3">
        <label class="form-label"><i class="bi bi-calendar-event me-2"></i>Install Date</label>
        <input type="date" name="install_date" class="form-control" value="<?= date('Y-m-d') ?>">
      </div>

      <div class="mb-3">
        <label class="form-label"><i class="bi bi-toggle-on me-2"></i>Status</label>
        <select name="status" class="form-select">
          <option value="Active" selected>🟢 Active</option>
          <option value="Inactive">⚪ Inactive</option>
        </select>
      </div>

      <div class="d-flex gap-3 mt-4">
        <button type="submit" class="btn btn-success btn-submit" style="flex: 1;">
          <i class="bi bi-check-circle-fill me-2"></i>Save Meter
        </button>
        <a href="../meters.php" class="btn btn-secondary" style="flex: 1; display: flex; align-items: center; justify-content: center; gap: 8px;">
          <i class="bi bi-arrow-left"></i>Back
        </a>
      </div>

    </form>
  </div>
</div>

<?php include '../footer.php'; ?>
