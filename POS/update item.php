<?php
$item_id = $_POST['item_id'];
$new_item_name = $_POST['item_name'];
$new_description = $_POST['description'];
$new_unit_price = $_POST['unit_price'];
$new_quantity = $_POST['quantity'];

// Connect to the database
$conn = mysqli_connect("localhost", "root", "", "db1");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Update the item details in the database
$sql = "UPDATE inventory SET `Item Name` = ?, `Description` = ?, `Unit Price` = ?, `Quantity in Stock` = ? WHERE `Item ID` = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ssdii", $new_item_name, $new_description, $new_unit_price, $new_quantity, $item_id);

if (mysqli_stmt_execute($stmt)) {
    header("Location: inventory.php");
} else {
    echo "Error updating record: " . mysqli_error($conn);
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>
