<?php
session_start();
include __DIR__ . '/admin/db_connection.php';

// Retrieve the deal of the day (assuming there's a flag or selection in your products table)
$sql_deal_of_day = "SELECT * FROM products WHERE is_deal_of_day = 1 LIMIT 1";
$result_deal_of_day = $conn->query($sql_deal_of_day);

// Retrieve all active deals from the products table
$sql_all_deals = "SELECT * FROM products WHERE is_active = 1";
$result_all_deals = $conn->query($sql_all_deals);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Overstock Daily Deals</title>
    <link rel="stylesheet" href="/overstock-daily-deals/css/style.css">
</head>
<body>
    <?php include __DIR__ . '/header.php'; ?>

    <div class="content">
        <h1>Deal of the Day</h1>
        <?php if ($result_deal_of_day && $result_deal_of_day->num_rows > 0) { 
            $deal_of_day = $result_deal_of_day->fetch_assoc();
        ?>
            <div class="deal-of-day">
                <h2><?php echo htmlspecialchars($deal_of_day['product_name']); ?></h2>
                <img src="<?php echo htmlspecialchars($deal_of_day['main_image']); ?>" alt="<?php echo htmlspecialchars($deal_of_day['product_name']); ?>" class="product-image">
                <p>Original Price: $<?php echo htmlspecialchars($deal_of_day['original_price']); ?></p>
                <p>Discount Price: $<?php echo htmlspecialchars($deal_of_day['discount_price']); ?></p>
                <p><?php echo htmlspecialchars($deal_of_day['description']); ?></p>
                <a href="product.php?product_id=<?php echo $deal_of_day['product_id']; ?>">View Deal</a>
            </div>
        <?php } else { ?>
            <p>No deal of the day selected at this time.</p>
        <?php } ?>

        <h1>All Deals</h1>
        <div class="products-list">
            <?php while ($product = $result_all_deals->fetch_assoc()) { ?>
                <div class="product-item">
                    <img src="<?php echo htmlspecialchars($product['main_image']); ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>" class="product-image">
                    <h3><?php echo htmlspecialchars($product['product_name']); ?></h3>
                    <p>Discount Price: $<?php echo htmlspecialchars($product['discount_price']); ?></p>
                    <a href="product.php?product_id=<?php echo $product['product_id']; ?>">View Product</a>
                </div>
            <?php } ?>
        </div>
    </div>
</body>
</html>

<?php
$conn->close();
?>
