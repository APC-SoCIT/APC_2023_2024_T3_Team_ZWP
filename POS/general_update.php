<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "db1";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Connection failed: ' . $conn->connect_error]));
}

// Decode incoming JSON data
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['items']) || !is_array($data['items']) || empty($data['items'])) {
    echo json_encode(['success' => false, 'message' => 'No valid items provided']);
    exit;
}

// Initialize variables for sales report calculation
$total_sales = 0;
$items_sold = 0;

// Start transaction
$conn->begin_transaction();

try {
    foreach ($data['items'] as $item) {
        $menu_id = $item['id'];
        $order_quantity = $item['quantity'];

        // Retrieve item name and price from the menu table
        $getItemInfoSql = "SELECT `Item Name`, `Unit Price` FROM menu WHERE `Menu ID` = ?";
        $stmt = $conn->prepare($getItemInfoSql);
        if ($stmt === false) {
            throw new Exception('SQL error (menu select): ' . $conn->error);
        }
        $stmt->bind_param("i", $menu_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $itemName = $row['Item Name'];
            $price = $row['Unit Price'];

            // Calculate total sales for the items
            $total_sales += $price * $order_quantity;
            $items_sold++;
        } else {
            throw new Exception('Item not found in menu table for Menu ID: ' . $menu_id);
        }

        // Retrieve the list of ingredient IDs and quantities for the given menu item ID
        $sql = "SELECT `ItemID`, `Quantity` FROM menu_ingredients WHERE `MenuID` = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            throw new Exception('SQL error (menu_ingredients): ' . $conn->error);
        }
        $stmt->bind_param("i", $menu_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $ingredient_id = $row['ItemID'];
                $quantity_per_unit = $row['Quantity'];
                $total_quantity = $order_quantity * $quantity_per_unit;

                // Check if subtracting the ordered quantity will result in a negative stock
                $check_sql = "SELECT `Quantity in Stock` FROM inventory WHERE `Item ID` = ?";
                $check_stmt = $conn->prepare($check_sql);
                if ($check_stmt === false) {
                    throw new Exception('SQL error (inventory check): ' . $conn->error);
                }
                $check_stmt->bind_param("i", $ingredient_id);
                $check_stmt->execute();
                $check_result = $check_stmt->get_result();

                if ($check_result->num_rows > 0) {
                    $row = $check_result->fetch_assoc();
                    $current_stock = $row['Quantity in Stock'];

                    // Ensure sufficient stock available
                    if ($current_stock >= $total_quantity) {
                        // Update the inventory quantity for each ingredient
                        $update_sql = "UPDATE inventory SET `Quantity in Stock` = `Quantity in Stock` - ? WHERE `Item ID` = ?";
                        $update_stmt = $conn->prepare($update_sql);
                        if ($update_stmt === false) {
                            throw new Exception('SQL error (inventory update): ' . $conn->error);
                        }
                        $update_stmt->bind_param("ii", $total_quantity, $ingredient_id);
                        $update_stmt->execute();

                        if ($update_stmt->affected_rows === 0) {
                            throw new Exception('No rows updated, check inventory ID and stock for Item ID: ' . $ingredient_id);
                        }
                    } else {
                        throw new Exception('Insufficient stock for Item ID: ' . $ingredient_id);
                    }
                } else {
                    throw new Exception('Inventory record not found for Item ID: ' . $ingredient_id);
                }
            }
        } else {
            throw new Exception('No ingredients found for menu item ID: ' . $menu_id);
        }
    }

    // Calculate net profit (assuming you have COGS calculation logic)
    $net_profit = $total_sales - calculateCOGS($data['items']);

    // Generate purchase invoice
    $purchaseDate = date('Y-m-d H:i:s');
    $referenceNumber = generateUniqueReferenceNumber($conn);
    $invoiceItems = [];

    foreach ($data['items'] as $item) {
        $itemName = getItemName($item['id']); // Example function to get item name from database based on item ID
        $quantity = $item['quantity'];
        $price = getItemPrice($item['id']); // Example function to get item price from database based on item ID
        $total = $price * $quantity;

        $invoiceItems[] = [
            'itemName' => $itemName,
            'quantity' => $quantity,
            'price' => $price,
            'total' => $total
        ];
    }

    // Insert purchase invoice data into database
    $insert_sql = "INSERT INTO purchaseinvoice (PurchaseDate, ReferenceNumber, ItemName, Quantity, Price, Total, PaymentMethod) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $insert_stmt = $conn->prepare($insert_sql);
    if ($insert_stmt === false) {
        throw new Exception('SQL error (purchaseinvoice insert): ' . $conn->error);
    }

    foreach ($invoiceItems as $item) {
        $itemName = $item['itemName'];
        $quantity = $item['quantity'];
        $price = $item['price'];
        $total = $item['total'];

        $paymentMethod = strtoupper($data['paymentMethod']); // Convert payment method to uppercase

        $insert_stmt->bind_param("sssiids", $purchaseDate, $referenceNumber, $itemName, $quantity, $price, $total, $paymentMethod);
        $insert_stmt->execute();

        if ($insert_stmt->affected_rows === 0) {
            throw new Exception('Failed to insert purchase invoice data');
        }
    }

    // Insert sales report data into database
    $reportDate = date('Y-m-d H:i:s');
    $insert_sales_sql = "INSERT INTO sales_report (Date, TotalSales, NetProfit, ItemsSold) VALUES (?, ?, ?, ?)";
    $insert_sales_stmt = $conn->prepare($insert_sales_sql);
    if ($insert_sales_stmt === false) {
        throw new Exception('SQL error (sales_report insert): ' . $conn->error);
    }

    $insert_sales_stmt->bind_param("sdds", $reportDate, $total_sales, $net_profit, $items_sold);
    $insert_sales_stmt->execute();

    if ($insert_sales_stmt->affected_rows === 0) {
        throw new Exception('Failed to insert sales report data');
    }

    // Commit transaction if all queries succeed
    $conn->commit();

    // Prepare response data with reference number and other invoice details
    $response = [
        'success' => true,
        'message' => 'Inventory updated, purchase invoice generated, sales report generated successfully',
        'referenceNumber' => $referenceNumber,
        'invoiceItems' => $invoiceItems
    ];

    // Successful execution
    echo json_encode($response);

} catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollback();

    // Error message
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

