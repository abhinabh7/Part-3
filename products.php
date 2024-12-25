<?php
// Include the database connection
include('connection.php');

// Query to get all products with category names
$sql = "SELECT p.id, p.name, c.name AS category, p.price, p.image
        FROM products p
        LEFT JOIN categories c ON p.category_id = c.id"; // Assuming 'categories' table exists
$result = $mysqli->query($sql); // Use the $mysqli object from connection.php
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products</title>
    <link rel="stylesheet" href="style/home.css">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
</head>

<body>
    <header>
        <a href="index.html" class="logo"><img src="./image/Matra.png" alt="Logo"></a>
        <ul class="navmenu">
            <li><a href="./index.html">home</a></li>
            <li><a href="about.html">About</a></li>
            <li><a href="product.php">products</a></li>
            <li><a href="blog.html">Blog</a></li>
            <li><a href="contact.html">contact</a></li>
        </ul>
        <div class="nav_icon">
            <a href="customer_dashboard.php"><i class='bx bx-user'></i></a>
            <a href="cart.php"><i class='bx bx-cart'></i></a>
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
                    echo '<div class="product-card">
                            <img class="product-image" src="image/' . htmlspecialchars($row["image"]) . '" alt="' . htmlspecialchars($row["name"]) . '">
                            <p class="product-name">' . htmlspecialchars($row["name"]) . '</p>
                            <p class="product-type">' . htmlspecialchars($row["category"]) . '</p>
                            <div class="product-price-cart">
                                <p class="product-price">NPR ' . number_format($row["price"], 2) . '</p>
                                <a href="product_details.php?product_id=' . $row["id"] . '" class="cart-button">
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

    <footer>
        <div class="top-section">
            <div class="footer-logo">
                <img src="image/Matra.png" alt="Logo">
            </div>
            <div class="social-icons">
                <a href="#" target="_blank"><i class="bx bxl-facebook"></i></a>
                <a href="#" target="_blank"><i class="bx bxl-twitter"></i></a>
                <a href="#" target="_blank"><i class="bx bxl-instagram"></i></a>
            </div>
        </div>
        <hr class="horizontal-line">
        <div class="footer-sections">
            <div class="footer-section">
                <h3>Contact Us</h3>
                <p><i class="bx bx-map icon"></i> Golmadhi -07-Bhaktapur, 8848, Nepal</p>
                <p><i class="bx bx-phone icon"></i> +9779864737132</p>
                <p><i class="bx bx-envelope icon"></i> matradude@gmail.com</p>
            </div>
            <div class="footer-section">
                <h3>My Account</h3>
                <a href="customer_dashboard.php">My Account</a>
                <a href="cart.php">View Cart</a>
                <a href="blog.html">Blog</a>
                <a href="product.php">Bestsellers Products</a>
            </div>
            <div class="footer-section">
                <h3>Opening Time</h3>
                <p>Sun - Tue: 9AM - 9PM</p>
                <p>Wed - Thu: 9AM - 9PM</p>
                <p>Fri: 8AM - 10PM</p>
                <p>Sat: Closed</p>
            </div>
        </div>
    </footer>

    <div class="footer-bottom">
        <p>© 2024 Matradude. All Rights Reserved</p>
    </div>

</body>

</html>
