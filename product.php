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
    // Calculate discount percentage
    $discount_percent = (($product['original_price'] - $product['discount_price']) / $product['original_price']) * 100;
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

        <!-- Product Information -->
        <p>Original Price: $<?php echo htmlspecialchars($product['original_price']); ?></p>
        <p>Discount Price: $<?php echo htmlspecialchars($product['discount_price']); ?></p>
        <p>Discount: <?php echo round($discount_percent, 2); ?>% off</p>
        <p><?php echo htmlspecialchars($product['description']); ?></p>

        <!-- Countdown Timer -->
        <?php if (!empty($product['deal_end_date'])): ?>
            <p>Deal ends on: <?php echo htmlspecialchars($product['deal_end_date']); ?></p>
            <p id="countdown"></p>
            <script>
                // JavaScript Countdown Timer
                const dealEndDate = new Date('<?php echo htmlspecialchars($product['deal_end_date']); ?>').getTime();
                
                function updateCountdown() {
                    const now = new Date().getTime();
                    const timeRemaining = dealEndDate - now;

                    if (timeRemaining > 0) {
                        const days = Math.floor(timeRemaining / (1000 * 60 * 60 * 24));
                        const hours = Math.floor((timeRemaining % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                        const minutes = Math.floor((timeRemaining % (1000 * 60 * 60)) / (1000 * 60));
                        const seconds = Math.floor((timeRemaining % (1000 * 60)) / 1000);

                        document.getElementById("countdown").innerHTML = 
                            `${days}d ${hours}h ${minutes}m ${seconds}s remaining`;
                    } else {
                        document.getElementById("countdown").innerHTML = "Deal has ended.";
                    }
                }

                setInterval(updateCountdown, 1000);
            </script>
        <?php endif; ?>

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
