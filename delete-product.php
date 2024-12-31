<?php
$errorMessage = "";
$successMessage = "";

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$database = "sheesh";

$connection = new mysqli($servername, $username, $password, $database);

if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

// Get product ID from query parameter
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id > 0) {
    // Delete query
    $sql = "DELETE FROM products WHERE id=$id";
    $result = $connection->query($sql);

    if (!$result) {
        $errorMessage = "Error: " . $connection->error;
    } else {
        $successMessage = "Product deleted successfully.";
    }
}

// Close the database connection
$connection->close();

// Redirect to product list page if operation is complete
if (!empty($successMessage) || !empty($errorMessage)) {
    header("Location: product_list.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Product</title>
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
        <h2>Delete Product</h2>

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

        <a href="product-list.php" role="button">Back to Product List</a>
    </div>
</body>
</html>
