<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/overstock-daily-deals/css/styles.css">
    <title>Overstock Daily Deals</title>
</head>
<body>
    <header class="main-header">
        <div class="container">
            <h1>Overstock Daily Deals</h1>
            <nav class="navbar">
                <ul>
                    <li><a href="/overstock-daily-deals/index.php">Home</a></li>
                    <?php if (isset($_SESSION['user_id'])) { ?>
                        <li><a href="/overstock-daily-deals/user_dashboard.php">My Dashboard</a></li>
                        <?php if ($_SESSION['role'] === 'admin') { ?>
                            <li><a href="/overstock-daily-deals/admin/products.php">Manage Products</a></li>
                            <li><a href="/overstock-daily-deals/admin/orders.php">Manage Orders</a></li>
                            <li><a href="/overstock-daily-deals/admin/customers.php">Manage Customers</a></li>
                        <?php } ?>
                        <li><a href="/overstock-daily-deals/logout.php">Logout (<?php echo htmlspecialchars($_SESSION['username']); ?>)</a></li>
                    <?php } else { ?>
                        <li><a href="/overstock-daily-deals/login.php">Login</a></li>
                        <li><a href="/overstock-daily-deals/register.php">Register</a></li>
                    <?php } ?>
                </ul>
            </nav>
        </div>
    </header>
</body>
</html>
