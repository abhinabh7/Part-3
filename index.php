<?php
session_start();
include('connection.php');

// Check if the user is logged in
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id']; // Get logged-in user ID
} else {
    $user_id = null; // No user logged in
}

// Fetch cart count if user is logged in
$cart_count = 0;
if ($user_id !== null) {
    $stmt = $mysqli->prepare("SELECT COUNT(*) AS total_items FROM user_cart WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $cart_count = $result->fetch_assoc()['total_items'] ?? 0;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ecommerce</title>
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

    <section class="main-home">
        <div class="main-text">
            <h1>Explore Our</h1>
            <h1>Latest Collection</h1>
            <br>Effortlessly redefine data storage with our </br>
            SSD and HDD solutions, offering cutting-edge <br>performance and transformative benefits.</p>

            <a href="products.php" class="main-btn">shop now <i class='bx bx-right-arrow-alt'></i></a>
        </div>
    </section>

    <!-- Bestseller Products Section -->
    <section class="bestsellers">
        <h2>Bestseller Products</h2>
        <div class="product-grid">
            <?php
            // Query to fetch products
            $sql = "SELECT p.id, p.name, c.name AS category, p.price, p.image
                    FROM products p
                    LEFT JOIN categories c ON p.category_id = c.id
                    LIMIT 8"; // Fetch only top 8 products for the homepage

            $result = $mysqli->query($sql);

            if ($result && $result->num_rows > 0) {
                // Display products
                while ($row = $result->fetch_assoc()) {
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

            $mysqli->close(); // Close connection
            ?>
        </div>
    </section>

    <footer>
        <?php include('footer.php'); ?>
    </footer>


</body>

</html>