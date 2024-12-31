<?php
// Include the database connection file
include 'connection.php';

// Check if the user is an admin
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

// Prepare statement for getting orders
$stmt = $mysqli->prepare("SELECT order_id, total_price, order_status FROM orders WHERE user_id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

// Display orders
echo "<h1>Manage Order Status</h1>";

while ($order = $result->fetch_assoc()) {
    echo "<p>Order ID: " . $order['order_id'] . " | Total Price: " . $order['total_price'] . " | Status: " . $order['order_status'] . "</p>";
}

// Handle form submission for updating order status
if (isset($_POST['order_id']) && isset($_POST['new_status'])) {
    $order_id = $_POST['order_id'];
    $new_status = $_POST['new_status'];

    // Validate status
    $valid_statuses = ['Pending', 'Processing', 'Shipped', 'Delivered', 'Cancelled'];
    if (in_array($new_status, $valid_statuses)) {
        // Update the order status in the database
        $stmt = $mysqli->prepare("UPDATE orders SET order_status = ? WHERE order_id = ?");
        $stmt->bind_param("si", $new_status, $order_id);

        if ($stmt->execute()) {
            echo "<p>Order status updated successfully!</p>";
        } else {
            echo "<p>There was an error updating the order status.</p>";
        }
    } else {
        echo "<p>Invalid status.</p>";
    }
}
?>

<h2>Update Order Status</h2>
<form method="POST" action="">
    <label for="order_id">Order ID:</label>
    <input type="number" name="order_id" id="order_id" required><br><br>

    <label for="new_status">New Status:</label>
    <select name="new_status" id="new_status">
        <option value="Pending">Pending</option>
        <option value="Processing">Processing</option>
        <option value="Shipped">Shipped</option>
        <option value="Delivered">Delivered</option>
        <option value="Cancelled">Cancelled</option>
    </select><br><br>

    <button type="submit">Update Status</button>
</form>
