<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include the database connection file
require_once 'db_connection.php';

// Generate a default password
function generateDefaultPassword() {
    $length = 8;
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $password = '';
    $characterCount = strlen($characters) - 1;

    for ($i = 0; $i < $length; $i++) {
        $password .= $characters[mt_rand(0, $characterCount)];
    }

    return $password;
}

// Hash the password
function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

// Store user details in the database
function storeUserDetails($email, $hashedPassword, $otherDetails) {
    global $conn;

    // Prepare and execute the SQL statement
    $stmt = $conn->prepare("INSERT INTO users (email, password, other_details) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $email, $hashedPassword, $otherDetails);
    $stmt->execute();

    // Close the statement
    $stmt->close();
}

// Send email to the user with their password
function sendPasswordEmail($email, $password) {
    $to = $email;
    $subject = "Your Account Information";
    $message = "Thank you for creating an account. Your default password is: " . $password;
    $headers = "From: your-email@example.com"; // Replace with your email address or a valid sender address

    // Send email
    if (mail($to, $subject, $message, $headers)) {
        echo "Account created successfully. An email with your default password has been sent.";
    } else {
        echo "Failed to send the email. Please contact support for assistance.";
    }
}

// Example usage for creating an account
$email = $_POST['email'];
$otherDetails = $_POST['other_details'];

// Generate a default password
$defaultPassword = generateDefaultPassword();

// Hash the default password
$hashedPassword = hashPassword($defaultPassword);

// Store user details in the database
storeUserDetails($email, $hashedPassword, $otherDetails);

// Send password email to the user
sendPasswordEmail($email, $defaultPassword);
?>
