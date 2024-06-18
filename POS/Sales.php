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

    $date_range = isset($_GET['date_range']) ? $_GET['date_range'] : '';
    $start_date = '';
    $end_date = '';

    if (!empty($date_range)) {
        $dates = explode(' - ', $date_range);
        if (count($dates) == 2) {
            $start_date = $dates[0];
            $end_date = $dates[1];
        }
    }

    $sql = "SELECT * FROM sales_report WHERE 1=1";
    
    if (!empty($start_date) && !empty($end_date)) {
        $sql .= " AND Date >= '$start_date' AND Date <= '$end_date'";
    }

    $result = $conn->query($sql);

    ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS and Inventory Management</title>

    <link rel="stylesheet" href="Sales.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
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

            <div class="details">
                <div class="recentInfor">    
                    <div class="cardHeader">
                        <h2>Sales Reports</h2>
                    </div>

                    <form method="GET" action="">
                        <label for="date_range">Date Range:</label>
                        <input type="text" id="date_range" name="date_range" placeholder="Select date range" value="<?php echo isset($_GET['date_range']) ? $_GET['date_range'] : ''; ?>">
                        <button type="submit">Filter</button>
                    </form>

                    <div class="inventory-table">
                        <table>
                            <thead>
                                <tr>
                                    <th>Report ID</th>
                                    <th>Date</th>
                                    <th>Total Sales</th>
                                    <th>Net Profit</th>
                                    <th>Items Sold</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<tr>";
                                        echo "<td>" . $row["ReportID"] . "</td>";
                                        echo "<td>" . $row["Date"] . "</td>";
                                        echo "<td>" . $row["TotalSales"] . "</td>";
                                        echo "<td>" . $row["NetProfit"] . "</td>";
                                        echo "<td>" . $row["ItemsSold"] . "</td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='5'>No sales report available</td></tr>";
                                }

                                $conn->close();
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/js/main.js"></script>
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>

    <script type="text/javascript">
        $(function() {
            $('#date_range').daterangepicker({
                locale: {
                    format: 'YYYY-MM-DD'
                },
                startDate: moment().startOf('month'),
                endDate: moment().endOf('month'),
                ranges: {
                   'Today': [moment(), moment()],
                   'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                   'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                   'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                   'This Month': [moment().startOf('month'), moment().endOf('month')],
                   'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                }
            });
        });
    </script>
</body>
</html>
