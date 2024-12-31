<?php
// Include the database connection file
include 'connection.php';

// Start the session to check if the user is logged in
session_start();

// Check if the user is logged in
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id']; // Get the logged-in user's ID

    // Fetch cart count for the logged-in user
    $count_stmt = $mysqli->prepare("SELECT COUNT(*) AS total_items FROM user_cart WHERE user_id = ?");
    $count_stmt->bind_param("i", $user_id);
    $count_stmt->execute();
    $count_result = $count_stmt->get_result();
    $cart_count = $count_result->fetch_assoc()['total_items'] ?? 0;

    echo json_encode(['cart_count' => $cart_count]);
} else {
    echo json_encode(['cart_count' => 0]);
}
?>
