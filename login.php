<?php
// Initialize the variable
$is_invalid = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $mysqli = require __DIR__ . "/connection.php";

    $sql = sprintf(
        "SELECT * FROM user WHERE email = '%s'",
        $mysqli->real_escape_string($_POST["email"])
    );

    $result = $mysqli->query($sql);

    $user = $result->fetch_assoc();

    if ($user) {
        if (password_verify($_POST["password"], $user["password_hash"])) {
            session_start();
            session_regenerate_id();
            $_SESSION["user_id"] = $user["id"];
            header("Location: index.php");
            exit;
        }
    }

    $is_invalid = true;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/style/login.css">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
        }

        .login-container {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 300px;
        }

        .login-container h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
        }

        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .login-btn {
            width: 100%;
            padding: 10px;
            background-color: #e5595d;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .login-btn:hover {
            background-color: #ff4d4d;
        }

        .signup-link {
            text-align: center;
            margin-top: 15px;
        }

        .error {
            color: red;
            font-size: 0.8em;
        }

        .invalid-login {
            text-align: center;
            color: red;
            margin-bottom: 15px;
            font-size: 0.9em;
        }
    </style>
</head>

<body>
    <div class="login-container">
        <h2>Login</h2>

        <?php if ($is_invalid): ?>
            <div class="invalid-login">Invalid login. Please try again.</div>
        <?php endif; ?>

        <form id="loginForm" method="post">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($_POST["email"] ?? "") ?>">
                <span class="error" id="emailError"></span>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
                <span class="error" id="passwordError"></span>
            </div>
            <button type="button" class="login-btn" id="loginButton">Login</button>
        </form>

        <div class="signup-link">
            <p>Don't have an account? <a href="sign_up.php">Sign Up</a></p>
        </div>
    </div>

    <script>
        document.getElementById('loginButton').addEventListener('click', function() {
            let isValid = true;

            // Validate email
            const emailField = document.getElementById('email');
            const emailError = document.getElementById('emailError');
            const emailValue = emailField.value.trim();

            if (!emailValue.match(/^\S+@\S+\.\S+$/)) {
                emailError.textContent = 'Please enter a valid email address.';
                emailField.setCustomValidity('Invalid email address');
                isValid = false;
            } else {
                emailError.textContent = '';
                emailField.setCustomValidity('');
            }

            // Validate password
            const passwordField = document.getElementById('password');
            const passwordError = document.getElementById('passwordError');
            const passwordValue = passwordField.value.trim();

            if (passwordValue.length < 8) {
                passwordError.textContent = 'Password must be at least 8 characters long.';
                passwordField.setCustomValidity('Password too short');
                isValid = false;
            } else {
                passwordError.textContent = '';
                passwordField.setCustomValidity('');
            }

            // If both email and password are valid, submit the form
            if (isValid) {
                document.getElementById('loginForm').submit();
            }
        });
    </script>
</body>

</html>
