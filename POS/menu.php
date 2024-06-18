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

$sql = "SELECT `Menu ID`, `Item Name`, `Description`, `Unit Price`, `Image Path` FROM menu";
$result = $conn->query($sql);

$menu_items = array();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $menu_items[] = $row;
    }
} else {
    echo "0 results";
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
<link rel="stylesheet" href="menu.css">
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

			<div class="search">
                <label>
                    <input type="text" id="search-input" placeholder="Search here" onkeyup="searchMenu()">
                    <ion-icon name="search-outline"></ion-icon>
                </label>
            </div>
        </div>
        <div class="details">
            <div class="cardBox">
                <div class="card">
                    <div class="cardHeader">
                        <h2>Menu</h2>
                    </div>
                    <div class="cardBody menu-grid">
                        <?php foreach ($menu_items as $item): ?>
                            <div class="menu-item" data-id="<?php echo $item['Menu ID']; ?>" data-name="<?php echo $item['Item Name']; ?>" data-price="<?php echo $item['Unit Price']; ?>">
                                <div class="item-image">
                                    <img src="<?php echo $item['Image Path'] ?: 'path/to/default/image.jpg'; ?>" alt="<?php echo $item['Item Name']; ?>">
                                </div>
                                <div class="item-details">
                                    <span><?php echo $item['Item Name']; ?></span>
                                </div>
                                <div class="item-price">₱<?php echo $item['Unit Price']; ?></div>
                                <div class="quantity">
                                    <button class="minus">-</button>
                                    <input type="text" class="quantity-input" value="0">
                                    <button class="add">+</button>
                                </div>
                                <button class="add-to-cart">Add</button>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Cart icon with cart counter -->
<div class="cart-icon" onclick="toggleCart()">
    <ion-icon name="cart-outline"></ion-icon>
    <span id="cart-counter" class="cart-counter">0</span>
</div>

<div class="cart" id="cart">
    <div class="cartHeader">
        <h2>Cart</h2>
        <ion-icon name="close-outline" class="close-icon" onclick="toggleCart()"></ion-icon>
    </div>
    <div class="cartBody" id="cart-items">
        <!-- Ordered items will be added here -->
    </div>
    <div class="cartFooter">
        <button onclick="checkout()">Confirm Order</button>
        <button onclick="clearOrder()">Clear Order</button>
  <!-- Payment Method Section -->
<div class="payment-section" id="payment-section" style="display: none;">
    <h3>Payment Method</h3>
    <label>
        <input type="radio" name="payment-method" value="cash">
        <img src="/pos/POSSSSS/images/cash-icon.png" alt="Cash Icon" class="payment-icon"> Cash
    </label>
    <label>
        <input type="radio" name="payment-method" value="gcash">
        <img src="/pos/POSSSSS/images/gcash-icon.png" alt="GCash Icon" class="payment-icon"> GCash
    </label>
    <button onclick="confirmPayment()">Checkout</button>
</div>

    </div>
    <div class="cartFooter" id="receipt-section" style="display: none;">
        <h3>Receipt</h3>
        <div id="receipt"></div>
    </div>
</div>


<script src="https://unpkg.com/ionicons@5.5.2/dist/ionicons.js"></script>
<script src="menu.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const menuItems = document.querySelectorAll('.menu-item');
    const orderList = document.getElementById('cart-items');
    const receipt = document.getElementById('receipt');
    const cart = document.getElementById('cart');
    const cartIcon = document.querySelector('.cart-icon');
    const closeIcon = document.querySelector('.close-icon');
    let totalItemsInCart = 0;

    menuItems.forEach(item => {
        const addButton = item.querySelector('.add-to-cart');
        const minusButtonQuantity = item.querySelector('.minus');
        const addButtonQuantity = item.querySelector('.add');
        const quantityInput = item.querySelector('.quantity-input');

        if (addButton) {
            addButton.addEventListener('click', () => {
                const id = item.getAttribute('data-id');
                const name = item.getAttribute('data-name');
                const price = parseFloat(item.getAttribute('data-price'));
                const quantity = parseInt(quantityInput.value) || 0;
                if (quantity > 0) {
                    addToCart(id, name, price, quantity);
                }
            });
        }

        if (addButtonQuantity && minusButtonQuantity) {
            addButtonQuantity.addEventListener('click', () => {
                let quantity = parseInt(quantityInput.value) || 0;
                quantityInput.value = quantity < 1 ? 1 : quantity + 1;
            });

            minusButtonQuantity.addEventListener('click', () => {
                let quantity = parseInt(quantityInput.value) || 0;
                if (quantity > 0) {
                    quantityInput.value = quantity - 1;
                }
            });
        }
    });

    function addToCart(id, name, price, quantity) {
        let existingItem = orderList.querySelector(`.cart-item[data-id='${id}']`);

        if (existingItem) {
            const itemText = existingItem.querySelector('span');
            let currentQuantity = parseInt(existingItem.getAttribute('data-quantity')) || 0;
            currentQuantity += quantity;
            existingItem.setAttribute('data-quantity', currentQuantity);
            itemText.textContent = `${currentQuantity} ${name} - ₱${(price * currentQuantity).toFixed(2)}`;
        } else {
            const newItem = createCartItem(id, name, price, quantity);
            orderList.appendChild(newItem);
        }

        totalItemsInCart += quantity;
        updateCartCounter();
    }

    function createCartItem(id, name, price, quantity) {
        const newItem = document.createElement('div');
        newItem.classList.add('cart-item');
        newItem.setAttribute('data-id', id);
        newItem.setAttribute('data-quantity', quantity);

        const itemText = document.createElement('span');
        const removeButton = document.createElement('span');

        itemText.textContent = `${quantity} ${name} - ₱${(price * quantity).toFixed(2)}`;

        removeButton.textContent = '-';
        removeButton.classList.add('remove-item');
        removeButton.addEventListener('click', () => {
            let currentQuantity = parseInt(newItem.getAttribute('data-quantity')) || 0;
            if (currentQuantity > 1) {
                currentQuantity--;
                newItem.setAttribute('data-quantity', currentQuantity);
                itemText.textContent = `${currentQuantity} ${name} - ₱${(price * currentQuantity).toFixed(2)}`;
            } else {
                newItem.remove();
            }
            totalItemsInCart--;
            updateCartCounter();
        });

        newItem.appendChild(itemText);
        newItem.appendChild(removeButton);

        return newItem;
    }

    function updateCartCounter() {
        const cartCounter = document.querySelector('.cart-counter');
        if (cartCounter) {
            cartCounter.textContent = totalItemsInCart;
        }
    }


    function checkout() {

        if (totalItemsInCart === 0) {
            alert('Your cart is empty. Please add items before checking out.');
            return;
        }

        let total = 0;
        let items = [];

        orderList.querySelectorAll('.cart-item').forEach(item => {
            const itemId = item.getAttribute('data-id');
            const itemText = item.textContent.trim();
            const quantity = parseInt(item.getAttribute('data-quantity'));
            const itemTextParts = itemText.split(' - ');
            const priceText = itemTextParts[itemTextParts.length - 1].replace('₱', '');
            const totalPrice = parseFloat(priceText);
            const price = totalPrice / quantity;

            if (!isNaN(price)) {
                total += totalPrice;
            } else {
                console.error(`Invalid price calculation for item ${itemId}`);
            }

            items.push({ id: itemId, quantity: quantity });
        });

        receipt.textContent = `Total: ₱${total.toFixed(2)}`;

        showPaymentOptions();
    }

    function showPaymentOptions() {
        const paymentSection = document.getElementById('payment-section');
        paymentSection.style.display = 'block';
    }

    function confirmPayment() {
        const selectedPaymentMethod = document.querySelector('input[name="payment-method"]:checked');

        if (!selectedPaymentMethod) {
            alert('Please select a payment method.');
            return;
        }

        const paymentMethod = selectedPaymentMethod.value;

        let totalAmount = 0;
        const cartItems = [];
        const displayItems = [];

        orderList.querySelectorAll('.cart-item').forEach(item => {
            const itemText = item.textContent.trim();
            const itemName = itemText.split(' - ')[0];
            const itemPrice = parseFloat(itemText.split(' - ')[1].replace('₱', ''));
            const itemId = item.getAttribute('data-id');
            const quantity = parseInt(item.getAttribute('data-quantity'));

            totalAmount += itemPrice;

            cartItems.push({ id: itemId, quantity: quantity });
            displayItems.push({ name: itemName, quantity: quantity });
        });

        let confirmationMessage = `Confirm Payment:
    Total Amount: ₱${totalAmount.toFixed(2)}
    Payment Method: ${paymentMethod.toUpperCase()}

    Items Ordered:
    `;

        displayItems.forEach(item => {
            confirmationMessage += `    ${item.name}\n`;
        });

        alert(confirmationMessage);

        updateInventory(cartItems, totalAmount, paymentMethod);
    }

function updateInventory(cartItems, totalAmount, paymentMethod) {
    const requestData = {
        items: cartItems,
        totalAmount: totalAmount,
        paymentMethod: paymentMethod
    };

    fetch('general_update.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(requestData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Inventory updated successfully');
            generatePurchaseInvoice(data.invoice); 
        } else {
            alert('Failed to update inventory: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while updating inventory.');
    });
}

