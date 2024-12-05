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




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Management - Overstock Stock</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<?php include __DIR__ . '/../header.php'; ?>

    
<form action="add_product.php" method="POST" enctype="multipart/form-data">
        <label for="product_name">Product Name:</label>
        <input type="text" id="product_name" name="product_name" required><br>

        <label for="description">Description:</label>
        <textarea id="description" name="description" required></textarea><br><br>

        <label for="category">Category:</label>
        <input type="text" id="category" name="category" required><br><br>

        <label for="original_price">Original Price:</label>
        <input type="number" id="original_price" name="original_price" required step="0.01"><br><br>

        <label for="discount_price">Discount Price:</label>
        <input type="number" id="discount_price" name="discount_price" required step="0.01"><br><br>

        <label for="stock_quantity">Stock Quantity:</label>
        <input type="number" id="stock_quantity" name="stock_quantity" required><br><br>

        <label for="deal_start_date">Deal Start Date:</label>
        <input type="date" id="deal_start_date" name="deal_start_date"><br><br>

        <label for="deal_end_date">Deal End Date:</label>
        <input type="date" id="deal_end_date" name="deal_end_date"><br><br>

        <label for="is_active">Is Active:</label>
        <input type="checkbox" id="is_active" name="is_active" checked><br><br>

        <label for="main_image">Upload Main Image:</label>
        <input type="file" id="main_image" name="main_image" accept="image/*" required><br>

        <label for="additional_images">Upload Additional Images:</label>
        <input type="file" id="additional_images" name="additional_images[]" accept="image/*" multiple><br> <!-- Allow multiple images -->
        
        <button type="submit">Add Product</button>
    </form>

    <h2>Current Products</h2>
    <div id="product-list">
        <?php include('fetch_products.php'); ?>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (window.location.search.includes('status=success')) {
                const successMessage = document.createElement('div');
                successMessage.textContent = "Product added successfully!";
                successMessage.style.color = "green";
                document.body.insertBefore(successMessage, document.body.firstChild);
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('status') === 'success') {
                const successMessage = document.createElement('div');
                successMessage.textContent = "Product added successfully!";
                successMessage.className = "success-message";
                document.body.insertBefore(successMessage, document.body.firstChild);
            } else if (urlParams.get('status') === 'updated') {
                const updatedMessage = document.createElement('div');
                updatedMessage.textContent = "Product updated successfully!";
                updatedMessage.className = "success-message";
                document.body.insertBefore(updatedMessage, document.body.firstChild);
            } else if (urlParams.get('status') === 'deleted') {
                const deletedMessage = document.createElement('div');
                deletedMessage.textContent = "Product deleted successfully!";
                deletedMessage.className = "success-message";
                document.body.insertBefore(deletedMessage, document.body.firstChild);
    }
    
    // Remove 'status' from URL without reloading
    window.history.replaceState({}, document.title, window.location.pathname);
});

    </script>
</body>
</html>
