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
    $orderId = $_GET['id'];

    // Fetch order details
    $sql = "SELECT * FROM orders WHERE order_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $orderId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $order = $result->fetch_assoc();
    } else {
        echo "Order not found.";
        exit();
    }
    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle form submission for updating
    $status = $_POST['status'];
    $orderNotes = $_POST['order_notes'];

    // Update the order in the database
    $sql = "UPDATE orders SET status = ?, order_notes = ? WHERE order_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $status, $orderNotes, $orderId);

    if ($stmt->execute()) {
        header("Location: orders.php?status=updated");
        exit();
    } else {
        echo "Error updating order: " . $stmt->error;
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
    <title>Update Order</title>
</head>
<body>
    <h1>Update Order</h1>
    <form action="" method="POST">
        <label for="status">Order Status:</label>
        <select id="status" name="status" required>
            <option value="Pending" <?php if ($order['status'] == 'Pending') echo 'selected'; ?>>Pending</option>
            <option value="Shipped" <?php if ($order['status'] == 'Shipped') echo 'selected'; ?>>Shipped</option>
            <option value="Completed" <?php if ($order['status'] == 'Completed') echo 'selected'; ?>>Completed</option>
            <option value="Cancelled" <?php if ($order['status'] == 'Cancelled') echo 'selected'; ?>>Cancelled</option>
        </select><br><br>

        <label for="order_notes">Order Notes:</label>
        <textarea id="order_notes" name="order_notes"><?php echo htmlspecialchars($order['order_notes']); ?></textarea><br><br>

        <button type="submit">Update Order</button>
    </form>
</body>
</html>
