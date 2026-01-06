<?php
require_once '../db.php';
require_once '../auth.php';
require_login();

header('Content-Type: application/json; charset=utf-8');

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    echo json_encode(["error" => "Invalid ID"]);
    exit;
}

// base meter
$stmt = $mysqli->prepare("
    SELECT id, customer_id, utility_id, meter_number, install_date, status
    FROM meters
    WHERE id = ? LIMIT 1
");
$stmt->bind_param("i", $id);
$stmt->execute();
$meter = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$meter) {
    echo json_encode(["error" => "Meter not found"]);
    exit;
}

// load customers & utilities for dropdowns
$customers = [];
$res = $mysqli->query("SELECT id, full_name FROM customers ORDER BY full_name");
while ($row = $res->fetch_assoc()) {
    $customers[] = $row;
}

$utilities = [];
$res2 = $mysqli->query("SELECT id, name FROM utilities ORDER BY name");
while ($row2 = $res2->fetch_assoc()) {
    $utilities[] = $row2;
}

echo json_encode([
    "meter"     => $meter,
    "customers" => $customers,
    "utilities" => $utilities
]);
