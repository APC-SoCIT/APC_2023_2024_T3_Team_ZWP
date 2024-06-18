<?php
session_start();
$servername = 'localhost';
$db_username = 'root';
$db_password = '';
$db_name = 'db1';

$conn = new mysqli($servername, $db_username, $db_password, $db_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $new_password = filter_input(INPUT_POST, 'new_password', FILTER_SANITIZE_STRING);
    $confirm_password = filter_input(INPUT_POST, 'confirm_password', FILTER_SANITIZE_STRING);

    if ($new_password !== $confirm_password) {
        $_SESSION['error'] = "password_mismatch";
        header('Location: login.php?error=password_mismatch');
        exit();
    }

    $stmt = $conn->prepare("SELECT * FROM login WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        $update_stmt = $conn->prepare("UPDATE login SET password = ? WHERE username = ?");
        $update_stmt->bind_param("ss", $hashed_password, $username);
        if ($update_stmt->execute()) {
            $_SESSION['reset_success'] = true;
            $_SESSION['reset_username'] = $username; 
            header('Location: login.php');
            exit();
        } else {
            $_SESSION['error'] = "reset_failed";
            header('Location: login.php?error=reset_failed');
            exit();
        }
    } else {
        $_SESSION['error'] = "user_not_found";
        header('Location: login.php?error=user_not_found');
        exit();
    }

    $stmt->close();
    $update_stmt->close();
}

$conn->close();
?>
