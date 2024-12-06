<?php
session_start();

// Include the database connection file
include_once __DIR__ . '/../admin/db_connection.php';

// Handle form submission to update order items
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $orderItemId = $_POST['order_item_id'];
    $productId = $_POST['product_id'];
    $quantity = $_POST['quantity'];

    // Fetch the correct unit price from the products table
    $productQuery = "SELECT discount_price FROM products WHERE product_id = ?";
    $stmt = $conn->prepare($productQuery);
    if (!$stmt) {
        die("Database error: " . $conn->error);
    }
    $stmt->bind_param("i", $productId);
    $stmt->execute();
    $productResult = $stmt->get_result();

    if ($productResult->num_rows > 0) {
        $product = $productResult->fetch_assoc();
        $unitPrice = $product['discount_price']; // Fetch correct price

        // Calculate the new line total
        $lineTotal = $unitPrice * $quantity;

        // Update the order_items table
        $updateQuery = "UPDATE order_items SET quantity = ?, unit_price = ?, line_total = ? WHERE order_item_id = ?";
        $updateStmt = $conn->prepare($updateQuery);
        if (!$updateStmt) {
            die("Database error: " . $conn->error);
        }
        $updateStmt->bind_param("idii", $quantity, $unitPrice, $lineTotal, $orderItemId);

        if ($updateStmt->execute()) {
            $_SESSION['message'] = "Order item updated successfully.";
        } else {
            $_SESSION['error'] = "Error updating order item.";
        }
        $updateStmt->close();
    } else {
        $_SESSION['error'] = "Product not found.";
    }

    $stmt->close();
    header("Location: edit_order_items.php?order_id=" . $_POST['order_id']);
    exit();
}

// Fetch order items for display
$orderId = $_GET['order_id'];
$orderItemsQuery = "SELECT oi.order_item_id, oi.product_id, p.product_name, oi.quantity, oi.unit_price, oi.line_total 
    FROM order_items oi 
    JOIN products p ON oi.product_id = p.product_id 
    WHERE oi.order_id = ?";
$stmt = $conn->prepare($orderItemsQuery);
if (!$stmt) {
    die("Database error: " . $conn->error);
}
$stmt->bind_param("i", $orderId);
$stmt->execute();
$orderItemsResult = $stmt->get_result();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Order Items</title>
    <link rel="stylesheet" href="/overstock-daily-deals/css/style.css">
</head>
<body>
    <?php include_once __DIR__ . '/../header.php'; ?>

    <div class="content">
        <h1>Edit Order Items</h1>
        <?php if (isset($_SESSION['message'])): ?>
            <p class="success"><?php echo $_SESSION['message']; unset($_SESSION['message']); ?></p>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <p class="error"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></p>
        <?php endif; ?>

        <table>
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Quantity</th>
                    <th>Unit Price</th>
                    <th>Line Total</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($orderItem = $orderItemsResult->fetch_assoc()): ?>
                    <tr>
                        <form action="edit_order_items.php" method="POST">
                            <td><?php echo htmlspecialchars($orderItem['product_name']); ?></td>
                            <td>
                                <input type="number" name="quantity" value="<?php echo htmlspecialchars($orderItem['quantity']); ?>" min="1" required>
                            </td>
                            <td>$<?php echo number_format($orderItem['unit_price'], 2); ?></td>
                            <td>$<?php echo number_format($orderItem['line_total'], 2); ?></td>
                            <td>
                                <input type="hidden" name="order_item_id" value="<?php echo $orderItem['order_item_id']; ?>">
                                <input type="hidden" name="product_id" value="<?php echo $orderItem['product_id']; ?>">
                                <input type="hidden" name="order_id" value="<?php echo $orderId; ?>">
                                <button type="submit">Update</button>
                            </td>
                        </form>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
