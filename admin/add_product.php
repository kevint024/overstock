<?php
include 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Product details
    $product_name = $_POST['product_name'];
    $category = $_POST['category'];
    $original_price = $_POST['original_price'];
    $discount_price = $_POST['discount_price'];
    $stock_quantity = $_POST['stock_quantity'];
    $description = $_POST['description'];
    $deal_start_date = $_POST['deal_start_date'];
    $deal_end_date = $_POST['deal_end_date'];

    // Handle main image upload
    if (isset($_FILES['main_image']) && $_FILES['main_image']['error'] == 0) {
        $target_dir = __DIR__ . "/../uploads/"; // Directory for images
        $main_image_name = basename($_FILES["main_image"]["name"]);
        $target_file = $target_dir . $main_image_name;
        $relative_file_path = "uploads/" . $main_image_name; // Path to store in DB

        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check if image file is a valid image
        $check = getimagesize($_FILES["main_image"]["tmp_name"]);
        if ($check !== false) {
            $uploadOk = 1;
        } else {
            echo "File is not an image.";
            $uploadOk = 0;
        }

        // Check file size and type
        if ($_FILES["main_image"]["size"] > 5000000) {
            echo "Sorry, your file is too large.";
            $uploadOk = 0;
        }

        if (!in_array($imageFileType, ['jpg', 'jpeg', 'png'])) {
            echo "Sorry, only JPG, JPEG, & PNG files are allowed.";
            $uploadOk = 0;
        }

        // Upload main image and insert product into database if valid
        if ($uploadOk == 1) {
            if (move_uploaded_file($_FILES["main_image"]["tmp_name"], $target_file)) {
                // Insert product into the `products` table
                $sql = "INSERT INTO products (product_name, category, original_price, discount_price, stock_quantity, description, deal_start_date, deal_end_date, main_image)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

                // Use prepared statements to avoid SQL injection
                if ($stmt = $conn->prepare($sql)) {
                    $stmt->bind_param("ssddissss", $product_name, $category, $original_price, $discount_price, $stock_quantity, $description, $deal_start_date, $deal_end_date, $relative_file_path);
                    
                    // Execute the query
                    if ($stmt->execute()) {
                        $product_id = $conn->insert_id; // Get the ID of the newly inserted product

                        // Handle multiple additional image uploads
                        if (!empty($_FILES['additional_images']['name'][0])) {
                            foreach ($_FILES['additional_images']['name'] as $key => $value) {
                                $additional_image_name = basename($_FILES['additional_images']['name'][$key]);
                                $additional_image_tmp = $_FILES['additional_images']['tmp_name'][$key];
                                $additional_target_file = $target_dir . $additional_image_name;
                                $additional_relative_path = "uploads/" . $additional_image_name;

                                // Validate and upload additional images
                                $additional_image_type = strtolower(pathinfo($additional_target_file, PATHINFO_EXTENSION));
                                if (in_array($additional_image_type, ['jpg', 'jpeg', 'png']) && $_FILES['additional_images']['size'][$key] <= 5000000) {
                                    if (move_uploaded_file($additional_image_tmp, $additional_target_file)) {
                                        // Insert each additional image into the `product_images` table
                                        $image_sql = "INSERT INTO product_images (product_id, image_path) VALUES (?, ?)";
                                        if ($image_stmt = $conn->prepare($image_sql)) {
                                            $image_stmt->bind_param("is", $product_id, $additional_relative_path);
                                            $image_stmt->execute();
                                            $image_stmt->close();
                                        }
                                    } else {
                                        echo "Error uploading additional image: " . htmlspecialchars($value);
                                    }
                                }
                            }
                        }

                        echo "New product added successfully with images.";
                    } else {
                        echo "Error: " . $stmt->error;
                    }

                    $stmt->close();
                } else {
                    echo "Error preparing statement: " . $conn->error;
                }
            } else {
                echo "Sorry, there was an error uploading your main image.";
            }
        }
    } else {
        echo "Main image upload failed.";
    }

    $conn->close();
}
?>
