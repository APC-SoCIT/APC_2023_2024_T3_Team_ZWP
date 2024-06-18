<div class="navigation">
    <ul>
        <li>
            <a href="#">
                <span class="icon">
                    <ion-icon name="logo-vue"></ion-icon>
                </span>
                <span class="title">POS and Inventory System</span>
            </a>
        </li>
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
        <li>
            <a href="index.php">
                <span class="icon">
                    <ion-icon name="home-outline"></ion-icon>
                </span>
                <span class="title">Dashboard</span>
            </a>
        </li>
        <li>
            <a href="menu.php">
                <span class="icon">
                    <ion-icon name="cash-outline"></ion-icon>
                </span>
                <span class="title">Menu</span>
            </a>
        </li>
        <li>
            <a href="inventory.php">
                <span class="icon">
                    <ion-icon name="list-outline"></ion-icon>
                </span>
                <span class="title">Inventory</span>
            </a>
        </li>
        <li>
            <a href="Sales.php">
                <span class="icon">
                    <ion-icon name="person-outline"></ion-icon>
                </span>
                <span class="title">Sales Report</span>
            </a>
        </li>
		<li>
            <a href="purin.php">
                <span class="icon">
                    <ion-icon name="person-outline"></ion-icon>
                </span>
                <span class="title">Purchase Invoice</span>
            </a>
        </li>
        <li>
            <a href="logout.php">
                <span class="icon">
                    <ion-icon name="log-out-outline"></ion-icon>
                </span>
                <span class="title">Logout</span>
            </a>
        </li>
        <?php endif; ?>
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'staff'): ?>
		<li>
            <a href="index.php">
                <span class="icon">
                    <ion-icon name="home-outline"></ion-icon>
                </span>
                <span class="title">Dashboard</span>
            </a>
        </li>
        <li>
            <a href="menu.php">
                <span class="icon">
                    <ion-icon name="cash-outline"></ion-icon>
                </span>
                <span class="title">Menu</span>
            </a>
        </li>
		<li>
            <a href="inventory.php">
                <span class="icon">
                    <ion-icon name="list-outline"></ion-icon>
                </span>
                <span class="title">Inventory</span>
            </a>
        </li>
        <li>
            <a href="logout.php">
                <span class="icon">
                    <ion-icon name="log-out-outline"></ion-icon>
                </span>
                <span class="title">Logout</span>
            </a>
        </li>
        <?php endif; ?>
    </ul>
</div>
