<?php
require_once '../db.php';
require_once '../auth.php';
require_login();

header('Content-Type: application/json; charset=utf-8');

$id          = (int)($_POST['id'] ?? 0);
$meter_no    = trim($_POST['meter_number'] ?? '');
$customer_id = (int)($_POST['customer_id'] ?? 0);
$utility_id  = (int)($_POST['utility_id'] ?? 0);
$install     = trim($_POST['install_date'] ?? '');
$status      = trim($_POST['status'] ?? 'Active');

if ($id <= 0 || $meter_no === '' || $customer_id <= 0 || $utility_id <= 0) {
    echo json_encode([
        "status"  => "error",
        "message" => "Required fields missing."
    ]);
    exit;
}

// prevent duplicate meter_number (other rows)
$stmt = $mysqli->prepare("SELECT id FROM meters WHERE meter_number = ? AND id != ? LIMIT 1");
$stmt->bind_param("si", $meter_no, $id);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    echo json_encode([
        "status"  => "error",
        "message" => "Meter number already exists."
    ]);
    $stmt->close();
    exit;
}
$stmt->close();

$stmt = $mysqli->prepare("
    UPDATE meters
       SET customer_id = ?,
           utility_id  = ?,
           meter_number= ?,
           install_date= ?,
           status      = ?
     WHERE id = ?
");
$stmt->bind_param("iisssi",
    $customer_id, $utility_id, $meter_no, $install, $status, $id
);

if ($stmt->execute()) {
    echo json_encode([
        "status"  => "success",
        "message" => "Meter updated successfully."
    ]);
} else {
    echo json_encode([
        "status"  => "error",
        "message" => "Database error: " . $mysqli->error
    ]);
}
$stmt->close();
