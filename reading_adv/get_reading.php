<?php
require_once '../db.php';

header('Content-Type: application/json');

$id = (int)($_GET['id'] ?? 0);

if ($id === 0) {
    echo json_encode(null);
    exit;
}

$q = $mysqli->query("
    SELECT *
    FROM meter_readings
    WHERE id = {$id}
    LIMIT 1
");

echo json_encode($q ? $q->fetch_assoc() : null);
