<?php
include('db_connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $orderId = $_POST['order_id'];

    // Handle Deleting Items
    if (isset($_POST['delete_items'])) {
        foreach ($_POST['delete_items'] as $orderItemId) {
            $sql_delete = "DELETE FROM order_items WHERE order_item_id = ?";
            $stmt_delete = $conn->prepare($sql_delete);
            $stmt_delete->bind_param("i", $orderItemId);
            $stmt_delete->execute();
            $stmt_delete->close();
        }
    }

    // Handle Updating Quantities
    if (isset($_POST['quantity'])) {
        foreach ($_POST['quantity'] as $orderItemId => $quantity) {
            $sql_update = "UPDATE order_items SET quantity = ? WHERE order_item_id = ?";
            $stmt_update = $conn->prepare($sql_update);
            $stmt_update->bind_param("ii", $quantity, $orderItemId);
            $stmt_update->execute();
            $stmt_update->close();
        }
    }

    // Handle Adding a New Product to the Order
    if (isset($_POST['new_product_id']) && isset($_POST['new_quantity'])) {
        $newProductId = $_POST['new_product_id'];
        $newQuantity = $_POST['new_quantity'];

        if ($newQuantity > 0) {
            $sql_product = "SELECT discount_price, stock_quantity FROM products WHERE product_id = ?";
            $stmt_product = $conn->prepare($sql_product);
            $stmt_product->bind_param("i", $newProductId);
            $stmt_product->execute();
            $result_product = $stmt_product->get_result();
            $product = $result_product->fetch_assoc();
            $stmt_product->close();

            if ($product && $product['stock_quantity'] >= $newQuantity) {
                $unitPrice = $product['discount_price'];

                // Insert new order item
                $sql_insert = "INSERT INTO order_items (order_id, product_id, quantity, unit_price) VALUES (?, ?, ?, ?)";
                $stmt_insert = $conn->prepare($sql_insert);
                $stmt_insert->bind_param("iiid", $orderId, $newProductId, $newQuantity, $unitPrice);
                $stmt_insert->execute();
                $stmt_insert->close();

                // Update stock quantity in products table
                $newStockQuantity = $product['stock_quantity'] - $newQuantity;
                $sql_update_stock = "UPDATE products SET stock_quantity = ? WHERE product_id = ?";
                $stmt_stock = $conn->prepare($sql_update_stock);
                $stmt_stock->bind_param("ii", $newStockQuantity, $newProductId);
                $stmt_stock->execute();
                $stmt_stock->close();
            }
        }
    }

    // Recalculate the total amount for the order
    $sql_total = "SELECT SUM(quantity * unit_price) AS total_amount FROM order_items WHERE order_id = ?";
    $stmt_total = $conn->prepare($sql_total);
    $stmt_total->bind_param("i", $orderId);
    $stmt_total->execute();
    $result_total = $stmt_total->get_result();
    $total = $result_total->fetch_assoc();
    $stmt_total->close();

    $newTotalAmount = $total['total_amount'] ?? 0;

    // Update the total amount in the orders table
    $sql_update_order = "UPDATE orders SET total_amount = ? WHERE order_id = ?";
    $stmt_update_order = $conn->prepare($sql_update_order);
    $stmt_update_order->bind_param("di", $newTotalAmount, $orderId);
    $stmt_update_order->execute();
    $stmt_update_order->close();

    header("Location: edit_order_items.php?order_id=$orderId&status=updated");
    exit();
}

$conn->close();
?>
