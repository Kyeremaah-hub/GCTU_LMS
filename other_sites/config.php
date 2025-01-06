<?php
// Database Configuration
define('DB_HOST', 'localhost'); // Database host (e.g., localhost)
define('DB_USER', 'root');     // Database username
define('DB_PASS', 'HACK_FEVER1@WORK.COm111');         // Database password
define('DB_NAME', 'gctu_lms'); // Database name

// Establishing a Connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check Connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
?>
