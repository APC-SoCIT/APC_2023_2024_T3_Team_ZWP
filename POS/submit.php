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
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT password, role FROM login WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $hashed_password = $row['password'];
        $role = $row['role'];

        if (password_verify($password, $hashed_password)) {
            $_SESSION['username'] = $username;
            $_SESSION['role'] = $role;
            header('Location: index.php');
            exit();
        } else {
            $_SESSION['error'] = "Incorrect username or password.";
            header('Location: login.php');
            exit();
        }
    } else {
        $_SESSION['error'] = "User not found.";
        header('Location: login.php');
        exit();
    }

    $stmt->close();
}

$conn->close();
?>
