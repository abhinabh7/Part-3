<?php
require 'connection.php'; // Include the connection file

// Initialize variables for error messages
$nameErr = $categoryErr = $priceErr = $stockErr = $imageErr = "";
$name = $category = $price = $stock = "";
$errorMessage = $successMessage = "";

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data and sanitize it
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $categoryId = $_POST['category'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $imagePath = "";

    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['image']['name'];
        $file_extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if (!in_array($file_extension, $allowed)) {
            $imageErr = "Only JPG, JPEG, PNG & GIF files are allowed.";
            $errorMessage = $imageErr;
        } else {
            // Create unique filename
            $newFilename = uniqid() . '.' . $file_extension;
            $uploadPath = 'image/' . $newFilename;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
                $imagePath = $uploadPath;
            } else {
                $imageErr = "Failed to upload image.";
                $errorMessage = $imageErr;
            }
        }
    } else {
        $imageErr = "Image is required.";
        $errorMessage = $imageErr;
    }

    // Validation check for empty fields
    if (empty($name) || empty($categoryId) || empty($price) || empty($stock)) {
        $errorMessage = "All fields are required.";
    } elseif (!is_numeric($price) || !is_numeric($stock)) {
        $errorMessage = "Price and Stock must be numeric.";
    } elseif (empty($imagePath)) {
        $errorMessage = "Please upload an image.";
    } else {
        // Validate category_id
        $categoryQuery = $mysqli->prepare("SELECT id FROM categories WHERE id = ?");
        $categoryQuery->bind_param("i", $categoryId);
        $categoryQuery->execute();
        $categoryQuery->store_result();

        if ($categoryQuery->num_rows == 0) {
            $errorMessage = "Invalid category selected.";
        } else {
            // SQL Insert with image
            $stmt = $mysqli->prepare("INSERT INTO products (name, description, category_id, price, stock, image) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssidis", $name, $description, $categoryId, $price, $stock, $imagePath);

            if ($stmt->execute()) {
                $successMessage = "New product added successfully!";
                header("Location: product_list.php");
                exit;
            } else {
                $errorMessage = "Error: " . $stmt->error;
            }
            $stmt->close();
        }
        $categoryQuery->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Product</title>
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

        input,
        textarea,
        select,
        button {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        .error {
            color: red;
            font-size: 0.9em;
        }

        .success {
            color: green;
            font-size: 1em;
            margin-top: 10px;
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
        <h2>Add New Product</h2>
        <?php if (!empty($errorMessage)): ?>
            <div class="error"><strong><?php echo $errorMessage; ?></strong></div>
        <?php endif; ?>
        <?php if (!empty($successMessage)): ?>
            <div class="success"><strong><?php echo $successMessage; ?></strong></div>
        <?php endif; ?>
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="name">Product Name:</label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($name ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="description">Description:</label>
                <textarea id="description" name="description"><?php echo htmlspecialchars($description ?? ''); ?></textarea>
            </div>
            <div class="form-group">
                <label for="category">Category:</label>
                <select id="category" name="category">
                    <option value="">Select a category</option>
                    <?php
                    $categoryResult = $mysqli->query("SELECT id, name FROM categories");
                    while ($categoryRow = $categoryResult->fetch_assoc()) {
                        $selected = ($categoryId ?? '') == $categoryRow['id'] ? 'selected' : '';
                        echo "<option value='{$categoryRow['id']}' $selected>{$categoryRow['name']}</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="price">Price:</label>
                <input type="number" step="0.01" id="price" name="price" value="<?php echo htmlspecialchars($price ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="stock">Stock:</label>
                <input type="number" id="stock" name="stock" value="<?php echo htmlspecialchars($stock ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="image">Product Image:</label>
                <input type="file" id="image" name="image" accept="image/*">
            </div>
            <button type="submit" class="login-btn">Add Product</button>
            <a href="product_list.php">Cancel</a>
        </form>
    </div>
</body>

</html> 