<?php
// Include the database connection file
include 'connection.php';

// Start the session to check if the user is logged in
session_start();

// Check if the user is logged in by checking the session variable
if (!isset($_SESSION['user_id'])) {
    // If the user is not logged in, redirect to the login/signup page
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

// Handle adding to cart
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id']; // Get user ID from session
    $product_id = $_POST['product_id']; // Get product ID from POST data
    $quantity = 1; // Hard code quantity to 1

    // Check if the product is already in the cart
    $checkCartStmt = $mysqli->prepare("SELECT id FROM user_cart WHERE user_id = ? AND product_id = ?");
    $checkCartStmt->bind_param("ii", $user_id, $product_id);
    $checkCartStmt->execute();
    $checkResult = $checkCartStmt->get_result();

    if ($checkResult->num_rows > 0) {
        // If the product is already in the cart, no need to update quantity
        // You could optionally update other cart details or show a message
    } else {
        // If the product is not in the cart, insert it with quantity 1
        $insertStmt = $mysqli->prepare("INSERT INTO user_cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
        $insertStmt->bind_param("iii", $user_id, $product_id, $quantity);
        $insertStmt->execute();
    }

    // Redirect to the cart page after adding the product to the cart
    header('Location: cart.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Details</title>
    <link rel="stylesheet" href="style/home.css">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <style>
        .category {
            padding-bottom: 10px;
            margin-bottom: 1.5rem;
        }
    </style>
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
                <span id="cart-count" class="cart-count">0</span>
            </a>
        </div>
    </header>

    <section class="product-page">
        <div class="product-image">
            <div class="image-placeholder">
                <img id="mainImage" src="./image/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
            </div>
            <div class="thumbnail-row">
                <div class="thumbnail">
                    <img src="./image/<?php echo htmlspecialchars($product['image']); ?>" alt="Thumbnail 1" onclick="changeImage('./image/<?php echo htmlspecialchars($product['image']); ?>')">
                </div>
            </div>
        </div>

        <div class="product-details">
            <h1><?php echo htmlspecialchars($product['name']); ?></h1>
            <p class="price">NPR <?php echo number_format($product['price'], 2); ?></p>
            <p class="description"><?php echo htmlspecialchars($product['description']); ?></p>

            <!-- Category Section -->
            <p class="category">Category: <?php echo htmlspecialchars($product['category_name']); ?></p>

            <!-- Add to Cart Section -->
            <div class="action-buttons">
                <form action="product_details.php?product_id=<?php echo $product['id']; ?>" method="POST">
                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                    <!-- Removed quantity input field -->
                    <button type="submit" class="add-to-cart">Add to Cart</button>
                </form>
                <a href="checkout.php?product_id=<?php echo $product['id']; ?>" aria-label="Buy Now">
                    <button class="buy-now">Buy Now</button>
                </a>
            </div>
        </div>
    </section>

    <footer>
        <?php include('footer.php'); ?>
    </footer>

    <script>
        function changeImage(imageSrc) {
            document.getElementById('mainImage').src = imageSrc;
        }
    </script>
</body>

</html>