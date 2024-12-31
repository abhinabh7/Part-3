<?php
session_start();
require 'connection.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = htmlspecialchars(trim($_POST['email']));
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $errors[] = "Please enter both email and password.";
    } else {
        $checkEmailQuery = "SELECT id, fullname, password_hash, is_admin FROM user WHERE email = ?";
        $stmt = $mysqli->prepare($checkEmailQuery);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($id, $fullname, $password_hash, $is_admin);
            $stmt->fetch();

            if (password_verify($password, $password_hash)) {
                $_SESSION['user_id'] = $id;
                $_SESSION['fullname'] = $fullname;
                $_SESSION['is_admin'] = $is_admin;

                if ($is_admin == 1) {
                    header("Location: admin_dashboard.php");
                } else {
                    header("Location: cart.php");
                }
                exit();
            } else {
                $errors[] = "Invalid email or password.";
            }
        } else {
            $errors[] = "Invalid email or password.";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="style/login.css">
</head>

<body>
    <div class="login-container">
        <h2>Login</h2>

        <?php
        if (!empty($errors)) {
            foreach ($errors as $error) {
                echo "<div class='error'>$error</div>";
            }
        }
        ?>

        <form method="post" novalidate>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
                <div id="emailError" class="error"></div>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
                <div id="passwordError" class="error"></div>
            </div>
            <button type="submit" class="login-btn">Login</button>
        </form>

        <div class="signup-link">
            <p>Don't have an account? <a href="sign_up.php">Sign Up</a></p>
        </div>
    </div>
</body>

</html>