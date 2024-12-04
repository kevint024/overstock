<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in
if (isset($_SESSION['user_id'])) {
    $username = htmlspecialchars($_SESSION['username']);
    $role = htmlspecialchars($_SESSION['role']);
} else {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/overstock-daily-deals/css/styles.css">
</head>
<body>
    <header class="main-header">
        <div class="container">
            <h2>Welcome, <?php echo $username; ?>!</h2>
            <nav class="navbar">
                <ul>
                    <li><a href="/overstock-daily-deals/index.php">Home</a></li>
                    <?php if ($role === 'admin') { ?>
                        <li><a href="/overstock-daily-deals/admin/orders.php">Manage Orders</a></li>
                        <li><a href="/overstock-daily-deals/admin/products.php">Manage Products</a></li>
                        <li><a href="/overstock-daily-deals/admin/customers.php">Manage Customers</a></li>
                    <?php } ?>
                    <li><a href="/overstock-daily-deals/logout.php">Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>
</body>
</html>
