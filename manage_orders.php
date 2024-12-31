<?php
// Include the database connection file
include 'connection.php';

// Start session
session_start();

// Check if the user is an admin
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    // Redirect non-admin users to an access denied page
    header('Location: access_denied.php');
    exit;
}

// Handle form submission to update order status and payment status
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = isset($_POST['order_id']) ? (int)$_POST['order_id'] : 0;
    $order_status = isset($_POST['order_status']) ? trim($_POST['order_status']) : '';
    $payment_status = isset($_POST['payment_status']) ? trim($_POST['payment_status']) : '';

    // Validate order status input
    if ($order_id > 0 && in_array($order_status, ['Pending', 'Processing', 'Shipped', 'Delivered', 'Cancelled'])) {
        $stmt = $mysqli->prepare("UPDATE orders SET order_status = ? WHERE id = ?");
        $stmt->bind_param("si", $order_status, $order_id);

        if ($stmt->execute()) {
            $message = "Order status updated successfully!";
        } else {
            $message = "Failed to update order status.";
        }
    }

    // Validate payment status input (Cash on Delivery)
    if ($order_id > 0 && in_array($payment_status, ['Unpaid', 'Paid'])) {
        $stmt = $mysqli->prepare("UPDATE orders SET payment_status = ? WHERE id = ?");
        $stmt->bind_param("si", $payment_status, $order_id);

        if ($stmt->execute()) {
            $message = "Payment status updated successfully!";
        } else {
            $message = "Failed to update payment status.";
        }
    }
}

// Fetch orders from the database
$stmt = $mysqli->prepare("SELECT o.id, o.user_id, p.name AS product_name, o.total_price, o.shipping_address, o.payment_status, o.order_status 
                          FROM orders o 
                          JOIN products p ON o.product_id = p.id");
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders</title>
    <link rel="stylesheet" href="style/home.css">

</head>

<body>
    <header>
        <a href="index.html" class="logo"><img src="./image/Matra.png" alt="Logo"></a>
    </header>

    <div class="dashboard-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <ul>
                <li><a href="admin_dashboard.php">Customer</a></li>
                <li><a href="product_list.php">Product</a></li>
                <li><a href="manage_orders.php" class="active">Manage Orders</a></li> <!-- Link to Manage Orders -->
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <h1>Order</h1>
            <div class="customers">
                <h2>Manage Orders</h2>
                <?php if (isset($message)) { ?>
                    <p style="color: green;"><?= htmlspecialchars($message) ?></p>
                <?php } ?>

                <table>
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>User ID</th>
                            <th>Product</th>
                            <th>Total Price</th>
                            <th>Shipping Address</th>
                            <th>Payment Status</th>
                            <th>Order Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($order = $result->fetch_assoc()) { ?>
                            <tr>
                                <td><?= htmlspecialchars($order['id']) ?></td>
                                <td><?= htmlspecialchars($order['user_id']) ?></td>
                                <td><?= htmlspecialchars($order['product_name']) ?></td>
                                <td><?= htmlspecialchars($order['total_price']) ?></td>
                                <td><?= htmlspecialchars($order['shipping_address']) ?></td>
                                <td><?= htmlspecialchars($order['payment_status']) ?></td>
                                <td><?= htmlspecialchars($order['order_status']) ?></td>
                                <td>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="order_id" value="<?= $order['id'] ?>">

                                        <div class="form-group">
                                            <!-- Order Status Dropdown -->
                                            <select name="order_status" required>
                                                <option value="Pending" <?= $order['order_status'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
                                                <option value="Processing" <?= $order['order_status'] === 'Processing' ? 'selected' : '' ?>>Processing</option>
                                                <option value="Shipped" <?= $order['order_status'] === 'Shipped' ? 'selected' : '' ?>>Shipped</option>
                                                <option value="Delivered" <?= $order['order_status'] === 'Delivered' ? 'selected' : '' ?>>Delivered</option>
                                                <option value="Cancelled" <?= $order['order_status'] === 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                            </select>

                                            <!-- Payment Status Dropdown -->
                                            <select name="payment_status" required>
                                                <option value="Unpaid" <?= $order['payment_status'] === 'Unpaid' ? 'selected' : '' ?>>Unpaid</option>
                                                <option value="Paid" <?= $order['payment_status'] === 'Paid' ? 'selected' : '' ?>>Paid</option>
                                            </select>

                                            <!-- Update Button -->
                                            <button type="submit" class="edit-btn">Update</button>
                                        </div>
                                    </form>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>

</html>