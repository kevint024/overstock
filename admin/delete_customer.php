<?php
include('db_connection.php');

if (isset($_GET['id'])) {
    $customer_id = $_GET['id'];

    // Delete customer
    $sql = "DELETE FROM customers WHERE customer_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $customer_id);

    if ($stmt->execute()) {
        header("Location: customers.php?status=deleted");
        exit();
    } else {
        echo "Error deleting customer: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
