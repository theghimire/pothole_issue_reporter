<?php
include 'db.php';
session_start();

if (!isset($_SESSION['admin_logged_in'])) {
    exit("Unauthorized");
}

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="municipality_issues_' . date('Y-m-d') . '.csv"');

$output = fopen('php://output', 'w');
fputcsv($output, ['Ticket ID', 'Reporter', 'Category', 'Ward', 'Status', 'Date', 'Latitude', 'Longitude']);

$res = mysqli_query($conn, "SELECT id, name, category, ward, status, created_at, latitude, longitude FROM issues ORDER BY id DESC");
while ($row = mysqli_fetch_assoc($res)) {
    fputcsv($output, $row);
}
fclose($output);
exit();
?>