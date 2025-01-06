<?php
// Include the database configuration file
require_once 'config.php';
session_start(); // Start the session to access session variables

// Ensure Secure Session Settings
if (ini_get('session.use_only_cookies') == 0) {
    ini_set('session.use_only_cookies', 1); // Force the use of cookies
}

if (ini_get('session.cookie_secure') == 0) {
    ini_set('session.cookie_secure', 1); // Ensure cookies are sent over HTTPS
}

if (ini_get('session.cookie_httponly') == 0) {
    ini_set('session.cookie_httponly', 1); // Prevent JavaScript access to session cookies
}

// Implement Session Timeout (30 minutes)
$session_timeout = 1800; // 30 minutes timeout
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $session_timeout)) {
    // Session expired, destroy session
    session_unset(); // Unset all session variables
    session_destroy(); // Destroy the session
    header("Location: ../../index.html"); // Redirect to login page
    exit();
}

// Update last activity timestamp
$_SESSION['last_activity'] = time();

// Check if the user is logged in
if (isset($_SESSION['user_id']) && isset($_SESSION['session_token'])) {
    // Retrieve user session data
    $user_id = $_SESSION['user_id'];
    $session_token = $_SESSION['session_token'];
    $session_id = session_id(); // Get PHP session ID
    
    // Debugging - Check if session ID and user are correct
    // echo "Session ID: $session_id, User ID: $user_id, Session Token: $session_token";
    
    // SQL query to delete the session from the database using prepared statements
    $sql = "DELETE FROM sessions WHERE session_id = ? AND user_id = ? AND session_token = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('sis', $session_id, $user_id, $session_token); // Bind session_id, user_id, and session_token
    
    if ($stmt->execute()) {
        // Successfully deleted the session from the database

        // Destroy the session data
        session_unset(); // Unset all session variables
        session_destroy(); // Destroy the session

        // Prevent browser caching of the page after logout
        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        header("Pragma: no-cache");
        header("Expires: 0"); // Prevent caching of the page in the future

        // Redirect to the login page (index.html)
        header("Location: ../../index.html"); // Redirect to login page
        exit();
    } else {
        // Error: Failed to delete session from the database
        echo "Error logging out. Please try again later.";
    }

    // Close the database connection
    $stmt->close();
} else {
    // No session found, redirect to login page
    header("Location: ../../index.html");
    exit();
}

// Close the database connection
$conn->close();
?>
