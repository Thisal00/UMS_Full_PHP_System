<?php
require_once '../db.php';
header('Content-Type: application/json');

$id = (int)($_GET['id'] ?? 0);

if ($id === 0) {
    echo json_encode(['status' => 'error', 'msg' => 'Invalid ID']);
    exit;
}

// prevent delete if bill exists
$billRes = $mysqli->query("SELECT id FROM bills WHERE reading_id = {$id} LIMIT 1");
if ($billRes && $billRes->num_rows > 0) {
    echo json_encode(['status' => 'error', 'msg' => 'Cannot delete. Bill already exists for this reading.']);
    exit;
}

$mysqli->query("DELETE FROM meter_readings WHERE id = {$id}");

if ($mysqli->affected_rows > 0) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'msg' => 'Delete failed or reading not found.']);
}
