<?php
session_start();
include __DIR__ . '/admin/db_connection.php';

// Get the product ID from the URL
if (!isset($_GET['product_id'])) {
    echo "Product not found.";
    exit();
}

$product_id = $_GET['product_id'];

// Retrieve product details from the database
$sql_product = "SELECT * FROM products WHERE product_id = ?";
$stmt_product = $conn->prepare($sql_product);
$stmt_product->bind_param("i", $product_id);
$stmt_product->execute();
$result_product = $stmt_product->get_result();

if ($result_product && $result_product->num_rows > 0) {
    $product = $result_product->fetch_assoc();
} else {
    echo "Product not found.";
    exit();
}

// Retrieve additional images from the product_images table
$sql_images = "SELECT image_path FROM product_images WHERE product_id = ?";
$stmt_images = $conn->prepare($sql_images);
$stmt_images->bind_param("i", $product_id);
$stmt_images->execute();
$result_images = $stmt_images->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['product_name']); ?></title>
    <link rel="stylesheet" href="/overstock-daily-deals/css/style.css">
</head>
<body>
    <?php include __DIR__ . '/header.php'; ?>

    <div class="content">
        <h1><?php echo htmlspecialchars($product['product_name']); ?></h1>

        <!-- Main Product Image -->
        <div class="main-image">
            <img src="<?php echo htmlspecialchars($product['main_image']); ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>" class="product-main-image">
        </div>

        <!-- Additional Product Images -->
        <?php if ($result_images && $result_images->num_rows > 0) { ?>
            <div class="additional-images">
                <h3>Additional Images</h3>
                <?php while ($image = $result_images->fetch_assoc()) { ?>
                    <img src="<?php echo htmlspecialchars($image['image_path']); ?>" alt="Additional Image" class="product-additional-image">
                <?php } ?>
            </div>
        <?php } ?>

        <p>Original Price: $<?php echo htmlspecialchars($product['original_price']); ?></p>
        <p>Discount Price: $<?php echo htmlspecialchars($product['discount_price']); ?></p>
        <p><?php echo htmlspecialchars($product['description']); ?></p>

        <!-- Purchase Section -->
        <?php if (isset($_SESSION['user_id'])) { ?>
            <form action="purchase.php" method="POST">
                <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product['product_id']); ?>">
                <label for="quantity">Quantity:</label>
                <input type="number" id="quantity" name="quantity" value="1" min="1" max="<?php echo htmlspecialchars($product['stock_quantity']); ?>" required>
                <button type="submit" class="button-primary">Purchase</button>
            </form>
        <?php } else { ?>
            <p><a href="login.php">Log in</a> to purchase this product.</p>
        <?php } ?>
    </div>
</body>
</html>

<?php
$stmt_product->close();
$stmt_images->close();
$conn->close();
?>
