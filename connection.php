<?php
$host = "localhost";
$dbname = "sheesh";
$username = "root";
$password = "";

// Create a new mysqli connection
$mysqli = new mysqli($host, $username, $password, $dbname);

// Check for connection errors
if ($mysqli->connect_errno) {
    die("Connection error: " . $mysqli->connect_error);
}

?>


