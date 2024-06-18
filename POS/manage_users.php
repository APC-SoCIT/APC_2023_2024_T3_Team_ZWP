<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header('Location: login.php');
    exit();
}

$servername = 'localhost';
$db_username = 'root';
$db_password = '';
$db_name = 'db1';
$conn = new mysqli($servername, $db_username, $db_password, $db_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch users data
$users = $conn->query("SELECT * FROM login WHERE role='staff'");
if (!$users) {
    die("Error fetching users: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="manageusers.css"> <!-- Include the new CSS file -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="logincontainer.css">
</head>

<body>
    <div class="container">
        <?php include 'sidebar.php'; ?>

        <div class="main">
            <div class="topbar">
                <div class="toggle">
                    <ion-icon name="menu-outline"></ion-icon>
                </div>
                <div class="search">
                    <label>
                        <input type="text" placeholder="Search here">
                        <ion-icon name="search-outline"></ion-icon>
                    </label>
                </div>
                <div class="user">
                    <a href="manage_users.php">
                        <ion-icon name="person-circle-outline"></ion-icon>
                    </a>
                </div>
            </div>

            <div class="details">
                <div class="recentInfor">
                    <div class="cardHeader">
                        <h2>Manage Users</h2>
                    </div>

                    <table>
                        <thead>
                            <tr>
                                <td>Username</td>
                                <td>Name</td>
                                <td>Contact Number</td>
                                <td>Role</td>
                                <td>Action</td>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($user = $users->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $user['username']; ?></td>
                                <td><?php echo $user['name']; ?></td>
                                <td><?php echo $user['contact_number']; ?></td>
                                <td><?php echo $user['role']; ?></td>
                                <td>
                                    <button onclick="editUser(<?php echo $user['id']; ?>, '<?php echo $user['name']; ?>', '<?php echo $user['contact_number']; ?>', '<?php echo $user['role']; ?>')">Edit</button>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>

                    <div id="editUserModal" class="modal">
                        <div class="modal-content">
                            <div class="modal-header">
                                <span class="close" onclick="closeModal()">&times;</span>
                                <h2>Edit User</h2>
                            </div>
                            <div class="modal-body">
                                <form method="post">
                                    <input type="hidden" name="user_id" id="editUserId">
                                    <label for="editName">Name</label>
                                    <input type="text" name="name" id="editName">
                                    <label for="editContactNumber">Contact Number</label>
                                    <input type="tel" name="contact_number" id="editContactNumber">
                                    <label for="editRole">Role</label>
                                    <select name="role" id="editRole">
                                        <option value="staff">Staff</option>
                                        <option value="admin">Admin</option>
                                    </select>
                                    <button type="submit" name="update_user">Update User</button>
                                </form>
                            </div>
                            <div class="modal-footer">

                            </div>
                        </div>
                    </div>

                    <script>
                        function editUser(id, name, contactNumber, role) {
                            document.getElementById('editUserId').value = id;
                            document.getElementById('editName').value = name;
                            document.getElementById('editContactNumber').value = contactNumber;
                            document.getElementById('editRole').value = role;
                            document.getElementById('editUserModal').style.display = 'block';
                        }

                        function closeModal() {
                            document.getElementById('editUserModal').style.display = 'none';
                        }
                    </script>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/js/main.js"></script>
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</body>

</html>
