<?php
$item_id = $_GET['id'];

$conn = mysqli_connect("localhost", "root", "", "db1");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$sql = "SELECT * FROM inventory WHERE `Item ID` = $item_id";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $item_name = $row['Item Name'];
	$description = $row['Description'];
	$item_id = $row['Item ID'];
} else {

    echo "Item not found";
    exit; 
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Item</title>
    <style>
        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        form {
            max-width: 400px;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        label {
            display: block;
            margin-bottom: 10px;
        }
        input[type="text"] {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        input[type="submit"] {
            width: 100%;
            padding: 10px;
            border: none;
            border-radius: 4px;
            background-color: #4CAF50;
            color: white;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #45a049;
        }
    </style>
	</head>
<body>
<div class="container">
<form action="update item.php" method="post">
	<label for="item_id">Item ID:</label>
    <input type="text" name="item_id" id="item_id" value="<?php echo $item_id; ?>"><br>
    <input type="hidden" name="item_id" value="<?php echo $item_id; ?>">
    <label for="item_name">Item Name:</label>
    <input type="text" name="item_name" id="item_name" value="<?php echo $item_name; ?>"><br>
	<label for="description">Description:</label>
    <input type="text" name="description" id="description" value="<?php echo $description; ?>"><br>
    <input type="submit" value="Save Changes">
</form>
</body>
</html>