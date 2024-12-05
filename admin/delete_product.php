<?php
// Enable error reporting to help with debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include the database connection
include 'db_connection.php';

// Handling both GET and POST methods for deletion
if (($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['product_id'])) ||
    ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id']))) {

    // Determine if request is GET or POST and get the product ID
    $product_id = $_SERVER["REQUEST_METHOD"] == "POST" ? $_POST['product_id'] : $_GET['id'];

    // Debugging - Show that the product ID was received
    echo "Received Product ID: " . htmlspecialchars($product_id) . "<br>";

    // Check if the product is in any orders
    $check_sql = "SELECT COUNT(*) as order_count FROM order_items WHERE product_id = ?";
    $stmt_check = $conn->prepare($check_sql);

    if ($stmt_check) {
        $stmt_check->bind_param("i", $product_id);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();

        if ($result_check) {
            $row = $result_check->fetch_assoc();
            if ($row['order_count'] > 0) {
                // If product is part of an order, prevent deletion
                echo "Error: Cannot delete product because it is part of an existing order.<br>";
                die();  // Stop here for debugging
            } else {
                // Proceed with product deletion
                $delete_sql = "DELETE FROM products WHERE product_id = ?";
                echo "SQL Statement to Execute: " . $delete_sql . "<br>";  // Debugging

                $stmt_delete = $conn->prepare($delete_sql);
                if ($stmt_delete) {
                    $stmt_delete->bind_param("i", $product_id);
                    if ($stmt_delete->execute()) {
                        echo "Product deleted successfully.<br>";
                        die();  // Stop to confirm success before redirecting
                    } else {
                        echo "Error deleting product: " . $stmt_delete->error . "<br>";
                        die();  // Stop to see the error message
                    }
                    $stmt_delete->close();
                } else {
                    echo "Failed to prepare delete statement.<br>";
                    die();  // Stop if the delete statement preparation fails
                }
            }
        } else {
            echo "Failed to get the result from the check statement.<br>";
            die();  // Stop here if the result retrieval fails
        }
        $stmt_check->close();
    } else {
        echo "Failed to prepare check statement.<br>";
        die();  // Stop if the check statement preparation fails
    }
} else {
    echo "Product ID not set or request is neither POST nor GET.<br>";
    die();  // Stop if the request is not valid
}

// Close the database connection
$conn->close();
?>
