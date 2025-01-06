<?php
// Include the database configuration file
require_once 'config.php';
session_start(); // Start a new session

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect and sanitize form inputs
    $username = $conn->real_escape_string(trim($_POST['username']));
    $password = $conn->real_escape_string(trim($_POST['password']));

    // Validate the inputs
    if (empty($username) || empty($password)) {
        die("Please fill in both fields.");
    }

    // Query to find the user by username
    $sql = "SELECT * FROM users WHERE username='$username'";
    $result = $conn->query($sql);

    // Check if the user exists
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        // Verify the password using password_hash()
        if (password_verify($password, $user['password'])) {
            // Password is correct, create a session for the user
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];

            // Generate a session token and store it in the sessions table
            $session_token = bin2hex(random_bytes(32)); // Generate a random session token
            $user_id = $user['user_id'];

            // Insert session details into the sessions table
            $session_sql = "INSERT INTO sessions (user_id, session_token) VALUES ('$user_id', '$session_token')";
            if ($conn->query($session_sql) === TRUE) {
                // Redirect to the main page after successful login
                header("Location: /other_sites/main.html"); // Absolute path to the main page
                exit(); // Ensure no further code is executed after redirect
            } else {
                die("Error storing session: " . $conn->error);
            }
        } else {
            // Incorrect password
            die("Incorrect username or password.");
        }
    } else {
        // User does not exist
        die("No such user found.");
    }
}

// Close the database connection
$conn->close();
?>
