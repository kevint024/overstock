<?php
include('db_connection.php');

if (isset($_GET['id'])) {
    $orderId = $_GET['id'];

    // Delete order
    $sql = "DELETE FROM orders WHERE order_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $orderId);

    if ($stmt->execute()) {
        header("Location: orders.php?status=deleted");
        exit();
    } else {
        echo "Error deleting order: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
