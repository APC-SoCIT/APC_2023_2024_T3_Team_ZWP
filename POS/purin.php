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
	
	$conn = new mysqli($servername, $username, $password, $dbname);
	
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}

	$sql = "SELECT PurchaseDate, ReferenceNumber, ItemName, Quantity, Price, Total, PaymentMethod FROM purchaseinvoice";
	$result = $conn->query($sql);
	
	$invoices = array();
	
	if ($result) {
		if ($result->num_rows > 0) {
			while ($row = $result->fetch_assoc()) {
				$invoices[] = $row;
			}
			} else {
			echo "0 results";
		}
		} else {
		echo "Error: " . $conn->error;
	}
	
	$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>POS and Inventory Management</title>
		<link rel="stylesheet" href="purin.css">
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
				
				<div class="purin-header">
					Purchase Invoices
				</div>				
				
				<div class="details">
					<div class="recentInfor">
						
						<div class="cardBody">
							<table>
								<thead>
									<tr>
										<th>Purchase Date</th>
										<th>Reference Number</th>
										<th>Item Name</th>
										<th>Quantity</th>
										<th>Price</th>
										<th>Total</th>
										<th>Payment Method</th>
									</tr>
								</thead>
								<tbody>
									<?php foreach ($invoices as $invoice): ?>
									<tr>
										<td><?php echo $invoice['PurchaseDate']; ?></td>
										<td><?php echo $invoice['ReferenceNumber']; ?></td>
										<td><?php echo $invoice['ItemName']; ?></td>
										<td><?php echo $invoice['Quantity']; ?></td>
										<td>₱<?php echo $invoice['Price']; ?></td>
										<td>₱<?php echo $invoice['Total']; ?></td>
										<td><?php echo $invoice['PaymentMethod']; ?></td>
									</tr>
									<?php endforeach; ?>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</body>
</html>
<script src="assets/js/main.js"></script>
<script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</body>
</html>
