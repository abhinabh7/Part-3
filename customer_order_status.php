<?php
// Start session
session_start();
include 'connection.php'; // Ensure this path is correct

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    $logged_in = false;
} else {
    $logged_in = true;
    $user_id = $_SESSION['user_id']; // Assuming 'user_id' is stored in the session

    // Fetch orders for the logged-in user
    $order_query = $mysqli->prepare("SELECT id, total_price, order_status, order_date, shipping_address, payment_status FROM orders WHERE user_id = ?");
    $order_query->bind_param("i", $user_id);
    $order_query->execute();
    $order_result = $order_query->get_result();
    $orders = $order_result->fetch_all(MYSQLI_ASSOC);
    $order_query->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Status</title>
    <link rel="stylesheet" href="style/home.css">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <style>
        /* CSS styles */
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
        }

        .dashboard-container {
            display: flex;
            height: calc(100vh - 70px);
            margin-top: 70px;
        }

        .sidebar {
            width: 250px;
            background-color: #fff;
            border-right: 1px solid #ddd;
            padding: 20px;
        }

        .sidebar ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .sidebar ul li {
            margin-bottom: 15px;
        }

        .sidebar ul li a {
            text-decoration: none;
            font-size: 16px;
            color: #666;
            display: block;
            padding: 10px 15px;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .sidebar ul li a:hover,
        .sidebar ul li a.active {
            background-color: #e74c3c;
            color: #fff;
        }

        .main-content {
            flex: 1;
            padding: 30px;
        }

        .order {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .order h2 {
            margin: 0 0 20px;
            font-size: 24px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            text-align: left;
            padding: 12px 15px;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f9f9f9;
        }
    </style>
</head>

<body>
    <header>
        <a href="index.php" class="logo"><img src="./image/Matra.png" alt=""></a>
    </header>

    <div class="dashboard-container">
        <div class="sidebar">
            <ul>
                <li><a href="customer_dashboard.php">Profile</a></li>
                <li><a href="index.php">Home</a></li>
                <li><a href="products.php">Shop</a></li>
                <li><a href="customer_order_status.php" class="active">Order</a></li>
                <li><a href="contact.php">Contact</a></li>
                <?php if ($logged_in): ?>
                    <li><a href="logout.php">Logout</a></li>
                <?php else: ?>
                    <li><a href="login.php">Login</a></li>
                <?php endif; ?>
            </ul>
        </div>

        <div class="main-content">
            <div class="order">
                <h2>Your Orders</h2>
                <?php if ($logged_in): ?>
                    <?php if (count($orders) > 0): ?>
                        <table>
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Total Price</th>
                                    <th>Order Status</th>
                                    <th>Order Date</th>
                                    <th>Shipping Address</th>
                                    <th>Payment Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orders as $order): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($order['id']); ?></td>
                                        <td><?php echo htmlspecialchars($order['total_price']); ?></td>
                                        <td><?php echo htmlspecialchars($order['order_status']); ?></td>
                                        <td><?php echo htmlspecialchars($order['order_date']); ?></td>
                                        <td><?php echo htmlspecialchars($order['shipping_address']); ?></td>
                                        <td><?php echo htmlspecialchars($order['payment_status']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p>You have no orders.</p>
                    <?php endif; ?>
                <?php else: ?>
                    <p>Please <a href="login.php">sign in</a> to view your orders.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>

</html>