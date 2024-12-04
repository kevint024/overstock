<?php
include('db_connection.php');

// Make sure $orderId is set initially
$orderId = null;

if (isset($_GET['order_id'])) {
    $orderId = $_GET['order_id'];

    // Fetch the order to display details
    $sql = "SELECT * FROM orders WHERE order_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $orderId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $order = $result->fetch_assoc();
    } else {
        echo "Order not found.";
        exit();
    }
    $stmt->close();
} else {
    echo "No order ID provided.";
    exit();
}

// Fetch products to add to the order
$sql_products = "SELECT product_id, product_name, stock_quantity, discount_price FROM products WHERE stock_quantity > 0";
$result_products = $conn->query($sql_products);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Order Items</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <h1>Add Products to Order #<?php echo htmlspecialchars($orderId); ?></h1>

    <form action="process_order_items.php" method="POST">
        <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($orderId); ?>">

        <label for="product_id">Product:</label>
        <select id="product_id" name="product_id" required>
            <?php
            if ($result_products && $result_products->num_rows > 0) {
                while ($row = $result_products->fetch_assoc()) {
                    echo "<option value='" . $row['product_id'] . "'>" . htmlspecialchars($row['product_name']) . " (Available: " . htmlspecialchars($row['stock_quantity']) . ") - $" . htmlspecialchars($row['discount_price']) . "</option>";
                }
            } else {
                echo "<option value=''>No products available</option>";
            }
            ?>
        </select><br><br>

        <label for="quantity">Quantity:</label>
        <input type="number" id="quantity" name="quantity" min="1" required><br><br>

        <button type="submit" class="button-primary">Add Product to Order</button>
    </form>

    <br><a href="orders.php">Back to Orders</a><br>
</body>
</html>
