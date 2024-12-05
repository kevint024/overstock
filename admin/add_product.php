<?php
include 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Existing product details
    $product_name = $_POST['product_name'];
    $category = $_POST['category'];
    $original_price = $_POST['original_price'];
    $discount_price = $_POST['discount_price'];
    $stock_quantity = $_POST['stock_quantity'];

    // Handle image upload
    if (isset($_FILES['main_image']) && $_FILES['main_image']['error'] == 0) {
        $target_dir = __DIR__ . "/../uploads/"; // Adjusted to be relative to add_product.php
        $target_file = $target_dir . basename($_FILES["main_image"]["name"]);

        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check if image file is a valid image
        $check = getimagesize($_FILES["main_image"]["tmp_name"]);
        if ($check !== false) {
            // File is an image
            $uploadOk = 1;
        } else {
            echo "File is not an image.";
            $uploadOk = 0;
        }

        // Check file size (5MB limit for this example)
        if ($_FILES["main_image"]["size"] > 5000000) {
            echo "Sorry, your file is too large.";
            $uploadOk = 0;
        }

        // Allow only certain formats
        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
            echo "Sorry, only JPG, JPEG, & PNG files are allowed.";
            $uploadOk = 0;
        }

        // Upload file if everything is OK
        if ($uploadOk == 1) {
            if (move_uploaded_file($_FILES["main_image"]["tmp_name"], $target_file)) {
                // Successfully uploaded, now insert product into database
                $relative_file_path = "uploads/" . basename($_FILES["main_image"]["name"]);
                $sql = "INSERT INTO products (product_name, category, original_price, discount_price, stock_quantity, main_image)
                        VALUES ('$product_name', '$category', '$original_price', '$discount_price', '$stock_quantity', '$relative_file_path')";

                if ($conn->query($sql) === TRUE) {
                    echo "New product added successfully with an image.";
                } else {
                    echo "Error: " . $sql . "<br>" . $conn->error;
                }
            } else {
                echo "Sorry, there was an error uploading your file.";
            }
        }
    } else {
        echo "Image upload failed.";
    }

    $conn->close();
}
?>
