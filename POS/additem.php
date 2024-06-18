<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Item</title>
</head>
<body>
    <h2>Add Item</h2>
    <form action="process_add_item.php" method="post">
        <label for="item_name">Item Name:</label><br>
        <input type="text" id="item_name" name="item_name" required><br>
        
        <label for="description">Description:</label><br>
        <input type="text" id="description" name="description" required><br>
        
        <label for="unit_price">Unit Price:</label><br>
        <input type="number" id="unit_price" name="unit_price" step="0.01" required><br>
        
        <label for="quantity">Quantity in Stock:</label><br>
        <input type="number" id="quantity" name="quantity" required><br>
        
        <label for="last_purchase">Last Purchase:</label><br>
        <input type="date" id="last_purchase" name="last_purchase" required><br><br>
        
        <input type="submit" value="Add Item">
    </form>
</body>
</html>
