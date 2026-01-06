<?php
require_once 'db.php';
require_once 'auth.php';
require_login();

// reuse simple filtered query (you can copy the same filter parsing as reports.php)
// For brevity, we'll export all unpaid from v_unpaid_bills (you can add filter parsing same as reports.php)
$sql = "SELECT bill_id, customer_code, full_name, billing_year, billing_month, total_amount, amount_paid, outstanding, due_date FROM v_unpaid_bills ORDER BY billing_year DESC, billing_month DESC";
$res = $mysqli->query($sql);

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=unpaid_bills_'.date('Ymd').'.csv');

$out = fopen('php://output', 'w');
fputcsv($out, ['Bill ID','Customer Code','Customer Name','Year','Month','Total','Paid','Due','Due Date']);
while ($r = $res->fetch_assoc()) {
    fputcsv($out, [
      $r['bill_id'],$r['customer_code'],$r['full_name'],$r['billing_year'],$r['billing_month'],
      number_format($r['total_amount'],2), number_format($r['amount_paid'],2), number_format($r['outstanding'],2), $r['due_date']
    ]);
}
fclose($out);
exit;
