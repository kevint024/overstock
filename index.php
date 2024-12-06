<?php
session_start();
include __DIR__ . '/admin/db_connection.php';

// Retrieve the deal of the day
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
            $discount_percent = (($deal_of_day['original_price'] - $deal_of_day['discount_price']) / $deal_of_day['original_price']) * 100;
        ?>
            <div class="deal-of-day">
                <h2><?php echo htmlspecialchars($deal_of_day['product_name']); ?></h2>
                <img src="<?php echo htmlspecialchars($deal_of_day['main_image']); ?>" alt="<?php echo htmlspecialchars($deal_of_day['product_name']); ?>" class="product-image">
                <p>Original Price: $<?php echo htmlspecialchars($deal_of_day['original_price']); ?></p>
                <p>Discount Price: $<?php echo htmlspecialchars($deal_of_day['discount_price']); ?></p>
                <p>Discount: <?php echo round($discount_percent, 2); ?>% off</p>
                <p><?php echo htmlspecialchars($deal_of_day['description']); ?></p>

                <!-- Countdown Timer for Deal of the Day -->
                <?php if (!empty($deal_of_day['deal_end_date'])): ?>
                    <p>Deal ends on: <?php echo htmlspecialchars($deal_of_day['deal_end_date']); ?></p>
                    <p id="countdown_deal_of_day"></p>
                    <script>
                        // JavaScript Countdown Timer for Deal of the Day
                        const dealEndDate = new Date('<?php echo htmlspecialchars($deal_of_day['deal_end_date']); ?>').getTime();

                        function updateDealCountdown() {
                            const now = new Date().getTime();
                            const timeRemaining = dealEndDate - now;

                            if (timeRemaining > 0) {
                                const days = Math.floor(timeRemaining / (1000 * 60 * 60 * 24));
                                const hours = Math.floor((timeRemaining % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                                const minutes = Math.floor((timeRemaining % (1000 * 60 * 60)) / (1000 * 60));
                                const seconds = Math.floor((timeRemaining % (1000 * 60)) / 1000);

                                document.getElementById("countdown_deal_of_day").innerHTML = 
                                    `${days}d ${hours}h ${minutes}m ${seconds}s remaining`;
                            } else {
                                document.getElementById("countdown_deal_of_day").innerHTML = "Deal has ended.";
                            }
                        }

                        setInterval(updateDealCountdown, 1000);
                    </script>
                <?php endif; ?>

                <a href="product.php?product_id=<?php echo $deal_of_day['product_id']; ?>" class="view-deal">View Deal</a>
            </div>
        <?php } else { ?>
            <p>No deal of the day selected at this time.</p>
        <?php } ?>

        <h1>All Deals</h1>
        <div class="products-list">
            <?php while ($product = $result_all_deals->fetch_assoc()) { 
                $discount_percent = (($product['original_price'] - $product['discount_price']) / $product['original_price']) * 100;
            ?>
                <div class="product-item">
                    <img src="<?php echo htmlspecialchars($product['main_image']); ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>" class="product-image">
                    <h3><?php echo htmlspecialchars($product['product_name']); ?></h3>
                    <p>Original Price: $<?php echo htmlspecialchars($product['original_price']); ?></p>
                    <p>Discount Price: $<?php echo htmlspecialchars($product['discount_price']); ?></p>
                    <p>Discount: <?php echo round($discount_percent, 2); ?>% off</p>

                    <!-- Countdown Timer for All Deals -->
                    <?php if (!empty($product['deal_end_date'])): ?>
                        <p>Deal ends on: <?php echo htmlspecialchars($product['deal_end_date']); ?></p>
                        <p id="countdown_<?php echo htmlspecialchars($product['product_id']); ?>"></p>
                        <script>
                            (function() {
                                const countdownId = 'countdown_<?php echo htmlspecialchars($product['product_id']); ?>';
                                const dealEndDate = new Date('<?php echo htmlspecialchars($product['deal_end_date']); ?>').getTime();
                                
                                function updateCountdown() {
                                    const now = new Date().getTime();
                                    const timeRemaining = dealEndDate - now;

                                    if (timeRemaining > 0) {
                                        const days = Math.floor(timeRemaining / (1000 * 60 * 60 * 24));
                                        const hours = Math.floor((timeRemaining % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                                        const minutes = Math.floor((timeRemaining % (1000 * 60 * 60)) / (1000 * 60));
                                        const seconds = Math.floor((timeRemaining % (1000 * 60)) / 1000);

                                        document.getElementById(countdownId).innerHTML = 
                                            `${days}d ${hours}h ${minutes}m ${seconds}s remaining`;
                                    } else {
                                        document.getElementById(countdownId).innerHTML = "Deal has ended.";
                                    }
                                }

                                setInterval(updateCountdown, 1000);
                            })();
                        </script>
                    <?php endif; ?>

                    <a href="product.php?product_id=<?php echo $product['product_id']; ?>" class="view-product">View Product</a>
                </div>
            <?php } ?>
        </div>
    </div>
</body>

</html>

<?php
$conn->close();
?>
