<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS and Inventory Management</title>
    <link rel="stylesheet" href="style.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="container">
        <?php include 'sidebar.php'; ?>

        <div class="main">
			<div class="topbar">
                <div class="toggle">
                    <ion-icon name="menu-outline"></ion-icon>
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
							<h2>Sales Statistics</h2>
						</div>
						<div>
							<canvas id="salesChart" width="100" height="50"></canvas>
						</div>
					</div>
				</div>
				
				<div class="details">
					<div class="recentInfor">
						<div class="cardHeader">
							<h2>Product Overview</h2>
						</div>
					</div>
				</div>

				<div class="details">
					<div class="recentInfor">
						<div class="cardHeader">
							<h2>Sales Statistics</h2>
						</div>
					</div>
				</div>

        </div>
    </div>

    <script src="assets/js/main.js"></script>
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>

    <script>
        // JavaScript for fetching data and creating the chart
        // This script will fetch data from your PHP file (sales_data.php) and create a bar chart

        // Fetch data asynchronously
        async function fetchData() {
            const response = await fetch('sales_data.php');
            const data = await response.json();
            return data;
        }

        // Function to initialize the chart
        async function initChart() {
            const data = await fetchData();

            const ctx = document.getElementById('salesChart').getContext('2d');
            const salesChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: data.dates,
                    datasets: [{
                        label: 'Total Sales',
                        data: data.totals,
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }

        // Call the function to initialize the chart
        initChart();
    </script>
</body>
</html>

