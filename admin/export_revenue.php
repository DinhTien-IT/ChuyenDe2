<?php
session_start();
if (!isset($_SESSION['currentUser']) || strtolower($_SESSION['currentUser']['role']) !== 'admin') {
    header("Location: ../login.php");
    exit();
}
include '../db.php';

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="revenue_report.csv"');

$output = fopen("php://output", "w");
fputcsv($output, ['ID Đơn hàng', 'Khách hàng', 'Tổng tiền', 'Ngày tạo']);

$result = $conn->query("SELECT id, full_name, total_price, created_at FROM orders ORDER BY created_at DESC");

while ($row = $result->fetch_assoc()) {
    fputcsv($output, [$row['id'], $row['full_name'], $row['total_price'], $row['created_at']]);
}

fclose($output);
exit();
?>
