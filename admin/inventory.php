<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Management - Overstock Stock</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<header>
        <h1>Inventory Management</h1>
        <nav>
            <a href="../index.html">Home</a>
            <a href="../contact.html">Contact</a>
            <a href="inventory.html">Inventory Management</a>
        </nav>
    </header>
    
    <form id="add-product-form" action="add_product.php" method="POST">
        <label for="product_name">Product Name:</label>
        <input type="text" id="product_name" name="product_name" required><br><br>

        <label for="category">Category:</label>
        <input type="text" id="category" name="category" required><br><br>

        <label for="original_price">Original Price:</label>
        <input type="number" id="original_price" name="original_price" required step="0.01"><br><br>

        <label for="discount_price">Discount Price:</label>
        <input type="number" id="discount_price" name="discount_price" required step="0.01"><br><br>

        <label for="stock_quantity">Stock Quantity:</label>
        <input type="number" id="stock_quantity" name="stock_quantity" required><br><br>

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
    </script>
</body>
</html>
