<?php
// Include the database configuration file
require_once 'config.php';
session_start(); // Start a new session

$error_message = ""; // Initialize an error message variable

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect and sanitize form inputs
    $studentID = trim($_POST['studentID']);
    $password = trim($_POST['password']);

    // Validate the inputs
    if (empty($studentID) || empty($password)) {
        $error_message = "Please fill in both fields.";
    } else {
        // Use prepared statements to prevent SQL injection
        $stmt = $conn->prepare("SELECT * FROM users WHERE index_number = ?");
        $stmt->bind_param("s", $studentID);  // 's' for string
        $stmt->execute();
        $result = $stmt->get_result();

        // Check if the user exists
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            
            // Verify the password using password_verify()
            if (password_verify($password, $user['password'])) {
                // Password is correct, create a session for the user
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['studentID'] = $user['index_number'];

                // Regenerate session ID to prevent session fixation
                session_regenerate_id(true);

                // Generate a session token and store it in the sessions table securely
                $session_token = bin2hex(random_bytes(32)); // Generate a random session token
                $user_id = $user['user_id'];

                // Use prepared statements for session storage to prevent SQL injection
                $session_sql = "INSERT INTO sessions (user_id, session_token) VALUES (?, ?)";
                $session_stmt = $conn->prepare($session_sql);
                $session_stmt->bind_param("is", $user_id, $session_token);
                
                if ($session_stmt->execute()) {
                    // Display login success message instead of redirecting for now
                    //echo "<h2>Login Successful!</h2>";
                   // echo "<p>You are now logged in.</p>";
                    
                    // I will Uncomment the following line once the main page is ready
                    header("Location: /GCTU_LMS/php_back_up/other_sites/main.html"); // Redirect to the main page
                    exit(); // Ensure no further code is executed
                } else {
                    $error_message = "Error storing session. Please try again.";
                }
            } else {
                // Incorrect password
                $error_message = "Incorrect Student ID or Password.";
            }
        } else {
            // User does not exist
            $error_message = "No user found with the provided Student ID.";
        }
    }
}

// Close the database connection
$conn->close();
?>

<!-- HTML for displaying the form and error message -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link rel="stylesheet" href="login_styles.css">
</head>
<body>
    <div class="login-container">
        <div class="form-wrapper">
            <h1>Welcome to GCTU Library</h1>
            <?php if (!empty($error_message)): ?>
                <div class="error-message" style="color: red; margin-bottom: 15px;">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>
            <form action="login.php" method="POST">
                <label for="studentID">Student ID</label>
                <input type="text" id="studentID" name="studentID" placeholder="Enter your Student ID" required>
                
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your Password" required>

                <button type="submit">Login</button>
            </form>
            <p>Don't have an account? <a href="../GCTU_LMS/other_sites/signup.html">Sign up here</a></p>
        </div>
    </div>
</body>
</html>
