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


<?php
include('db_connection.php');

if (isset($_GET['order_id'])) {
    $orderId = $_GET['order_id'];

    // Fetch order items for the given order ID
    $sql = "SELECT order_items.order_item_id, products.product_name, order_items.quantity, order_items.unit_price, (order_items.quantity * order_items.unit_price) AS line_total
            FROM order_items
            JOIN products ON order_items.product_id = products.product_id
            WHERE order_items.order_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $orderId);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    echo "No order ID provided.";
    exit();
}
?>

<?php include __DIR__ . '/../header.php'; ?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Order Items for Order #<?php echo htmlspecialchars($orderId); ?></title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>

<?php include __DIR__ . '/../header.php'; ?>


    <h1>Edit Order Items for Order #<?php echo htmlspecialchars($orderId); ?></h1>

    <form action="update_order_items.php" method="POST">
        <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($orderId); ?>">

        <?php
        if ($result && $result->num_rows > 0) {
            echo "<table class='order-items-table'>";
            echo "<thead><tr><th>Product Name</th><th>Quantity</th><th>Unit Price</th><th>Line Total</th><th>Actions</th></tr></thead><tbody>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['product_name']) . "</td>";
                echo "<td><input type='number' name='quantity[" . $row['order_item_id'] . "]' value='" . htmlspecialchars($row['quantity']) . "' min='1' required></td>";
                echo "<td>$" . htmlspecialchars($row['unit_price']) . "</td>";
                echo "<td>$" . htmlspecialchars($row['line_total']) . "</td>";
                echo "<td><input type='checkbox' name='delete_items[]' value='" . $row['order_item_id'] . "'> Delete</td>";
                echo "</tr>";
            }
            echo "</tbody></table>";
        } else {
            echo "<p>No items found for this order.</p>";
        }

        // Adding a new product to the order
        echo "<h2>Add New Product to Order</h2>";
        echo "<label for='new_product_id'>Product:</label>";
        $sql_products = "SELECT product_id, product_name, stock_quantity, discount_price FROM products WHERE stock_quantity > 0";
        $result_products = $conn->query($sql_products);

        if ($result_products && $result_products->num_rows > 0) {
            echo "<select id='new_product_id' name='new_product_id'>";
            while ($row = $result_products->fetch_assoc()) {
                echo "<option value='" . $row['product_id'] . "'>" . htmlspecialchars($row['product_name']) . " (Available: " . htmlspecialchars($row['stock_quantity']) . ") - $" . htmlspecialchars($row['discount_price']) . "</option>";
            }
            echo "</select><br><br>";

            echo "<label for='new_quantity'>Quantity:</label>";
            echo "<input type='number' id='new_quantity' name='new_quantity' min='1'><br><br>";
        } else {
            echo "<p>No products available to add.</p>";
        }

        $conn->close();
        ?>

        <button type="submit" class="button-primary">Update Order Items</button>
    </form>

    <br><a href="orders.php">Back to Orders</a><br>
</body>
</html>
