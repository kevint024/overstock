<?php
include('db_connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customerId = $_POST['customer_id'];
    $orderNotes = !empty($_POST['order_notes']) ? $_POST['order_notes'] : NULL;
    $orderDate = date("Y-m-d H:i:s");
    $status = "Pending"; // Default order status
    $totalAmount = 0.00; // This will be calculated later based on products

    // Insert new order into the database
    $sql = "INSERT INTO orders (customer_id, order_date, total_amount, status, order_notes) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isdss", $customerId, $orderDate, $totalAmount, $status, $orderNotes);

    if ($stmt->execute()) {
        header("Location: orders.php?status=success");
        exit();
    } else {
        echo "Error adding order: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
