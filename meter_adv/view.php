<?php
require_once '../db.php';
require_once '../auth.php';
require_login();

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) die("Invalid meter");

$meter = $mysqli->query("
    SELECT m.*, c.full_name AS customer_name, u.name AS utility_name
    FROM meters m
    JOIN customers c ON c.id = m.customer_id
    JOIN utilities u ON u.id = m.utility_id
    WHERE m.id = $id
")->fetch_assoc();

if (!$meter) {
    die("Meter not found");
}

// load readings if table exists
$readings = $mysqli->query("
    SELECT id, reading_date, previous_reading, current_reading, units_used, billing_month, billing_year
    FROM meter_readings
    WHERE meter_id = $id
    ORDER BY reading_date DESC
");


include '../header.php';
?>

<h2 class="mb-3">
  <i class="bi bi-speedometer2 me-1"></i> Meter Profile
</h2>

<div class="card mb-3 shadow-sm">
  <div class="card-body">
    <h4 class="card-title mb-2">
      <?= htmlspecialchars($meter['meter_number']) ?>
      <span class="badge bg-secondary ms-2"><?= htmlspecialchars($meter['utility_name']) ?></span>
    </h4>
    <p class="mb-1">
      <strong>Customer:</strong> <?= htmlspecialchars($meter['customer_name']) ?>
    </p>
    <p class="mb-1">
      <strong>Install Date:</strong> <?= htmlspecialchars($meter['install_date']) ?>
    </p>
    <p class="mb-0">
      <strong>Status:</strong>
      <?php if ($meter['status']=='Active'): ?>
        <span class="badge bg-success">Active</span>
      <?php else: ?>
        <span class="badge bg-secondary">Inactive</span>
      <?php endif; ?>
    </p>
  </div>
</div>

<div class="card shadow-sm">
  <div class="card-header">
    <i class="bi bi-list-numeric me-1"></i> Readings
  </div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-sm mb-0">
        <thead>
          <tr>
            <th>Date</th>
            <th>Previous</th>
            <th>Current</th>
            <th>Units</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($readings && $readings->num_rows > 0): ?>
            <?php while ($r = $readings->fetch_assoc()): ?>
            <tr>
              <td><?= htmlspecialchars($r['reading_date']) ?></td>
              <td><?= htmlspecialchars($r['previous_reading']) ?></td>
              <td><?= htmlspecialchars($r['current_reading']) ?></td>
              <td><?= htmlspecialchars($r['units_used']) ?></td>
            </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr><td colspan="4" class="text-center text-muted">No readings</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<a href="../meters.php" class="btn btn-secondary mt-3">
  <i class="bi bi-arrow-left me-1"></i> Back to Meters
</a>

<?php include '../footer.php'; ?>
