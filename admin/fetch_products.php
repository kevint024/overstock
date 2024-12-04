<?php
include('db_connection.php');

// Fetch all products from the database
$sql = "SELECT * FROM products";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    echo "<table border='1'>";
    echo "<tr><th>Product Name</th><th>Category</th><th>Original Price</th><th>Discount Price</th><th>Stock Quantity</th><th>Actions</th></tr>";
    while($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['product_name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['category']) . "</td>";
        echo "<td>" . htmlspecialchars($row['original_price']) . "</td>";
        echo "<td>" . htmlspecialchars($row['discount_price']) . "</td>";
        echo "<td>" . htmlspecialchars($row['stock_quantity']) . "</td>";
        // Edit Button Right Here
        echo "<td><a href='update_product.php?id=" . $row['product_id'] . "'>Edit</a></td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "No products found.";
}

$conn->close();
?>
