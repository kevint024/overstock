<?php
include('db_connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $orderId = $_POST['order_id'];
    $productId = $_POST['product_id'];
    $quantity = $_POST['quantity'];

    // Fetch product details to get price
    $sql_product = "SELECT product_id, discount_price, stock_quantity FROM products WHERE product_id = ?";
    $stmt = $conn->prepare($sql_product);
    $stmt->bind_param("i", $productId);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();

    if (!$product) {
        echo "Product not found.";
        exit();
    }

    // Check if enough stock is available
    if ($product['stock_quantity'] < $quantity) {
        echo "Not enough stock available for this product.";
        exit();
    }

    // Calculate the line total
    $unitPrice = $product['discount_price'];
    $lineTotal = $unitPrice * $quantity;

    // Insert into order_items table
    $sql = "INSERT INTO order_items (order_id, product_id, quantity, unit_price) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiid", $orderId, $productId, $quantity, $unitPrice);

    if ($stmt->execute()) {
        // Update total_amount in orders table
        $sql_update_total = "UPDATE orders SET total_amount = total_amount + ? WHERE order_id = ?";
        $stmt_update = $conn->prepare($sql_update_total);
        $stmt_update->bind_param("di", $lineTotal, $orderId);
        $stmt_update->execute();

        // Update stock_quantity in products table
        $newStockQuantity = $product['stock_quantity'] - $quantity;
        $sql_update_stock = "UPDATE products SET stock_quantity = ? WHERE product_id = ?";
        $stmt_stock = $conn->prepare($sql_update_stock);
        $stmt_stock->bind_param("ii", $newStockQuantity, $productId);
        $stmt_stock->execute();

        header("Location: add_order_items.php?order_id=$orderId&status=success");
        exit();
    } else {
        echo "Error adding product to order: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
