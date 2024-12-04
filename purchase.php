<?php
session_start();
include __DIR__ . '/admin/db_connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['product_id'];
    $user_id = $_SESSION['user_id'];

    // Retrieve customer ID and address information from the customers table linked to this user
    $sql_customer = "SELECT customer_id, first_name, last_name, address, city, state, zip_code, phone_number FROM customers WHERE user_id = ?";
    $stmt_customer = $conn->prepare($sql_customer);
    $stmt_customer->bind_param("i", $user_id);
    $stmt_customer->execute();
    $result_customer = $stmt_customer->get_result();

    if ($result_customer && $result_customer->num_rows > 0) {
        $customer = $result_customer->fetch_assoc();
        $customer_id = $customer['customer_id'];
        $shipping_address = $customer['address'];
        $shipping_city = $customer['city'];
        $shipping_state = $customer['state'];
        $shipping_zip_code = $customer['zip_code'];
        $shipping_phone_number = $customer['phone_number'];

        // Insert order details into the orders table
        $sql_order = "INSERT INTO orders (customer_id, order_date, status, shipping_address, shipping_city, shipping_state, shipping_zip_code, shipping_phone_number) VALUES (?, NOW(), 'Pending', ?, ?, ?, ?, ?)";
        $stmt_order = $conn->prepare($sql_order);
        $stmt_order->bind_param("isssss", $customer_id, $shipping_address, $shipping_city, $shipping_state, $shipping_zip_code, $shipping_phone_number);

        if ($stmt_order->execute()) {
            $order_id = $stmt_order->insert_id;

            // Insert order item
            $sql_order_item = "INSERT INTO order_items (order_id, product_id, quantity) VALUES (?, ?, 1)";
            $stmt_order_item = $conn->prepare($sql_order_item);
            $stmt_order_item->bind_param("ii", $order_id, $product_id);
            $stmt_order_item->execute();

            echo "Order placed successfully!";
        } else {
            echo "Error placing order.";
        }
    } else {
        echo "Customer not found.";
    }
}

$conn->close();
?>
