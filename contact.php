<?php
// Start session for user authentication
session_start();

// Include the database connection
include('connection.php');

// Check if the user is logged in
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id']; // Get logged-in user ID
} else {
    $user_id = null; // No user logged in
}

// Fetch cart count if the user is logged in
$cart_count = 0;
if ($user_id !== null) {
    $stmt = $mysqli->prepare("SELECT COUNT(*) AS total_items FROM user_cart WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $cart_count = $result->fetch_assoc()['total_items'] ?? 0;
}

// Close the MySQL connection
$mysqli->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Matradude</title>
    <link rel="stylesheet" href="style/home.css">
    <script src="js/cart_count.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
</head>

<body>
    <header>
        <a href="index.php" class="logo"><img src="./image/Matra.png" alt=""></a>
        <ul class="navmenu">
            <li><a href="index.php">home</a></li>
            <li><a href="products.php">products</a></li>
            <li><a href="contact.php">contact</a></li>
        </ul>
        <div class="nav_icon">
            <a href="customer_dashboard.php"><i class='bx bx-user'></i></a>
            <a href="cart.php" class="cart-icon">
                <i class='bx bx-cart'></i>
                <span id="cart-count" class="cart-count"><?php echo $cart_count; ?></span>
            </a>
        </div>
    </header>

    <section class="contact-section">
        <h1>Contact Us</h1>
        <form method="post" action="">
            <div class="form-row">
                <input type="text" id="name" name="name" placeholder="Your Name">
                <input type="email" id="email" name="email" placeholder="Your Email">
            </div>
            <textarea id="message" name="msg" placeholder="Your Message"></textarea>
            <button id="btn" type="submit" name="send" class="submit-btn">Send Message</button>
        </form>

        <?php

        use PHPMailer\PHPMailer\PHPMailer;
        use PHPMailer\PHPMailer\SMTP;
        use PHPMailer\PHPMailer\Exception;

        if (isset($_POST['send'])) {
            $name = $_POST['name'];
            $email = $_POST['email'];
            $msg = $_POST['msg'];

            // Load Composer's autoloader
            require 'PHPMailer/Exception.php';
            require 'PHPMailer/PHPMailer.php';
            require 'PHPMailer/SMTP.php';

            // Create an instance; passing `true` enables exceptions
            $mail = new PHPMailer(true);

            try {
                // Server settings
                $mail->isSMTP();                                            // Send using SMTP
                $mail->Host       = 'smtp.gmail.com';                        // Set the SMTP server to send through
                $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
                $mail->Username   = 'bikashthapa9815648792@gmail.com';      // SMTP username
                $mail->Password   = 'qhejzpehpywecwdn';                     // SMTP password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            // Enable implicit TLS encryption
                $mail->Port       = 465;                                    // TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

                // Recipients
                $mail->setFrom('bikashthapa9815648792@gmail.com', 'contact form');
                $mail->addAddress('bikashthapa9815648792@gmail.com', 'Bikash Thapa'); // Add a recipient

                // Content
                $mail->isHTML(true);                                          // Set email format to HTML
                $mail->Subject = 'Test Contact Form';
                $mail->Body    = "Sender Name - $name <br> Sender Email - $email <br> message - $msg";

                $mail->send();
                echo "<div class='success'> Message has been Sent!</div>";
            } catch (Exception $e) {
                echo "<div class='alert'> Message couldn't Send!</div>";
            }
        }
        ?>

        <style>
            /* Contact form CSS */
            .alert,
            .success {
                width: 400px;
                text-align: center;
                position: absolute;
                top: 30px;
                left: 50%;
                transform: translateX(-50%);
                color: whitesmoke;
                padding: 8px 0;
            }

            .alert {
                background-color: rgb(252, 59, 59);
            }

            .success {
                background-color: rgb(44, 158, 24);
            }
        </style>

        <div class="contact-info">
            <h3>Contact Info</h3>
            <p><i class="bx bx-map icon"></i> Golmadhi -07-Bhaktapur, 8848, Nepal</p>
            <p><i class="bx bx-phone icon"></i> +9779864737132</p>
            <p><i class="bx bx-envelope icon"></i> matradude@gmail.com</p>
        </div>
    </section>

    <footer>
        <?php include('footer.php'); ?>
    </footer>

</body>

</html>