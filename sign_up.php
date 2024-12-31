<?php
require 'connection.php'; // Import the database connection

// Initialize variables
$errors = [];
$successMessage = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect and sanitize form data
    $fullname = htmlspecialchars(trim($_POST['fullname'] ?? ''));
    $email = htmlspecialchars(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';
    $confirmpassword = $_POST['confirmpassword'] ?? '';
    $is_admin = isset($_POST['is_admin']) ? 1 : 0;

    // Validate input
    if (empty($fullname) || empty($email) || empty($password) || empty($confirmpassword)) {
        $errors[] = "All fields are required.";
    } elseif (strlen($fullname) < 3) {
        $errors[] = "Full name must be at least 3 characters.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    } elseif ($password !== $confirmpassword) {
        $errors[] = "Passwords do not match.";
    } elseif (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters.";
    }

    // Check if the email already exists
    if (empty($errors)) {
        $checkEmailQuery = "SELECT id FROM user WHERE email = ?";
        $stmt = $mysqli->prepare($checkEmailQuery);
        if ($stmt) {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $errors[] = "Email is already registered.";
            }
            $stmt->close();
        } else {
            $errors[] = "Database error: " . $mysqli->error;
        }
    }

    // Insert the user into the database if no errors
    if (empty($errors)) {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $insertQuery = "INSERT INTO user (fullname, email, password_hash, is_admin) VALUES (?, ?, ?, ?)";
        $stmt = $mysqli->prepare($insertQuery);

        if ($stmt) {
            $stmt->bind_param("sssi", $fullname, $email, $password_hash, $is_admin);
            if ($stmt->execute()) {
                // Redirect to the signup success page
                header("Location: signup_success.php");
                exit();
            } else {
                $errors[] = "Database error: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $errors[] = "Database error: " . $mysqli->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link rel="stylesheet" href="style/sign-up.css">
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            document.getElementById("signupForm").addEventListener("submit", function(event) {
                event.preventDefault();

                let isValid = true;

                // Validate full name
                const fullname = document.getElementById("fullname").value.trim();
                const fullnameError = document.getElementById("fullnameError");
                if (fullname.length < 3) {
                    fullnameError.textContent = "Full name must be at least 3 characters.";
                    isValid = false;
                } else {
                    fullnameError.textContent = "";
                }

                // Validate email
                const email = document.getElementById("email").value.trim();
                const emailError = document.getElementById("emailError");
                const emailRegex = /^\S+@\S+\.\S+$/;
                if (!emailRegex.test(email)) {
                    emailError.textContent = "Please enter a valid email address.";
                    isValid = false;
                } else {
                    emailError.textContent = "";
                }

                // Validate password
                const password = document.getElementById("password").value.trim();
                const passwordError = document.getElementById("passwordError");
                if (password.length < 8) {
                    passwordError.textContent = "Password must be at least 8 characters.";
                    isValid = false;
                } else {
                    passwordError.textContent = "";
                }

                // Validate confirm password
                const confirmPassword = document.getElementById("confirmpassword").value.trim();
                const confirmPasswordError = document.getElementById("confirmpasswordError");
                if (confirmPassword !== password) {
                    confirmPasswordError.textContent = "Passwords do not match.";
                    isValid = false;
                } else {
                    confirmPasswordError.textContent = "";
                }

                if (isValid) {
                    this.submit();
                }
            });
        });
    </script>
</head>

<body>
    <div class="signup-container">
        <h2>Sign Up</h2>

        <?php if (!empty($errors)): ?>
            <div class="error">
                <?= implode('<br>', $errors) ?>
            </div>
        <?php endif; ?>

        <form id="signupForm" method="post" novalidate>
            <div class="form-group">
                <label for="fullname">Full Name</label>
                <input type="text" id="fullname" name="fullname" value="<?= htmlspecialchars($_POST['fullname'] ?? '') ?>" required>
                <span class="error" id="fullnameError"></span>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                <span class="error" id="emailError"></span>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
                <span class="error" id="passwordError"></span>
            </div>
            <div class="form-group">
                <label for="confirmpassword">Confirm Password</label>
                <input type="password" id="confirmpassword" name="confirmpassword" required>
                <span class="error" id="confirmpasswordError"></span>
            </div>
            <div class="form-group">
                <label for="is_admin">Admin</label>
                <input type="checkbox" id="is_admin" name="is_admin">
                <span class="error" id="isAdminError"></span>
            </div>
            <button type="submit" class="signup-btn">Sign up</button>
        </form>
        <div class="signin-link">
            <p>Already a member? <a href="login.php">Sign in</a></p>
        </div>
    </div>
</body>

</html>