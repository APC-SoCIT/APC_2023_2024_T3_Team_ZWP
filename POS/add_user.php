<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add User</title>
    <link rel="stylesheet" href="login.css">
    <style>
        /* Modal styles */
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
    </style>
</head>
<body>
    <div class="login-container">
        <h1>POS and Inventory Management System</h1>
        <form id="signup-form" method="post" action="process_add_user.php">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" required>
            <label for="contact_number">Contact Number:</label>
            <input type="text" id="contact_number" name="contact_number" required pattern="\d{11}" title="Contact number must be 11 digits">
            <label for="role">Role:</label>
            <select id="role" name="role" required>
                <option value="staff">Staff</option>
                <option value="admin">Admin</option>
            </select>
            <button type="submit">Sign Up</button>
        </form>
    </div>

    <!-- Sign Up Modal -->
    <div id="signup-modal" class="modal">
        <div class="modal-content">
            <span class="close-button">&times;</span>
            <h1>POS</h1>
            <form id="signup-form-modal" method="post" action="process_add_user.php">
				<label for="username">Username:</label>
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" required>
                <label for="contact_number">Contact Number:</label>
                <input type="text" id="contact_number" name="contact_number" required pattern="\d{11}" title="Contact number must be 11 digits">
                <label for="role">Role:</label>
                <select id="role" name="role" required>
                    <option value="staff">Staff</option>
                    <option value="admin">Admin</option>
                </select>
                <button type="submit">Sign Up</button>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const signupLink = document.getElementById('signup-link');
            const modal = document.getElementById('signup-modal');
            const closeButton = document.querySelector('.close-button');

            signupLink.addEventListener('click', () => {
                modal.style.display = 'block';
            });

            closeButton.addEventListener('click', () => {
                modal.style.display = 'none';
            });

            window.addEventListener('click', (event) => {
                if (event.target == modal) {
                    modal.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html>
