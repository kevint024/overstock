<?php
include('db_connection.php');

// Check if the request is a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $productName = $_POST['product_name'] ?? null;
    $category = $_POST['category'] ?? null;
    $originalPrice = $_POST['original_price'] ?? null;
    $discountPrice = $_POST['discount_price'] ?? null;
    $stockQuantity = $_POST['stock_quantity'] ?? null;

    // Check if all required data is available
    if ($productName && $category && $originalPrice !== null && $discountPrice !== null && $stockQuantity !== null) {
        // Prepare and execute the SQL query
        $stmt = $conn->prepare("INSERT INTO products (product_name, category, original_price, discount_price, stock_quantity) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssdii", $productName, $category, $originalPrice, $discountPrice, $stockQuantity);
        $stmt->execute();
        $stmt->close();
    }

    $conn->close();

    // Redirect back to inventory.html with a success indicator
    header("Location: inventory.php?status=success");
    exit();
} else {
    // Redirect back to inventory.html if accessed directly
    header("Location: inventory.php");
    exit();
}
?>
