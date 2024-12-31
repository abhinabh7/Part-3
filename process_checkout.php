    <?php
    // Include the database connection file
    include 'connection.php';

    // Start session
    session_start();

    // Check if the user is logged in
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }

    // Validate and sanitize inputs
    $user_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;
    $product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
    $address = isset($_POST['address']) ? trim($_POST['address']) : '';
    $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
    $payment_method = isset($_POST['payment_method']) ? trim($_POST['payment_method']) : '';

    // Validate required fields
    if (empty($address) || empty($phone) || empty($payment_method)) {
        die("<p>Invalid input. Please fill all the fields correctly.</p>");
    }

    // Validate phone number
    if (!preg_match('/^[0-9]{10}$/', $phone)) {
        die("<p>Invalid phone number. Please use a valid 10-digit number.</p>");
    }

    // Validate payment method
    if ($payment_method !== 'cod') {
        die("<p>Invalid payment method.</p>");
    }

    // Fetch product price
    $stmt = $mysqli->prepare("SELECT price FROM products WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 0) {
        die("<p>Product not found.</p>");
    }
    $product = $result->fetch_assoc();
    $total_price = $product['price'];

    // Insert the order into the database
    $stmt = $mysqli->prepare("INSERT INTO orders (user_id, product_id, total_price, shipping_address, payment_status, order_status) VALUES (?, ?, ?, ?, 'Pending', 'Pending')");
    $stmt->bind_param("iids", $user_id, $product_id, $total_price, $address);
    if ($stmt->execute()) {
        // Fetch the inserted order ID
        $order_id = $stmt->insert_id;

        // Output the HTML content for order confirmation
        echo "
        <!DOCTYPE html>
        <html lang='en'>
            <head>
                <meta charset='UTF-8'>
                <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                <title>Order Confirmation</title>
                <link rel='stylesheet' href='style/order_confirmation.css'>
                <link href='https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
            </head>
            <body>
                <div class='popup'>
                    <h1>Order Confirmation</h1>
                    <div class='success-icon'>
                        <i class='bx bx-check'></i>
                    </div>
                    <p>Your order has been placed successfully.</p>
                    <a href='index.php'>
                        <button class='complete-btn'>Thank You</button>
                    </a>
                </div>
            </body>
        </html>";
    } else {
        echo "<p>Failed to place order. Please try again.</p>";
    }
