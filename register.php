<?php
include __DIR__ . '/admin/db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect user details from the form
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Collect customer details from the form
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $address = $_POST['address'];
    $city = $_POST['city'];
    $state = $_POST['state'];
    $zip_code = $_POST['zip_code'];
    $phone_number = $_POST['phone_number'];

    // Check if the passwords match
    if ($password !== $confirm_password) {
        $error_message = "Passwords do not match.";
    } else {
        // Check if username already exists
        $sql_check = "SELECT * FROM users WHERE username = ?";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->bind_param("s", $username);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();

        if ($result_check && $result_check->num_rows > 0) {
            $error_message = "Username already exists. Please choose a different one.";
        } else {
            // Hash the password for security
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $role = 'user';  // Default role is 'user' to prevent admin privileges

            // Insert new user into the database
            $sql_insert = "INSERT INTO users (username, password, role) VALUES (?, ?, ?)";
            $stmt_insert = $conn->prepare($sql_insert);
            $stmt_insert->bind_param("sss", $username, $hashed_password, $role);

            if ($stmt_insert->execute()) {
                // Get the newly created user ID
                $new_user_id = $stmt_insert->insert_id;

                // Create an entry in the customers table linked to the new user
                $sql_customer_insert = "INSERT INTO customers (user_id, first_name, last_name, address, city, state, zip_code, phone_number) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt_customer_insert = $conn->prepare($sql_customer_insert);
                $stmt_customer_insert->bind_param("isssssss", $new_user_id, $first_name, $last_name, $address, $city, $state, $zip_code, $phone_number);

                if ($stmt_customer_insert->execute()) {
                    $success_message = "Account created successfully. Please log in.";
                    header("Location: login.php?status=registered");
                    exit();
                } else {
                    $error_message = "Error creating customer record: " . $stmt_customer_insert->error;
                }

                $stmt_customer_insert->close();
            } else {
                $error_message = "Error creating account: " . $stmt_insert->error;
            }

            $stmt_insert->close();
        }

        $stmt_check->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <h1>Register</h1>
    <?php if (isset($error_message)) { echo "<p style='color: red;'>$error_message</p>"; } ?>
    <?php if (isset($success_message)) { echo "<p style='color: green;'>$success_message</p>"; } ?>

    <form action="register.php" method="POST">
        <!-- User Details -->
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required><br><br>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br><br>

        <label for="confirm_password">Confirm Password:</label>
        <input type="password" id="confirm_password" name="confirm_password" required><br><br>

        <!-- Customer Details -->
        <label for="first_name">First Name:</label>
        <input type="text" id="first_name" name="first_name" required><br><br>

        <label for="last_name">Last Name:</label>
        <input type="text" id="last_name" name="last_name" required><br><br>

        <label for="address">Address:</label>
        <input type="text" id="address" name="address" required><br><br>

        <label for="city">City:</label>
        <input type="text" id="city" name="city" required><br><br>

        <label for="state">State:</label>
        <input type="text" id="state" name="state" required><br><br>

        <label for="zip_code">Zip Code:</label>
        <input type="text" id="zip_code" name="zip_code" required><br><br>

        <label for="phone_number">Phone Number:</label>
        <input type="text" id="phone_number" name="phone_number" required><br><br>

        <button type="submit" class="button-primary">Register</button>
    </form>
    <br><a href="login.php">Back to Login</a>
</body>
</html>
