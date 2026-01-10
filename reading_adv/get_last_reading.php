<?php
require_once '../db.php';

header('Content-Type: application/json');

$meter_id = (int)($_GET['meter_id'] ?? 0);

if ($meter_id === 0) {
    echo json_encode(['previous' => 0]);
    exit;
}

$res = $mysqli->query("
    SELECT current_reading
    FROM meter_readings
    WHERE meter_id = {$meter_id}
    ORDER BY id DESC
    LIMIT 1
");

$row = $res ? $res->fetch_assoc() : null;

echo json_encode([
    'previous' => $row['current_reading'] ?? 0
]);
