<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Ensure only admins have access to the admin pages
if ($_SESSION['role'] !== 'admin') {
    echo "Access denied. You do not have permission to access this page.";
    exit();
}
?>

<?php include __DIR__ . '/../header.php'; ?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders Management</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php include __DIR__ . '/../header.php'; ?>




    <div class="container">
        <!-- Add Order Section -->
        <section class="add-order">
            <h2>Add New Order</h2>
            <form action="add_order.php" method="POST" class="order-form">
                <label for="customer_id">Customer:</label>
                <select id="customer_id" name="customer_id" required>
                    <?php
                    include('db_connection.php');

                    // Fetch all customers to populate dropdown
                    $sql = "SELECT customer_id, first_name, last_name FROM customers";
                    $result = $conn->query($sql);

                    if ($result && $result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<option value='" . $row['customer_id'] . "'>" . htmlspecialchars($row['first_name']) . " " . htmlspecialchars($row['last_name']) . "</option>";
                        }
                    } else {
                        echo "<option value=''>No customers available</option>";
                    }

                    $conn->close();
                    ?>
                </select><br><br>

                <label for="order_notes">Order Notes:</label>
                <textarea id="order_notes" name="order_notes"></textarea><br><br>

                <button type="submit" class="button-primary">Add Order</button>
            </form>
        </section>

        <!-- Current Orders Section -->
        <section class="current-orders">
            <h2>Current Orders</h2>
           
    <?php
    include('db_connection.php');

    // Fetch all orders from the database
    $sql = "SELECT orders.order_id, customers.first_name, customers.last_name, orders.order_date, orders.total_amount, orders.status FROM orders
        JOIN customers ON orders.customer_id = customers.customer_id";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        echo "<table class='orders-table'>";
        echo "<thead><tr><th>Order ID</th><th>Customer</th><th>Order Date</th><th>Total Amount</th><th>Status</th><th>Actions</th></tr></thead><tbody>";
        while($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['order_id']) . "</td>";
        echo "<td>" . htmlspecialchars($row['first_name']) . " " . htmlspecialchars($row['last_name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['order_date']) . "</td>";
        echo "<td>$" . htmlspecialchars($row['total_amount']) . "</td>";
        echo "<td>" . htmlspecialchars($row['status']) . "</td>";
        echo "<td>
            <a href='update_order.php?id=" . $row['order_id'] . "' class='button-edit'>Edit</a> | 
            <a href='delete_order.php?id=" . $row['order_id'] . "' class='button-delete' onclick='return confirm(\"Are you sure you want to delete this order?\");'>Delete</a> | 
            <a href='add_order_items.php?order_id=" . $row['order_id'] . "' class='button-add'>Add Items</a> |
            <a href='edit_order_items.php?order_id=" . $row['order_id'] . "' class='button-edit'>Edit Items</a>
            </td>";
        echo "</tr>";
        }
        echo "</tbody></table>";
    } else {
        echo "<p>No orders found.</p>";
    }

    $conn->close();
    ?>

        </section>
    </div>

</body>
</html>
