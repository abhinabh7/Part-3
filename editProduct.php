<?php
include 'connection.php'; // Include the connection file

// Fetch categories for dropdown
$categories_query = "SELECT * FROM categories";
$categories_result = $mysqli->query($categories_query);

// Initialize variables
$product_name = $product_category = $product_price = $product_stock = $product_description = $imagePath = "";
$errorMessage = $successMessage = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_name = mysqli_real_escape_string($mysqli, $_POST["name"]);
    $product_category = isset($_POST["category_id"]) ? mysqli_real_escape_string($mysqli, $_POST["category_id"]) : "";
    $product_price = mysqli_real_escape_string($mysqli, $_POST["price"]);
    $product_stock = mysqli_real_escape_string($mysqli, $_POST["stock"]);
    $product_description = mysqli_real_escape_string($mysqli, $_POST["description"]);

    // Handle file upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['image']['tmp_name'];
        $fileName = $_FILES['image']['name'];
        $fileSize = $_FILES['image']['size'];
        $fileType = $_FILES['image']['type'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($fileExtension, $allowedExtensions) && $fileSize <= 5 * 1024 * 1024) {
            $uploadDir = 'uploads/';
            $newFileName = uniqid('img_', true) . '.' . $fileExtension;
            $destPath = $uploadDir . $newFileName;

            if (move_uploaded_file($fileTmpPath, $destPath)) {
                $imagePath = $newFileName;
            } else {
                $errorMessage = "Error moving uploaded file.";
            }
        } else {
            $errorMessage = "Invalid file type or size.";
        }
    } else {
        $imagePath = ""; // Set empty if no file uploaded
    }

    // Validate required fields
    if (empty($product_name) || empty($product_category) || empty($product_price) || empty($product_stock) || empty($product_description)) {
        $errorMessage = "All fields are required.";
    } else {
        // Insert or update the product
        $sql = "INSERT INTO products (name, category_id, price, stock, description, image)
                VALUES ('$product_name', '$product_category', '$product_price', '$product_stock', '$product_description', '$imagePath')";
        $result = $mysqli->query($sql);

        if (!$result) {
            $errorMessage = "Error: " . $mysqli->error;
        } else {
            $successMessage = "Product added successfully.";
            header("Location: ./product_list.php");
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 500px;
            margin: 0 auto;
            background: #f9f9f9;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            font-size: 14px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .form-group select {
            width: 100%;
            padding: 10px;
            font-size: 14px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .alert {
            color: #d9534f;
            margin-bottom: 15px;
        }

        .success {
            color: #5cb85c;
            margin-bottom: 15px;
        }

        .login-btn {
            background-color: #5cb85c;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
        }

        .login-btn:hover {
            background-color: #4cae4c;
        }

        a {
            display: inline-block;
            margin-top: 10px;
            color: #5bc0de;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Edit Product</h2>

        <?php if (!empty($errorMessage)): ?>
            <div class="alert">
                <strong><?php echo $errorMessage; ?></strong>
            </div>
        <?php endif; ?>

        <?php if (!empty($successMessage)): ?>
            <div class="success">
                <strong><?php echo $successMessage; ?></strong>
            </div>
        <?php endif; ?>

        <form method="post">
            <div class="form-group">
                <label for="name">Product Name</label>
                <input type="text" name="name" value="<?php echo htmlspecialchars($product_name); ?>">
            </div>

            <div class="form-group">
                <label for="category_id">Category</label>
                <!-- Category dropdown with available categories -->
                <select name="category_id">
                    <?php while ($category = $categories_result->fetch_assoc()): ?>
                        <option value="<?php echo $category['id']; ?>" <?php echo ($category['id'] == $product_category) ? 'selected' : ''; ?>>
                            <?php echo $category['name']; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="price">Price</label>
                <input type="text" name="price" value="<?php echo htmlspecialchars($product_price); ?>">
            </div>

            <div class="form-group">
                <label for="stock">Stock</label>
                <input type="text" name="stock" value="<?php echo htmlspecialchars($product_stock); ?>">
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea name="description"><?php echo htmlspecialchars($product_description); ?></textarea>
            </div>

            <div class="form-group">
                <label for="image">Product Image</label>
                <input type="file" name="image">
            </div>


            <button type="submit" class="login-btn">Update</button>
            <a href="./product_list.php" role="button">Cancel</a>
        </form>
    </div>
</body>

</html>

<?php
// Close the database connection
$mysqli->close();
?>