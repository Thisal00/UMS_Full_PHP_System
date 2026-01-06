<?php
require_once 'db.php';
require_once 'auth.php';
require_login();

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=monthly_revenue.csv');

$out = fopen('php://output', 'w');
fputcsv($out, ['Year','Month','Total Collected']);

$res = $mysqli->query("SELECT * FROM v_monthly_revenue ORDER BY billing_year, billing_month");
while ($row = $res->fetch_assoc()) {
    fputcsv($out, [$row['billing_year'], $row['billing_month'], $row['total_collected']]);
}
fclose($out);
exit;
