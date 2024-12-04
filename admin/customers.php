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
    <title>Customer Management</title>
    <link rel="stylesheet" href="../css/style.css"> 
</head>
<body>

<header class="site-header">
        <h1>Customer Mangement - Overstock Stock</h1>
        <nav>
            <a href="../index.html">Home</a>
            <a href="../contact.html">Contact</a>
            <a href="inventory.php">Inventory Management</a>
            <a href="customers.php">Customer Mangement</a>
        </nav>
    </header>

    <h1>Customer Management</h1>

    <!-- Add Customer Form -->
    <h2>Add Customer</h2>
    <form action="add_customer.php" method="POST">
        <label for="first_name">First Name:</label>
        <input type="text" id="first_name" name="first_name" required><br><br>

        <label for="last_name">Last Name:</label>
        <input type="text" id="last_name" name="last_name" required><br><br>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required><br><br>

        <label for="phone_number">Phone Number:</label>
        <input type="text" id="phone_number" name="phone_number"><br><br>

        <label for="address">Address:</label>
        <input type="text" id="address" name="address"><br><br>

        <label for="city">City:</label>
        <input type="text" id="city" name="city"><br><br>

        <label for="state">State:</label>
        <input type="text" id="state" name="state"><br><br>

        <label for="zip_code">Zip Code:</label>
        <input type="text" id="zip_code" name="zip_code"><br><br>

        <button type="submit">Add Customer</button>
    </form>

    <!-- Link back to inventory management page -->
    <br><a href="inventory.php">Back to Inventory Management</a><br>

</body>

<?php
include('db_connection.php');

// Fetch all customers from the database
$sql = "SELECT * FROM customers";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    echo "<h2>Current Customers</h2>";
    echo "<table border='1'>";
    echo "<tr><th>First Name</th><th>Last Name</th><th>Email</th><th>Phone Number</th><th>Address</th><th>City</th><th>State</th><th>Zip Code</th><th>Actions</th></tr>";
    while($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['first_name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['last_name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['email']) . "</td>";
        echo "<td>" . htmlspecialchars($row['phone_number']) . "</td>";
        echo "<td>" . htmlspecialchars($row['address']) . "</td>";
        echo "<td>" . htmlspecialchars($row['city']) . "</td>";
        echo "<td>" . htmlspecialchars($row['state']) . "</td>";
        echo "<td>" . htmlspecialchars($row['zip_code']) . "</td>";
        // Add actions for Edit and Delete
        echo "<td><a href='update_customer.php?id=" . $row['customer_id'] . "'>Edit</a> | ";
        echo "<a href='delete_customer.php?id=" . $row['customer_id'] . "' onclick='return confirm(\"Are you sure you want to delete this customer?\");'>Delete</a></td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No customers found.</p>";
}

$conn->close();
?>


</html>

