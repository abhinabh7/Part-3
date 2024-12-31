    <?php
    // Include the database connection file
    include 'connection.php';

    // Start the session to check if the user is logged in
    session_start();

    // Check if the user is logged in by checking the session variable
    if (!isset($_SESSION['user_id'])) {
        // If the user is not logged in, redirect to the login page
        header('Location: login.php');
        exit;
    }

    $user_id = $_SESSION['user_id']; // Get the logged-in user's ID

    // Fetch cart items for the logged-in user
    $stmt = $mysqli->prepare("
        SELECT user_cart.*, products.name AS product_name, products.price AS product_price, products.image AS product_image 
        FROM user_cart 
        INNER JOIN products ON user_cart.product_id = products.id 
        WHERE user_cart.user_id = ?
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Fetch cart count for the logged-in user (for AJAX call)
    if (isset($_GET['action']) && $_GET['action'] === 'cart_count') {
        // Query to count the number of distinct products in the cart
        $count_stmt = $mysqli->prepare("SELECT COUNT(*) AS total_items FROM user_cart WHERE user_id = ?");
        $count_stmt->bind_param("i", $user_id);
        $count_stmt->execute();
        $count_result = $count_stmt->get_result();
        $cart_count = $count_result->fetch_assoc()['total_items'] ?? 0;

        echo json_encode(['cart_count' => $cart_count]);
        exit;
    }

    // Handle cart item update or removal via POST request (AJAX call)
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $cart_id = $_POST['cart_id'];

        // Handle removal of cart item
        if (isset($_POST['action']) && $_POST['action'] == 'remove') {
            $remove_stmt = $mysqli->prepare("DELETE FROM user_cart WHERE id = ?");
            $remove_stmt->bind_param("i", $cart_id);
            $remove_stmt->execute();

            echo json_encode(['success' => $remove_stmt->affected_rows > 0]);
            exit;
        }

        // Handle quantity update for cart item
        if (isset($_POST['quantity'])) {
            $quantity = $_POST['quantity'];
            $update_stmt = $mysqli->prepare("UPDATE user_cart SET quantity = ? WHERE id = ?");
            $update_stmt->bind_param("ii", $quantity, $cart_id);
            $update_stmt->execute();

            echo json_encode(['success' => $update_stmt->affected_rows > 0]);
            exit;
        }
    }

    // Check if the cart is empty
    if ($result->num_rows == 0) {
        echo "<p>Your cart is empty.</p>";
        exit;
    }
    ?>

    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Shopping Cart</title>
        <link rel="stylesheet" href="style/home.css">
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
                    <span id="cart-count" class="cart-count">0</span>
                </a>
            </div>
        </header>

        <main class="cart-container">
            <h1>Your Cart</h1>
            <div class="cart">
                <div class="cart-items">
                    <?php while ($cartItem = $result->fetch_assoc()): ?>
                        <div class="item" data-cart-id="<?php echo $cartItem['id']; ?>">
                            <div class="item-image">
                                <img src="./image/<?php echo htmlspecialchars($cartItem['product_image']); ?>" alt="<?php echo htmlspecialchars($cartItem['product_name']); ?>">
                            </div>
                            <div class="item-info">
                                <h2><?php echo htmlspecialchars($cartItem['product_name']); ?></h2>
                                <div class="quantity-selector">
                                    <button class="decrease">-</button>
                                    <input type="text" class="quantity" value="<?php echo $cartItem['quantity']; ?>" readonly>
                                    <button class="increase">+</button>
                                </div>
                                <p class="price" data-price="<?php echo $cartItem['product_price']; ?>">NRP <?php echo number_format($cartItem['product_price'], 2); ?></p>
                                <a href="#" class="remove">Remove</a>
                                <a href="checkout.php?product_id=<?php echo $cartItem['product_id']; ?>" class="remove">Checkout</a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </main>

        <footer>
            <!-- Footer Content -->
        </footer>

        <script>
            document.addEventListener('DOMContentLoaded', () => {
                function updateCartCount() {
                    fetch('cart.php?action=cart_count')
                        .then(response => response.json())
                        .then(data => {
                            const cartCountEl = document.getElementById('cart-count');
                            cartCountEl.textContent = data.cart_count || 0;
                        })
                        .catch(error => console.error('Error fetching cart count:', error));
                }

                updateCartCount();

                const items = document.querySelectorAll('.item');
                items.forEach(item => {
                    const cartId = item.getAttribute('data-cart-id');
                    const increaseBtn = item.querySelector('.increase');
                    const decreaseBtn = item.querySelector('.decrease');
                    const quantityEl = item.querySelector('.quantity');

                    increaseBtn.addEventListener('click', () => {
                        let quantity = parseInt(quantityEl.value) + 1;
                        quantityEl.value = quantity;
                        updateCart(cartId, quantity);
                    });

                    decreaseBtn.addEventListener('click', () => {
                        let quantity = parseInt(quantityEl.value);
                        if (quantity > 1) {
                            quantity--;
                            quantityEl.value = quantity;
                            updateCart(cartId, quantity);
                        }
                    });

                    item.querySelector('.remove').addEventListener('click', (e) => {
                        e.preventDefault();
                        if (confirm('Are you sure?')) {
                            item.remove();
                            removeCartItem(cartId);
                            updateCartCount();
                        }
                    });
                });

                function updateCart(cartId, quantity) {
                    fetch('cart.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: `cart_id=${cartId}&quantity=${quantity}`
                    });
                }

                function removeCartItem(cartId) {
                    fetch('cart.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: `cart_id=${cartId}&action=remove`
                    });
                }
            });
        </script>
    </body>

    </html>