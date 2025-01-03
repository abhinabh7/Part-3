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

// Query to get all products with category names
$sql = "SELECT p.id, p.name, c.name AS category, p.price, p.image
        FROM products p
        LEFT JOIN categories c ON p.category_id = c.id"; // Assuming 'categories' table exists

$result = $mysqli->query($sql);

// Check for query errors
if (!$result) {
    die("Error fetching products: " . $mysqli->error);
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products</title>
    <link rel="stylesheet" href="style/home.css">
    <script src="js/cart_count.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
</head>

<body>
    <!-- Header with Navigation and Cart Count -->
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

    <!-- Products Section -->
    <section class="bestsellers">
        <h2>Our Products</h2>
        <div class="product-grid">
            <?php
            // Check if there are any products
            if ($result->num_rows > 0) {
                // Loop through all products and display them
                while ($row = $result->fetch_assoc()) {
                    // Escape and validate output
                    $productName = htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8');
                    $productCategory = htmlspecialchars($row['category'], ENT_QUOTES, 'UTF-8');
                    $productImage = htmlspecialchars($row['image'], ENT_QUOTES, 'UTF-8');
                    $productPrice = number_format($row['price'], 2);
                    $productId = intval($row['id']); // Ensure ID is an integer

                    echo '<div class="product-card">
                            <img class="product-image" src="image/' . $productImage . '" alt="' . $productName . '">
                            <p class="product-name">' . $productName . '</p>
                            <p class="product-type">' . $productCategory . '</p>
                            <div class="product-price-cart">
                                <p class="product-price">NPR ' . $productPrice . '</p>
                                <a href="product_details.php?product_id=' . $productId . '" class="cart-button">
                                    <i class="bx bx-cart"></i>
                                </a>
                            </div>
                          </div>';
                }
            } else {
                echo "<p>No products available</p>";
            }
            $mysqli->close(); // Close the MySQL connection
            ?>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <?php include('footer.php'); ?>
    </footer>
</body>

</html>