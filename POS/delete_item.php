<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Assuming $_POST['id'] contains the item ID to delete
    $id = intval($_POST['id']);
    
    // Connect to the database
    $conn = new mysqli("localhost", "root", "", "db1");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    // Prepare and bind the delete statement
    $stmt = $conn->prepare("DELETE FROM inventory WHERE `Item ID` = ?");
    $stmt->bind_param("i", $id);
    
    // Execute the statement
    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "error";
    }
    
    // Close statement and connection
    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request";
}
?>
