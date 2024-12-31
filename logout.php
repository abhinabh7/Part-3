<?php
// Start the session to access the session variables
session_start();

// Unset all session variables
session_unset();

// Destroy the session
session_destroy();

// Redirect the user to the login page (or any page you prefer)
header("Location: index.php");
exit();
?>
