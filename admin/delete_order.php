<?php
include('db_connection.php');

if (isset($_GET['id'])) {
    $orderId = $_GET['id'];

    // First, delete all order items linked to this order
    $sql_delete_items = "DELETE FROM order_items WHERE order_id = ?";
    $stmt_items = $conn->prepare($sql_delete_items);
    $stmt_items->bind_param("i", $orderId);
    
    if ($stmt_items->execute()) {
        // Now delete the order itself
        $sql_delete_order = "DELETE FROM orders WHERE order_id = ?";
        $stmt_order = $conn->prepare($sql_delete_order);
        $stmt_order->bind_param("i", $orderId);

        if ($stmt_order->execute()) {
            // Redirect back to orders page with a success status
            header("Location: orders.php?status=deleted");
            exit();
        } else {
            echo "Error deleting order: " . $stmt_order->error;
        }
        
        $stmt_order->close();
    } else {
        echo "Error deleting order items: " . $stmt_items->error;
    }

    $stmt_items->close();
} else {
    echo "No order ID provided.";
}

$conn->close();
?>
