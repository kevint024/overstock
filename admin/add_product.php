<?php
include('db_connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $productName = $_POST['product_name'];
    $description = $_POST['description'];
    $category = $_POST['category'];
    $originalPrice = $_POST['original_price'];
    $discountPrice = $_POST['discount_price'];
    $stockQuantity = $_POST['stock_quantity'];
    $dealStartDate = !empty($_POST['deal_start_date']) ? $_POST['deal_start_date'] : NULL; // Can be NULL if not provided
    $dealEndDate = !empty($_POST['deal_end_date']) ? $_POST['deal_end_date'] : NULL; // Can be NULL if not provided
    $isActive = isset($_POST['is_active']) ? 1 : 0; // Checkbox returns true if checked

    // Insert into the database
    $sql = "INSERT INTO products (product_name, description, category, original_price, discount_price, stock_quantity, deal_start_date, deal_end_date, is_active)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssddissi", $productName, $description, $category, $originalPrice, $discountPrice, $stockQuantity, $dealStartDate, $dealEndDate, $isActive);

    if ($stmt->execute()) {
        header("Location: inventory.php?status=success");
        exit();
    } else {
        echo "Error adding product: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
