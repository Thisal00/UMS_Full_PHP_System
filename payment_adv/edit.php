<?php
require_once '../db.php';
require_once '../auth.php';
require_login();

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    die("Invalid payment ID");
}

// Load payment
$sql = "
SELECT p.*, 
       b.id AS bill_id,
       c.customer_code, c.full_name
FROM payments p
JOIN bills b ON b.id = p.bill_id
JOIN customers c ON c.id = b.customer_id
WHERE p.id = {$id}
LIMIT 1
";
$res = $mysqli->query($sql);
$pay = $res ? $res->fetch_assoc() : null;

if (!$pay) {
    die("Payment not found");
}

$msg = "";

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount  = (float)($_POST['amount'] ?? 0);
    $date    = trim($_POST['payment_date'] ?? '');
    $method  = trim($_POST['method'] ?? 'Cash');
    $ref     = trim($_POST['reference_no'] ?? '');

    if ($amount > 0 && $date) {

        $stmt = $mysqli->prepare("
            UPDATE payments
            SET payment_date = ?, amount = ?, method = ?, reference_no = ?
            WHERE id = ?
        ");
        $stmt->bind_param("sdssi", $date, $amount, $method, $ref, $id);
        if ($stmt->execute()) {

            $bill_id = (int)$pay['bill_id'];

            // recalc bill totals
            $mysqli->query("
                UPDATE bills b
                SET amount_paid = (
                    SELECT COALESCE(SUM(p.amount),0)
                    FROM payments p
                    WHERE p.bill_id = b.id
                ),
                outstanding = GREATEST(
                    b.total_amount - (
                        SELECT COALESCE(SUM(p.amount),0)
                        FROM payments p
                        WHERE p.bill_id = b.id
                    ), 0
                )
                WHERE b.id = {$bill_id}
            ");

            // update status
            $bill = $mysqli->query("SELECT total_amount, amount_paid, due_date FROM bills WHERE id=$bill_id")->fetch_assoc();

            $status = 'Pending';
            if ($bill['amount_paid'] >= $bill['total_amount']) {
                $status = 'Paid';
            } elseif ($bill['amount_paid'] > 0) {
                $status = 'Partially Paid';
            }

            $today = date('Y-m-d');
            if ($status !== 'Paid' && !empty($bill['due_date']) && $today > $bill['due_date']) {
                $status = 'Overdue';
            }

            $mysqli->query("UPDATE bills SET status='$status' WHERE id=$bill_id");

            header("Location: ../payments.php");
            exit;

        } else {
            $msg = "Error updating payment: " . $mysqli->error;
        }
        $stmt->close();
    } else {
        $msg = "Please fill all fields correctly.";
    }
}

include '../header.php';
?>

<h2 class="mb-3">
  <i class="bi bi-pencil-square"></i> Edit Payment #<?= htmlspecialchars($pay['id']) ?>
</h2>

<?php if ($msg): ?>
<div class="alert alert-info"><?= htmlspecialchars($msg) ?></div>
<?php endif; ?>

<div class="card shadow-sm border-0">
  <div class="card-header bg-primary text-white">
    <i class="bi bi-person-vcard"></i>
    <?= htmlspecialchars($pay['customer_code'].' - '.$pay['full_name']) ?>
  </div>
  <div class="card-body">
    <form method="post" class="row g-3">

      <div class="col-md-6">
        <label class="form-label fw-bold">Payment Date</label>
        <input type="date" name="payment_date" class="form-control" 
               value="<?= htmlspecialchars($pay['payment_date']) ?>" required>
      </div>

      <div class="col-md-6">
        <label class="form-label fw-bold">Amount</label>
        <input type="number" step="0.01" name="amount" class="form-control" 
               value="<?= htmlspecialchars($pay['amount']) ?>" required>
      </div>

      <div class="col-md-6">
        <label class="form-label fw-bold">Method</label>
        <select name="method" class="form-select">
          <option <?= $pay['method']=='Cash'?'selected':'' ?>>Cash</option>
          <option <?= $pay['method']=='Card'?'selected':'' ?>>Card</option>
          <option <?= $pay['method']=='Online'?'selected':'' ?>>Online</option>
        </select>
      </div>

      <div class="col-md-6">
        <label class="form-label fw-bold">Reference No</label>
        <input type="text" name="reference_no" class="form-control"
               value="<?= htmlspecialchars($pay['reference_no']) ?>">
      </div>

      <div class="col-12">
        <button class="btn btn-success">
          <i class="bi bi-check-circle"></i> Update Payment
        </button>
        <a href="../payments.php" class="btn btn-outline-secondary">
          Cancel
        </a>
      </div>

    </form>
  </div>
</div>

<?php include '../footer.php'; ?>
