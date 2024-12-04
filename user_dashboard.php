<?php
session_start();
include __DIR__ . '/admin/db_connection.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Retrieve orders for the logged-in user
$sql_orders = "SELECT * FROM orders WHERE customer_id IN (SELECT customer_id FROM customers WHERE user_id = ?)";
$stmt_orders = $conn->prepare($sql_orders);
$stmt_orders->bind_param("i", $user_id);
$stmt_orders->execute();
$result_orders = $stmt_orders->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="/overstock-daily-deals/css/styles.css">
</head>
<body>
    <?php include __DIR__ . '/header.php'; ?>

    <div class="content">
        <h1>User Dashboard</h1>
        <p>Welcome to your dashboard, <?php echo htmlspecialchars($_SESSION['username']); ?>. Here are your orders:</p>

        <?php if ($result_orders->num_rows > 0) { ?>
            <table>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Order Date</th>
                        <th>Total Amount</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($order = $result_orders->fetch_assoc()) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($order['order_id']); ?></td>
                            <td><?php echo htmlspecialchars($order['order_date']); ?></td>
                            <td><?php echo htmlspecialchars($order['total_amount']); ?></td>
                            <td><?php echo htmlspecialchars($order['status']); ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        <?php } else { ?>
            <p>You have no orders at this time.</p>
        <?php } ?>
    </div>
</body>
</html>

<?php
$stmt_orders->close();
$conn->close();
?>