// Close database connection
$conn->close();

// Function to generate a unique reference number
function generateUniqueReferenceNumber($conn) {
    do {
        $referenceNumber = (string)microtime(true); // Get the current time in microseconds since Unix epoch as a float

        // Check if this reference number already exists in the database
        $check_sql = "SELECT 1 FROM purchaseinvoice WHERE ReferenceNumber = ?";
        $check_stmt = $conn->prepare($check_sql);
        if ($check_stmt === false) {
            throw new Exception('SQL error (reference check): ' . $conn->error);
        }
        $check_stmt->bind_param("s", $referenceNumber);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
    } while ($check_result->num_rows > 0); // Repeat if the reference number already exists

    return $referenceNumber;
}

// Example function to get item name from database based on item ID
function getItemName($itemId) {
    global $conn;
    $getItemNameSql = "SELECT `Item Name` FROM menu WHERE `Menu ID` = ?";
    $stmt = $conn->prepare($getItemNameSql);
    if ($stmt === false) {
        throw new Exception('SQL error (getItemName select): ' . $conn->error);
    }
    $stmt->bind_param("i", $itemId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['Item Name'];
    } else {
        throw new Exception('Item Name not found for ID: ' . $itemId);
    }
}

// Example function to get item price from database based on item ID
function getItemPrice($itemId) {
    global $conn;
    $getItemPriceSql = "SELECT `Unit Price` FROM menu WHERE `Menu ID` = ?";
    $stmt = $conn->prepare($getItemPriceSql);
    if ($stmt === false) {
        throw new Exception('SQL error (getItemPrice select): ' . $conn->error);
    }
    $stmt->bind_param("i", $itemId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['Unit Price'];
    } else {
        throw new Exception('Unit Price not found for ID: ' . $itemId);
    }
}

// Example function to calculate COGS (Cost of Goods Sold)
function calculateCOGS($items) {
    // Replace with your logic to calculate COGS based on items sold
    return 0; // Example return, replace with actual calculation
}
?>
