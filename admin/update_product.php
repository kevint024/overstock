<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Ensure only admins have access to the admin pages
if ($_SESSION['role'] !== 'admin') {
    echo "Access denied. You do not have permission to access this page.";
    exit();
}
?>



<?php
include('db_connection.php');

if (isset($_GET['id'])) {
    $product_id = $_GET['id'];

    // Fetch the product details
    $sql = "SELECT * FROM products WHERE product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $product = $result->fetch_assoc();
    } else {
        echo "Product not found.";
        exit();
    }
    $stmt->close();
} else {
    echo "No product ID provided.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle the update form submission
    $productName = $_POST['product_name'];
    $description = $_POST['description'];
    $category = $_POST['category'];
    $originalPrice = $_POST['original_price'];
    $discountPrice = $_POST['discount_price'];
    $stockQuantity = $_POST['stock_quantity'];
    $dealStartDate = !empty($_POST['deal_start_date']) ? $_POST['deal_start_date'] : NULL;
    $dealEndDate = !empty($_POST['deal_end_date']) ? $_POST['deal_end_date'] : NULL;
    $isActive = isset($_POST['is_active']) ? 1 : 0;

    // Update the product in the database
    $sql = "UPDATE products SET product_name = ?, description = ?, category = ?, original_price = ?, discount_price = ?, stock_quantity = ?, deal_start_date = ?, deal_end_date = ?, is_active = ? WHERE product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssddissii", $productName, $description, $category, $originalPrice, $discountPrice, $stockQuantity, $dealStartDate, $dealEndDate, $isActive, $product_id);

    if ($stmt->execute()) {
        header("Location: inventory.php?status=updated");
        exit();
    } else {
        echo "Error updating product: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Product</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <h1>Update Product</h1>
    <form action="" method="POST">
        <label for="product_name">Product Name:</label>
        <input type="text" id="product_name" name="product_name" value="<?php echo htmlspecialchars($product['product_name']); ?>" required><br><br>

        <label for="description">Description:</label>
        <textarea id="description" name="description" required><?php echo htmlspecialchars($product['description']); ?></textarea><br><br>

        <label for="category">Category:</label>
        <input type="text" id="category" name="category" value="<?php echo htmlspecialchars($product['category']); ?>" required><br><br>

        <label for="original_price">Original Price:</label>
        <input type="number" id="original_price" name="original_price" value="<?php echo htmlspecialchars($product['original_price']); ?>" required step="0.01"><br><br>

        <label for="discount_price">Discount Price:</label>
        <input type="number" id="discount_price" name="discount_price" value="<?php echo htmlspecialchars($product['discount_price']); ?>" required step="0.01"><br><br>

        <label for="stock_quantity">Stock Quantity:</label>
        <input type="number" id="stock_quantity" name="stock_quantity" value="<?php echo htmlspecialchars($product['stock_quantity']); ?>" required><br><br>

        <label for="deal_start_date">Deal Start Date:</label>
        <input type="date" id="deal_start_date" name="deal_start_date" value="<?php echo htmlspecialchars($product['deal_start_date']); ?>"><br><br>

        <label for="deal_end_date">Deal End Date:</label>
        <input type="date" id="deal_end_date" name="deal_end_date" value="<?php echo htmlspecialchars($product['deal_end_date']); ?>"><br><br>

        <label for="is_active">Is Active:</label>
        <input type="checkbox" id="is_active" name="is_active" <?php echo ($product['is_active'] == 1) ? 'checked' : ''; ?>><br><br>

        <button type="submit">Update Product</button>
    </form>
</body>
</html>
