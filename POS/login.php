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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT password, role FROM login WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            $_SESSION['username'] = $username;
            $_SESSION['role'] = $row['role'];
            header('Location: index.php');
            exit();
        } else {
            header('Location: login.php?error=invalid_credentials');
            exit();
        }
    } else {
        header('Location: login.php?error=invalid_credentials');
        exit();
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory System Login</title>
    <link rel="stylesheet" href="login.css">
    <style>
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgb(0,0,0);
            background-color: rgba(0,0,0,0.4);
            padding-top: 60px;
        }

        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 500px;
            border-radius: 15px;
        }

        .close-button {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close-button:hover,
        .close-button:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        .login-container {
            position: relative;
        }

        .popup-message {
            background-color: white; /* White background */
            color: black; /* Green text */
            text-align: center; /* Centered text */
            padding: 10px; /* Padding */
            margin-bottom: 15px; /* Bottom margin */
            border: 1px solid black; /* Green border */
            border-radius: 5px; /* Rounded corners */
            font-weight: bold; /* Bold text */
        }
    </style>
</head>
<body>
    <div class="login-container">
        <?php
        // Display reset success message if set in session
        if (isset($_SESSION['reset_success']) && $_SESSION['reset_success']) {
            echo '<div class="popup-message">Password has been successfully reset</div>';
            unset($_SESSION['reset_success']); // Clear the success message after displaying it
        }
        ?>
        <h1>POS and Inventory Management System</h1>
        <form id="login-form" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            <div class="checkbox-row">
                <a href="#" id="forgot-password-link" class="forgot-password">Forgot Password?</a>
                <a href="#" id="signup-link">Sign Up</a>
            </div>
            <button type="submit">Login</button>
        </form>
		
        <!--php
            $logo1 = 'images/APC Logo.png'; 
            $logo2 = 'images/Prito Corner Logo-modified.png'; 
        ?>
        <div class="logos">
            <img src="<?php echo $logo1; ?>" alt="APC Logo" class="logo">
            <img src="<?php echo $logo2; ?>" alt="Prito Corner Logo" class="logo">
        </div>-->
		
        <?php
        if (isset($_GET['error']) && $_GET['error'] == 'invalid_credentials') {
            echo '<p class="error-message">Invalid username or password!</p>';
        }
        if (isset($_GET['error']) && $_GET['error'] == 'registration_failed') {
            echo '<p class="error-message">Registration failed. Please try again.</p>';
        }
        if (isset($_GET['error']) && $_GET['error'] == 'reset_failed') {
            echo '<p class="error-message">Password reset failed. Please try again.</p>';
        }
        if (isset($_GET['error']) && $_GET['error'] == 'incorrect_format') {
            echo '<p class="error-message">Incorrect contact number format. It should be 11 digits.</p>';
        }
        ?>
    </div>

    <div id="signup-modal" class="modal">
        <div class="modal-content">
            <span class="close-button">&times;</span>
            <h1>Sign Up</h1>
            <form id="signup-form-modal" method="post" action="process_add_user.php">
                <label for="username">Username:</label>
                <input type="text" id="modal-username" name="username" required>
                <label for="password">Password:</label>
                <input type="password" id="modal-password" name="password" required>
                <label for="name">Name:</label>
                <input type="text" id="modal-name" name="name" required>
                <label for="contact_number">Contact Number:</label>
                <input type="text" id="modal-contact_number" name="contact_number" required pattern="\d{11}" title="Contact number must be 11 digits">
                <label for="role">Role:</label>
                <select id="modal-role" name="role" required>
                    <option value="staff">Staff</option>
                    <option value="admin">Admin</option>
                </select>
				<p><br></p>
                <h1 for="username">Data Privacy Notice</h1>
				<label for="Data Privacy">By clicking Sign Up, you agree to our data privacy policy, which outlines how we collect, use, disclose, and protect your personal information. 
				This policy ensures that your data is handled responsibly and in accordance with relevant laws and regulations, maintaining your privacy and security as our top priority.
				<a href="dataprivacy.html" target="_blank">Data Privacy Notice</a> </label>
                <button type="submit">Sign Up</button>
            </form>
        </div>
    </div>

    <div id="forgot-password-modal" class="modal">
        <div class="modal-content">
            <span class="close-button">&times;</span>
            <h1>Forgot Password</h1>
            <form id="forgot-password-form" method="post" action="process_forgot_password.php">
                <label for="forgot-username">Username:</label>
                <input type="text" id="forgot-username" name="username" required>
                <label for="new-password">New Password:</label>
                <input type="password" id="new-password" name="new_password" required>
                <label for="confirm-password">Confirm Password:</label>
                <input type="password" id="confirm-password" name="confirm_password" required>
                <button type="submit">Reset Password</button>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const signupLink = document.getElementById('signup-link');
            const forgotPasswordLink = document.getElementById('forgot-password-link');
            const signupModal = document.getElementById('signup-modal');
            const forgotPasswordModal = document.getElementById('forgot-password-modal');
            const closeButtons = document.querySelectorAll('.close-button');

            signupLink.addEventListener('click', (event) => {
                event.preventDefault();
                signupModal.style.display = 'block';
            });

            forgotPasswordLink.addEventListener('click', (event) => {
                event.preventDefault();
                forgotPasswordModal.style.display = 'block';
            });

            closeButtons.forEach(button => {
                button.addEventListener('click', () => {
                    signupModal.style.display = 'none';
                    forgotPasswordModal.style.display = 'none';
                });
            });

            window.addEventListener('click', (event) => {
                if (event.target == signupModal) {
                    signupModal.style.display = 'none';
                }
                if (event.target == forgotPasswordModal) {
                    forgotPasswordModal.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html>
