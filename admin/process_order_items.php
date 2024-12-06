<?php
include 'db_connection.php';
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Ensure only admins can access this functionality
if ($_SESSION['role'] !== 'admin') {
    echo "Access denied. You do not have permission to perform this action.";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $order_id = $_POST['order_id'];
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];

    // Step 1: Retrieve the unit price from the products table
    $sql_get_price = "SELECT discount_price FROM products WHERE product_id = ?";
    if ($stmt_get_price = $conn->prepare($sql_get_price)) {
        $stmt_get_price->bind_param("i", $product_id);
        $stmt_get_price->execute();
        $stmt_get_price->bind_result($unit_price);
        if ($stmt_get_price->fetch()) {
            // Step 2: Calculate line total
            $line_total = $unit_price * $quantity;

            // Step 3: Insert the order item into the order_items table
            $sql_insert_order_item = "INSERT INTO order_items (order_id, product_id, quantity, unit_price, line_total) VALUES (?, ?, ?, ?, ?)";
            if ($stmt_insert = $conn->prepare($sql_insert_order_item)) {
                $stmt_insert->bind_param("iiidd", $order_id, $product_id, $quantity, $unit_price, $line_total);
                if ($stmt_insert->execute()) {
                    echo "Order item added successfully.";
                } else {
                    echo "Error adding order item: " . $stmt_insert->error;
                }
                $stmt_insert->close();
            } else {
                echo "Error preparing insert statement: " . $conn->error;
            }
        } else {
            echo "Error retrieving product price.";
        }
        $stmt_get_price->close();
    } else {
        echo "Error preparing price retrieval statement: " . $conn->error;
    }

    // Close the database connection
    $conn->close();
    // Redirect back to the order details page
    header("Location: orders.php");
    exit();
}
?>
