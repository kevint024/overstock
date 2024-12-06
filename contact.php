<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us</title>
    <link rel="stylesheet" href="/overstock-daily-deals/css/style.css">
</head>
<body>
    <?php include_once __DIR__ . '/header.php'; ?>

    <div class="content">
        <h1>Contact Us</h1>

        <p>If you have any questions, concerns, or feedback, please feel free to reach out to us using the form below.</p>

        <form action="contact_submit.php" method="POST" class="contact-form">
            <label for="name">Your Name:</label>
            <input type="text" id="name" name="name" required>

            <label for="email">Your Email:</label>
            <input type="email" id="email" name="email" required>

            <label for="message">Your Message:</label>
            <textarea id="message" name="message" rows="5" required></textarea>

            <button type="submit">Submit</button>
        </form>
    </div>

    <footer>
        <p>&copy; <?php echo date('Y'); ?> Overstock Daily Deals. All rights reserved.</p>
    </footer>
</body>
</html>
