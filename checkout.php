<?php
// Include the database connection file
include 'connection.php';

// Start the session to check if the user is logged in
session_start();

// Check if the user is logged in by checking the session variable
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Get the product ID from the URL parameter
$product_id = isset($_GET['product_id']) ? (int)$_GET['product_id'] : 0;

// Fetch product and category data for the specific product using a prepared statement
$stmt = $mysqli->prepare("SELECT products.*, categories.name AS category_name 
                          FROM products 
                          INNER JOIN categories ON products.category_id = categories.id 
                          WHERE products.id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

// If no product found, redirect or show an error
if ($result->num_rows == 0) {
    echo "<p>Product not found.</p>";
    exit;
}

// Fetch the product details
$product = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link rel="stylesheet" href="style/home.css">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <style>
        .item-price {
            text-align: left;
        }

        .error {
            color: red;
            font-size: 12px;
        }
    </style>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            document.getElementById("checkoutForm").addEventListener("submit", function(event) {
                let isValid = true;

                // Validate Shipping Address
                const address = document.getElementById("address").value.trim();
                const addressError = document.getElementById("addressError");
                if (address.length < 5) {
                    addressError.textContent = "Address must be at least 5 characters long.";
                    isValid = false;
                } else {
                    addressError.textContent = "";
                }

                // Validate Phone Number
                const phone = document.getElementById("phone").value.trim();
                const phoneError = document.getElementById("phoneError");
                const phoneRegex = /^[0-9]{10}$/;
                if (!phoneRegex.test(phone)) {
                    phoneError.textContent = "Please enter a valid 10-digit phone number.";
                    isValid = false;
                } else {
                    phoneError.textContent = "";
                }

                // Validate Payment Method
                const paymentMethod = document.getElementById("payment_method").value.trim();
                const paymentMethodError = document.getElementById("paymentMethodError");
                if (paymentMethod === "") {
                    paymentMethodError.textContent = "Please select a payment method.";
                    isValid = false;
                } else {
                    paymentMethodError.textContent = "";
                }

                if (!isValid) {
                    event.preventDefault();
                }
            });
        });
    </script>
</head>

<body>
    <header>
        <a href="index.html" class="logo"><img src="./image/Matra.png" alt="Matra Logo"></a>
        <ul class="navmenu">
            <li><a href="index.html">Home</a></li>
            <li><a href="#">About</a></li>
            <li><a href="product.php">Products</a></li>
            <li><a href="#">Blog</a></li>
            <li><a href="#">Contact</a></li>
        </ul>
        <div class="nav_icon">
            <a href="customer_dashboard.php" aria-label="Customer Dashboard"><i class='bx bx-user'></i></a>
            <a href="cart.php" aria-label="View Cart"><i class='bx bx-cart'></i></a>
        </div>
    </header>

    <section class="checkout-page">
        <h1>Checkout</h1>
        <div class="checkout-section">
            <div class="cart-summary">
                <h2>Product You Are Buying:</h2>
                <div class="cart-item">
                    <div class="item-details">
                        <img src="./image/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="product-image">
                    </div>
                    <div class="item-price">
                        <p><strong>Product Name:</strong> <?php echo htmlspecialchars($product['name']); ?></p>
                        <p><strong>Category:</strong> <?php echo htmlspecialchars($product['category_name']); ?></p>
                        <p><strong>Price:</strong> NPR <?php echo number_format($product['price'], 2); ?></p>
                    </div>
                </div>
            </div>

            <div class="checkout-form">
                <h1>Checkout Form</h1>
                <form id="checkoutForm" action="process_checkout.php" method="POST" novalidate>
                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                    <input type="hidden" name="user_id" value="<?php echo $_SESSION['user_id']; ?>">

                    <div class="form-row">
                        <label for="address">Shipping Address</label>
                        <input type="text" id="address" name="address" required placeholder="Enter your address">
                        <span class="error" id="addressError"></span>
                    </div>

                    <div class="form-row">
                        <label for="phone">Phone Number</label>
                        <input type="text" id="phone" name="phone" required placeholder="Enter your phone number">
                        <span class="error" id="phoneError"></span>
                    </div>

                    <div class="payment-method">
                        <label for="payment_method">Payment Method</label>
                        <select id="payment_method" name="payment_method" required>
                            <option value="" disabled selected>Select Payment Method</option>
                            <option value="cod">Cash on Delivery</option>
                        </select>
                        <span class="error" id="paymentMethodError"></span>
                    </div>

                    <button type="submit" class="btn-submit">Place Order</button>
                </form>
            </div>
        </div>
    </section>

    <footer>
        <?php include('footer.php'); ?>
    </footer>



</body>

</html>