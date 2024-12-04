<?php
include('db_connection.php');

if (isset($_GET['id'])) {
    $product_id = $_GET['id'];

    // Prepare the delete query
    $sql = "DELETE FROM products WHERE product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $product_id);

    if ($stmt->execute()) {
        // Redirect back to the inventory page with a success message
        header("Location: inventory.php?status=deleted");
        exit();
    } else {
        echo "Error deleting product: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "No product ID provided.";
}

$conn->close();
?>
