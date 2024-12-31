<?php
require_once "connection.php"; // Include the connection file
$name = $email = $phone = $address = ""; // Initialize variables
$errorMessage = $successMessage = ""; // Initialize messages

// Get customer ID from query parameter
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id > 0) {
    // Fetch the existing customer data from the database
    $result = $mysqli->query("SELECT * FROM clients WHERE id=$id");

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $name = $row['name'];
        $email = $row['email'];  // Email is not editable
        $phone = $row['phone'];
        $address = $row['address'];
    } else {
        die("Customer not found.");
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the submitted form data
    $name = mysqli_real_escape_string($mysqli, $_POST["name"]);
    $phone = mysqli_real_escape_string($mysqli, $_POST["phone"]);
    $address = mysqli_real_escape_string($mysqli, $_POST["address"]);

    // Validate inputs
    if (empty($name) || empty($phone) || empty($address)) {
        $errorMessage = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMessage = "Invalid email format.";
    } elseif (!is_numeric($phone)) {
        $errorMessage = "Phone number should be numeric.";
    } else {
        // Update the customer details in the database
        $sql = "UPDATE clients SET name='$name', phone='$phone', address='$address' WHERE id=$id";
        $result = $mysqli->query($sql);

        if (!$result) {
            $errorMessage = "Error: " . $mysqli->error;
        } else {
            $successMessage = "Customer details updated successfully.";
            // Redirect to the admin dashboard
            header("Location: ./admin_dashboard.php");
            exit;
        }
    }
}

// Close the database connection
$mysqli->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Customer</title>
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

        .form-group input {
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
        <h2>Edit Customer</h2>

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
                <label for="name">Name</label>
                <input type="text" name="name" value="<?php echo htmlspecialchars($name); ?>" required>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" readonly>
            </div>

            <div class="form-group">
                <label for="phone">Phone</label>
                <input type="text" name="phone" value="<?php echo htmlspecialchars($phone); ?>" required>
            </div>

            <div class="form-group">
                <label for="address">Address</label>
                <input type="text" name="address" value="<?php echo htmlspecialchars($address); ?>" required>
            </div>

            <button type="submit" class="login-btn">Update</button>
            <a href="./admin_dashboard.php" role="button">Cancel</a>
        </form>
    </div>
</body>
</html>
