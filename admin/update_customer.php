<?php
include('db_connection.php');

if (isset($_GET['id'])) {
    $customer_id = $_GET['id'];

    // Fetch customer details
    $sql = "SELECT * FROM customers WHERE customer_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $customer_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $customer = $result->fetch_assoc();
    } else {
        echo "Customer not found.";
        exit();
    }
    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle form submission for updating
    $firstName = $_POST['first_name'];
    $lastName = $_POST['last_name'];
    $email = $_POST['email'];
    $phoneNumber = $_POST['phone_number'];
    $address = $_POST['address'];
    $city = $_POST['city'];
    $state = $_POST['state'];
    $zipCode = $_POST['zip_code'];

    // Update the customer in the database
    $sql = "UPDATE customers SET first_name = ?, last_name = ?, email = ?, phone_number = ?, address = ?, city = ?, state = ?, zip_code = ? WHERE customer_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssssi", $firstName, $lastName, $email, $phoneNumber, $address, $city, $state, $zipCode, $customer_id);

    if ($stmt->execute()) {
        header("Location: customers.php?status=updated");
        exit();
    } else {
        echo "Error updating customer: " . $stmt->error;
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
    <title>Update Customer</title>
</head>
<body>
    <h1>Update Customer</h1>
    <form action="" method="POST">
        <label for="first_name">First Name:</label>
        <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($customer['first_name']); ?>" required><br><br>

        <label for="last_name">Last Name:</label>
        <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($customer['last_name']); ?>" required><br><br>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($customer['email']); ?>" required><br><br>

        <label for="phone_number">Phone Number:</label>
        <input type="text" id="phone_number" name="phone_number" value="<?php echo htmlspecialchars($customer['phone_number']); ?>"><br><br>

        <label for="address">Address:</label>
        <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($customer['address']); ?>"><br><br>

        <label for="city">City:</label>
        <input type="text" id="city" name="city" value="<?php echo htmlspecialchars($customer['city']); ?>"><br><br>

        <label for="state">State:</label>
        <input type="text" id="state" name="state" value="<?php echo htmlspecialchars($customer['state']); ?>"><br><br>

        <label for="zip_code">Zip Code:</label>
        <input type="text" id="zip_code" name="zip_code" value="<?php echo htmlspecialchars($customer['zip_code']); ?>"><br><br>

        <button type="submit">Update Customer</button>
    </form>
</body>
</html>
