<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "db1";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$range = $_GET['range'] ?? 'daily';
switch ($range) {
    case 'weekly':
        $sql = "SELECT DATE_FORMAT(Date, '%Y-%u') AS WeekDate, SUM(TotalSales) AS TotalSales FROM sales_report GROUP BY WeekDate";
        break;
    case 'monthly':
        $sql = "SELECT DATE_FORMAT(Date, '%Y-%m') AS MonthDate, SUM(TotalSales) AS TotalSales FROM sales_report GROUP BY MonthDate";
        break;
    case 'daily':
    default:
        $sql = "SELECT Date, TotalSales FROM sales_report";
        break;
}

$result = $conn->query($sql);

$dates = [];
$totals = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $dates[] = $row['Date'] ?? $row['WeekDate'] ?? $row['MonthDate'];
        $totals[] = $row['TotalSales'];
    }
}

$conn->close();

$data = [
    'dates' => $dates,
    'totals' => $totals,
];

header('Content-Type: application/json');
echo json_encode($data);
?>
