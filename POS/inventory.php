<?php
	session_start();
	if (!isset($_SESSION['username'])) {
		header('Location: login.php');
		exit();
	}
	$servername = "localhost";
	$username = "root";
	$password = "";
	$dbname = "db1";
	
	// Create connection
	$conn = new mysqli($servername, $username, $password, $dbname);
	
	// Check connection
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}
?>


<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>POS and Inventory Management</title>
		<link rel="stylesheet" href="inventory.css">
		<link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
		
	</head>
	<body>
		
		<div class="container">
			<?php include 'sidebar.php';?>
			<div class="main">
				<div class="topbar">
					<div class="toggle">
						<ion-icon name="menu-outline"></ion-icon>
					</div>
				</div>
				<div class="inventory-header">
					Inventory Details
				</div>
				<div class="details">
					<div class="recentInfor">
						<div class="cardHeader">
							<div class="button-container">
								<body>
									<input type="hidden" id="item_id" name="item_id">
									<button class="add-btn">Add</button>
									<button class="delete-btn" onclick="deleteItem()">Delete</button>
								</div>
							</div>
							<div class="inventory-table">
								<table>
									<thead>
										<tr>
											<th>Item Id</th>
											<th>Item Name</th>
											<th>Description</th>
											<th>Unit Price</th>
											<th>Quantity in stock</th>
											<th>Last Purchase</th>
											<th>Actions</th>
										</tr>
									</thead>
									<tbody>
										<?php
											$conn = mysqli_connect("localhost", "root", "", "db1");
											if (!$conn) {
												die("Connection failed: " . mysqli_connect_error());
											}
											
											// Pagination logic
											$items_per_page = 10;
											$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
											$offset = ($current_page - 1) * $items_per_page;
											
											$total_items_sql = "SELECT COUNT(*) FROM inventory";
											$total_items_result = mysqli_query($conn, $total_items_sql);
										$total_items_row = mysqli_fetch_array($total_items_result);
										$total_items = $total_items_row[0];
										$total_pages = ceil($total_items / $items_per_page);
										
										$sql = "SELECT * FROM inventory LIMIT $offset, $items_per_page";
										$result = mysqli_query($conn, $sql);
										
										if (mysqli_num_rows($result) > 0) {
										while ($row = mysqli_fetch_assoc($result)) {
										echo "<tr>";
										echo "<td style='text-align: center;'>" . $row["Item ID"] . "</td>";
										echo "<td style='text-align: center;'>" . $row["Item Name"] . "</td>";
										echo "<td style='text-align: center;'>" . $row["Description"] . "</td>";
										echo "<td style='text-align: center;'>" . $row["Unit Price"] . "</td>";
										echo "<td style='text-align: center;'>" . $row["Quantity in Stock"] . "</td>";
										echo "<td style='text-align: center;'>" . $row["Last Purchase"] . "</td>";
										echo "<td style='text-align: center;'><button class='edit-btn' data-id='" . $row["Item ID"] . "' data-name='" . $row["Item Name"] . "' data-description='" . $row["Description"] . "' data-price='" . $row["Unit Price"] . "' data-quantity='" . $row["Quantity in Stock"] . "'>Edit</button></td>";
										echo "</tr>";
										}
										} else {
										echo "<tr><td colspan='7' style='text-align: center;'>No results</td></tr>";
										}
										mysqli_close($conn);
										?>
										</tbody>
										</table>
										</div>
										<div class="pagination">
										<?php if ($current_page > 1): ?>
										<button onclick="window.location.href='?page=<?php echo ($current_page - 1); ?>'" class='pagination-prev'>
										<ion-icon name="chevron-back-outline"></ion-icon> Previous
										</button>
										<?php endif; ?>
										
										<?php for ($i = 1; $i <= $total_pages; $i++): ?>
										<?php if ($i == $current_page): ?>
										<button class='pagination-current'><?php echo $i; ?></button>
										<?php else: ?>
										<button onclick="window.location.href='?page=<?php echo $i; ?>'" class='pagination-page'><?php echo $i; ?></button>
										<?php endif; ?>
										<?php endfor; ?>
										
										<?php if ($current_page < $total_pages): ?>
										<button onclick="window.location.href='?page=<?php echo ($current_page + 1); ?>'" class='pagination-next'>
										Next <ion-icon name="chevron-forward-outline"></ion-icon>
										</button>
										<?php endif; ?>
										</div>
										
										
										</div>
										</div>
										</div>
										</div>
										
										
										<div id="edit-modal" class="modal">
										<div class="modal-content">
										<span class="close-btn">&times;</span>
										<h2>Edit Item</h2>
										<form id="edit-form" action="update item.php" method="post">
										<input type="hidden" name="item_id" id="modal-item-id">
										<label for="modal-item-name">Item Name:</label>
										<input type="text" name="item_name" id="modal-item-name"><br>
										<label for="modal-description">Description:</label>
										<input type="text" name="description" id="modal-description"><br>
										<label for="modal-unit-price">Unit Price:</label>
										<input type="text" name="unit_price" id="modal-unit-price"><br>
										<label for="modal-quantity">Quantity in Stock:</label>
										<input type="text" name="quantity" id="modal-quantity"><br>
										<input type="submit" value="Save Changes">
										</form>
										</div>
										</div>
										
										<div id="addModal" class="modal">
										<div class="modal-content">
										<span class="close-btn" onclick="closeAddModal()">&times;</span>
										<h2>Add Item</h2>
										<form action="add_item.php" method="POST">
										<label for="new_item_name">Item Name:</label>
										<input type="text" id="new_item_name" name="item_name" required>
										<label for="new_item_description">Description:</label>
										<input type="text" id="new_item_description" name="item_description" required>
										<input type="submit" value="Add Item">
										</form>
										</div>
										</div>
										
										
										<div id="add-modal" class="modal">
										<label for="add-last-purchase">Last Purchase:</label>
										<div class="input-group">
										<span class="calendar-icon">
										<ion-icon name="calendar-outline"></ion-icon>
										</span>
										</div>
										
										<div class="modal-content">
										<span class="close-btn">&times;</span>
										<h2>Add Item</h2>
										<form id="add-form" action="process_add_item.php" method="post">
										<label for="add-item-name">Item Name:</label>
										<input type="text" name="item_name" id="add-item-name" required>
										<label for="add-description">Description:</label>
										<input type="text" name="description" id="add-description" required>
										<label for="add-unit-price">Unit Price:</label>
										<input type="text" name="unit_price" id="add-unit-price" required>
										<label for="add-quantity">Quantity in Stock:</label>
										<input type="text" name="quantity" id="add-quantity" required>
										<label for="add-last-purchase">Last Purchase:</label>
										<input type="date" name="last_purchase" id="add-last-purchase" required>
										<input type="submit" value="Add Item">
										</form>
										</div>
										</div>
										
										<script>
										function deleteItem() {
										var itemId = prompt("Enter Item ID:");
										if (itemId === null || itemId.trim() === "") {
										alert("Please enter a valid item ID.");
										return;
										}
										if (confirm("Are you sure you want to delete the item with ID: " + itemId + "?")) {
										var form = document.createElement("form");
										form.method = "POST";
										form.action = "delete_item.php";
										
										var input = document.createElement("input");
										input.type = "hidden";
										input.name = "item_id";
										input.value = itemId;
										form.appendChild(input);
										
										document.body.appendChild(form);
										form.submit();
										}
										}
										
										function openAddModal() {
										// Logic to open add item modal
										console.log('Open add item modal.');
										}
										</script>
										
										<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
										<script>
										// JavaScript to handle calendar icon click
										document.querySelector('.calendar-icon').addEventListener('click', function() {
										// Open date picker when the calendar icon is clicked
										document.getElementById('add-last-purchase').focus();
										});
										
										
										document.addEventListener('DOMContentLoaded', (event) => {
										var addModal = document.getElementById("add-modal");
										var addBtn = document.querySelector('.add-btn');
										
										// Function to open add item modal
										function openAddModal() {
										addModal.style.display = "block";
										}
										
										// Close modal when clicking the close button
										document.querySelector('#add-modal .close-btn').onclick = function() {
										addModal.style.display = "none";
										}
										
										// Close modal when clicking outside the modal
										window.onclick = function(event) {
										if (event.target == addModal) {
										addModal.style.display = "none";
										}
										}
										
										// Open add item modal when clicking the add button
										addBtn.onclick = function() {
										openAddModal();
										};
										});
										</script>
										
										<script src="assets/js/main.js"></script>
										<script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
										<script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
										<script>
										document.addEventListener('DOMContentLoaded', (event) => {
										var modal = document.getElementById("edit-modal");
										var closeBtn = document.querySelector(".close-btn");
										
										function openModal(itemId, itemName, description, unitPrice, quantity) {
										document.getElementById('modal-item-id').value = itemId;
										document.getElementById('modal-item-name').value = itemName;
										document.getElementById('modal-description').value = description;
										document.getElementById('modal-unit-price').value = unitPrice;
										document.getElementById('modal-quantity').value = quantity;
										modal.style.display = "block";
										}
										
										closeBtn.onclick = function() {
										modal.style.display = "none";
										}
										
										window.onclick = function(event) {
										if (event.target == modal) {
										modal.style.display = "none";
										}
										}
										
										document.querySelectorAll('.edit-btn').forEach(button => {
										button.addEventListener('click', function() {
										var itemId = this.getAttribute('data-id');
										var itemName = this.getAttribute('data-name');
										var description = this.getAttribute('data-description');
										var unitPrice = this.getAttribute('data-price');
										var quantity = this.getAttribute('data-quantity');
										openModal(itemId, itemName, description, unitPrice, quantity);
										});
										});
										});
										</script>
										</body>
										</html>
																				