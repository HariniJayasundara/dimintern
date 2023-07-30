<?php
session_start();
// Include the database connection file
require_once 'db_connection.php';

// Retrieve the form data
$role = $_POST['role'];
$email = $_POST['email'];
$password = $_POST['password'];

// Prepare and execute the SQL query to retrieve the email and hashed password based on the role
if ($role === 'company') {
    $table = 'company';
} elseif ($role === 'student') {
    $table = 'student';
} elseif ($role === 'admin') {
    $table = 'admin';
} 

// Prepare the SQL statement
$stmt = $conn->prepare("SELECT email, password FROM $table WHERE email = ? LIMIT 1");

// Bind the email parameter and execute the query
$stmt->bind_param("s", $email);
$stmt->execute();

// Store the result
$stmt->store_result();

// Check if a row is returned
if ($stmt->num_rows == 1) {
    // Bind the result to variables
    $stmt->bind_result($dbEmail, $dbPassword);
    $stmt->fetch();

    // Verify the password
    if (password_verify($password, $dbPassword)) {
        // Password is correct, store the email and role in the session
        $_SESSION['email'] = $dbEmail;
        $_SESSION['role'] = $role; // Set the user's role

        // Redirect user to their landing page
        switch ($role) {
            case 'company':
                header("Location: User_management/Company/company_dashboard.php");
                break;
            case 'student':
                header("Location: User_management/Student/student_dashboard.php");
                break;
            case 'admin':
                header("Location: AdminMy/index.php");
                break;
            default:
                // Invalid role
                break;
        }
    } else {
        // Incorrect password, handle accordingly (e.g., show an error message)
        echo "Incorrect password.";
    }
} else {
    // User does not exist, handle accordingly (e.g., show an error message)
    echo "User not found.";
}

// Close the statement and database connection
$stmt->close();
$conn->close();
?>