function generatePurchaseInvoice(invoiceData) {
    console.log('Generated Purchase Invoice:', invoiceData);
}

    function clearOrder() {
        orderList.innerHTML = '';
        receipt.textContent = '';
        hidePaymentOptions();
        totalItemsInCart = 0;
        updateCartCounter();
    }

    function hidePaymentOptions() {
        const paymentSection = document.getElementById('payment-section');
        paymentSection.style.display = 'none';
    }

    function toggleCart() {
        cart.classList.toggle('active');
        if (closeIcon) {
            closeIcon.classList.toggle('active');
        }
    }

    if (cartIcon && closeIcon) {
        cartIcon.addEventListener('click', toggleCart);
        closeIcon.addEventListener('click', toggleCart);
    }

    const checkoutButton = document.querySelector('.cartFooter button:nth-child(1)');
    const clearOrdersButton = document.querySelector('.cartFooter button:nth-child(2)');
    if (checkoutButton && clearOrdersButton) {
        checkoutButton.addEventListener('click', checkout);
        clearOrdersButton.addEventListener('click', clearOrder);
    }

    const confirmPaymentButton = document.querySelector('#payment-section button');
    if (confirmPaymentButton) {
        confirmPaymentButton.addEventListener('click', confirmPayment);
    }
});

	function searchMenu() {
    const searchInput = document.getElementById('search-input').value.toLowerCase();
    const menuItems = document.querySelectorAll('.menu-item');

    menuItems.forEach(item => {
        const itemName = item.getAttribute('data-name').toLowerCase();
        if (itemName.includes(searchInput)) {
            item.style.display = '';
        } else {
            item.style.display = 'none';
        }
    });
}




</script>

<script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</body>
</html>

