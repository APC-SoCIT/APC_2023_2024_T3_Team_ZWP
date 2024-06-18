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
    $password = password_hash(filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING), PASSWORD_DEFAULT);
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $contact_number = filter_input(INPUT_POST, 'contact_number', FILTER_SANITIZE_STRING);
    $role = filter_input(INPUT_POST, 'role', FILTER_SANITIZE_STRING);

    if (!preg_match('/^\d{11}$/', $contact_number)) {
        $_SESSION['error'] = "Incorrect contact number format. It should be 11 digits.";
        header('Location: login.php?error=incorrect_format');
        exit();
    }

    $stmt = $conn->prepare("INSERT INTO login (username, password, name, contact_number, role) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $username, $password, $name, $contact_number, $role);

    if ($stmt->execute()) {
        $_SESSION['username'] = $username;
        $_SESSION['role'] = $role;
        header('Location: index.php');
        exit();
    } else {
        $_SESSION['error'] = "Registration failed. Please try again.";
        header('Location: login.php?error=registration_failed');
        exit();
    }

    $stmt->close();
}

$conn->close();
?>
