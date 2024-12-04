<?php
include('db_connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = $_POST['first_name'];
    $lastName = $_POST['last_name'];
    $email = $_POST['email'];
    $phoneNumber = $_POST['phone_number'];
    $address = $_POST['address'];
    $city = $_POST['city'];
    $state = $_POST['state'];
    $zipCode = $_POST['zip_code'];

    $sql = "INSERT INTO customers (first_name, last_name, email, phone_number, address, city, state, zip_code) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssss", $firstName, $lastName, $email, $phoneNumber, $address, $city, $state, $zipCode);

    if ($stmt->execute()) {
        header("Location: customers.php?status=success");
        exit();
    } else {
        echo "Error adding customer: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
