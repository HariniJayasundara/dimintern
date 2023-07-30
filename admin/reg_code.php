<?php
session_start();
//Database connection
require_once('../db_connection.php');

$first_name = $_POST['first_name'];
$last_name = $_POST['last_name'];
$staff_id = $_POST['staff_id'];
$email = $_POST['email'];
$contact_number = $_POST['contact_number'];
$password = $_POST['password'];
$confirmPassword = $_POST['confirmPassword'];

// Validate if passwords match
if ($password === $confirmPassword) {
    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Create a prepared statement to prevent SQL injection
    $stmt = $conn->prepare("INSERT INTO admin (first_name, last_name, staff_id, email, contact_number, password) VALUES (?, ?, ?, ?, ?, ?)");

    $stmt->bind_param("ssssss", $first_name, $last_name, $staff_id, $email, $contact_number, $hashedPassword);

    // Execute the statement
    if ($stmt->execute()) {
        // Insertion successful
        $_SESSION['status'] = "Admin Profile Added";
        echo '<script>alert("Registration Successful"); window.location.replace("register.php");</script>';
        exit;

    } else {
        // Insertion failed
        $_SESSION['status'] = "Admin Profile Not Added";
        echo '<script>alert("Registration unsuccessful. Try again"); window.location.replace("register.php");</script>';
        exit;
    }
} else {
        // Passwords different
        $_SESSION['status'] = "Admin Profile Not Added";
        echo '<script>alert("Passwords do not match"); window.location.replace("register.php");</script>';
        exit;
}

// Close the prepared statement and database connection
$stmt->close();
$conn->close();
?>