<?php

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection file
require_once('../../db_connection.php');

// Retrieve the form data
$student_number = $_POST["student_number"];
$name_with_initials = $_POST['name_with_initials'];
$preferred_name = $_POST['preferred_name'];
$email = $_POST['email'];
$phone_number = $_POST['phone_number'];
$specialization = $_POST['specialization'];
$current_gpa = $_POST['current_gpa'];
$qualifications = $_POST['qualifications'];
$special_achievements = $_POST["special_achievements"];
$extra_curricular_activities = $_POST['extra_curricular_activities'];
$linkedin_account = $_POST['linkedin_account'];
$password = $_POST['password'];

// Hash the password
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Validate student number
$student_number = trim($student_number); // Remove leading/trailing whitespace
if (empty($student_number)) {
    die("Error: Student number is required.");
}

// Validate name with initials
$name_with_initials = trim($name_with_initials);
if (empty($name_with_initials)) {
    die("Error: Name with initials is required.");
}

// Validate preferred name
$preferred_name = trim($preferred_name);
if (empty($preferred_name)) {
    die("Error: Preferred name is required.");
}

// Validate email
$email = filter_var($email, FILTER_VALIDATE_EMAIL);
if (!$email) {
    die("Error: Invalid email address.");
}

// Validate phone number
$phone_number = preg_replace('/\D/', '', $phone_number); // Remove non-digit characters
if (strlen($phone_number) !== 10) {
    die("Error: Phone number must have exactly 10 digits.");
}

// Validate specialization
$specialization = trim($specialization);
if (empty($specialization)) {
    die("Error: Specialization is required.");
}

// Validate current GPA
$current_gpa = filter_var($current_gpa, FILTER_VALIDATE_FLOAT);
if ($current_gpa === false) {
    die("Error: Invalid GPA.");
}

// Sanitize qualifications, achievements, extra-curricular activities, and LinkedIn account
$qualifications = trim($qualifications);
$special_achievements = trim($special_achievements);
$extra_curricular_activities = trim($extra_curricular_activities);
$linkedin_account = trim($linkedin_account);

// Prepare the statement for the 'student' table insertion
$stmt = $conn->prepare("INSERT INTO student (student_number, name_with_initials, preferred_name, email, phone_number, specialization, current_gpa, qualifications, special_achievements, extra_curricular_activities, linkedin_account, password)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssssssssss", $student_number, $name_with_initials, $preferred_name, $email, $phone_number, $specialization, $current_gpa, $qualifications, $special_achievements, $extra_curricular_activities, $linkedin_account, $hashedPassword);

if ($stmt->execute()) {
    // Redirect to the student's landing page
    header("Location: student_dashboard.php");
    // Set the preferred name in the session
    session_start();
    $_SESSION['preferred_name'] = $preferred_name;
    exit(); // exit after the redirect
} else {
    $error_message = "Error inserting student data: " . $stmt->error;
}

if (!empty($error_message)) {
    echo "<div class='error-message'>$error_message</div>";
}

// Close the prepared statement
$stmt->close();

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <style type="text/css">
        .error-message {
            color: red;
            font-weight: bold;
        }
    </style>
</head>
<body>
</body>
</html>
