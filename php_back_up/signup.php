<?php
// Include the database configuration file
require_once 'config.php';
session_start(); // Start a session if needed for flash messages

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect and sanitize form inputs
    $firstName = $conn->real_escape_string(trim($_POST['firstName']));
    $middleName = $conn->real_escape_string(trim($_POST['middleName']));
    $lastName = $conn->real_escape_string(trim($_POST['lastName']));
    $index = $conn->real_escape_string(trim($_POST['index']));
    $schoolEmail = $conn->real_escape_string(trim($_POST['schoolEmail']));
    $tel = $conn->real_escape_string(trim($_POST['tel']));
    $username = $conn->real_escape_string(trim($_POST['username']));
    $password = $conn->real_escape_string(trim($_POST['password']));
    $faculty = $conn->real_escape_string(trim($_POST['faculty']));
    $program = $conn->real_escape_string(trim($_POST['program']));

    // Validate required fields
    if (empty($firstName) || empty($lastName) || empty($index) || empty($schoolEmail) || empty($tel) || empty($username) || empty($password) || empty($faculty) || empty($program)) {
        die("All fields are required. Please fill out the form completely.");
    }

    // Validate email
    if (!filter_var($schoolEmail, FILTER_VALIDATE_EMAIL)) {
        die("Invalid email format.");
    }

    // Check if the username, email, or index number already exists
    $checkQuery = "SELECT * FROM users WHERE username='$username' OR school_email='$schoolEmail' OR index_number='$index'";
    $result = $conn->query($checkQuery);

    if ($result->num_rows > 0) {
        die("Error: The username, email, or index number is already registered.");
    }

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert the user into the database
    $sql = "INSERT INTO users (first_name, middle_name, last_name, index_number, school_email, telephone, username, password, faculty, program) 
            VALUES ('$firstName', '$middleName', '$lastName', '$index', '$schoolEmail', '$tel', '$username', '$hashedPassword', '$faculty', '$program')";

    if ($conn->query($sql) === TRUE) {
        // Display success message and redirect after a short delay
        echo "<h2>Registration Successful!</h2>";
        echo "<p>You will be redirected to the login page shortly...</p>";
        echo "<script>
                setTimeout(function(){
                    window.location.href = '/GCTU_LMS/'; // Absolute path to login page
                }, 2000); // Redirect after 2 seconds
              </script>";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Close the database connection
$conn->close();
?>
