<?php
include('db_connection.php'); 

// Retrieve form data
$productName = $_POST['product_name'];
$category = $_POST['category'];
$originalPrice = $_POST['original_price'];
$discountPrice = $_POST['discount_price'];
$stockQuantity = $_POST['stock_quantity'];

// No wanty injection. Injection bad.
$stmt = $conn->prepare("INSERT INTO products (product_name, category, original_price, discount_price, stock_quantity) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("ssdii", $productName, $category, $originalPrice, $discountPrice, $stockQuantity);

if ($stmt->execute() === TRUE) {
    echo "New product added successfully";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
