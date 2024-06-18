<?php
// Retrieve form data
$item_name = $_POST['item_name'];
$description = $_POST['description'];
$unit_price = $_POST['unit_price'];
$quantity = $_POST['quantity'];

$conn = mysqli_connect("localhost", "root", "", "db1");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}


$sql = "INSERT INTO inventory (`Item Name`, `Description`, `Unit Price`, `Quantity in Stock`)
        VALUES (?, ?, ?, ?)";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ssdi", $item_name, $description, $unit_price, $quantity);
$result = mysqli_stmt_execute($stmt);

if ($result) {
    header("Location: inventory.php");
} else {
    echo "Error: " . mysqli_error($conn);
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>
