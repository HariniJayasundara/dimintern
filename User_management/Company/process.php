<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection file
require_once('../../db_connection.php');

// Function to sanitize input data
function sanitize($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Function to validate email format
function validateEmail($email)
{
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Function to hash the password
function hashPassword($password)
{
    return password_hash($password, PASSWORD_DEFAULT);
}

// Retrieve the form data and sanitize
$companyID = sanitize($_POST['companyID']);
$company_name = isset($_POST['company_name']) ? sanitize($_POST['company_name']) : '';
$address = sanitize($_POST['address']);
$contactPerson = sanitize($_POST['contactPerson']);
$designation = sanitize($_POST['designation']);
$email = sanitize($_POST['email']);
$phoneNumber = sanitize($_POST['phoneNumber']);
$specialization = sanitize($_POST['specialization']);
$description = sanitize($_POST['description']);
$password = isset($_POST['password']) ? $_POST['password'] : '';

// Perform additional validation
$errors = array();

if (empty($company_name)) {
    $errors[] = "Company name is required.";
}

if (empty($address)) {
    $errors[] = "Address is required.";
}

if (empty($contactPerson)) {
    $errors[] = "Contact person name is required.";
}

if (empty($designation)) {
    $errors[] = "Contact person designation is required.";
}

if (empty($email)) {
    $errors[] = "Email is required.";
} elseif (!validateEmail($email)) {
    $errors[] = "Invalid email format.";
}

if (empty($phoneNumber)) {
    $errors[] = "Phone number is required.";
} elseif (!preg_match('/^\d{10}$/', $phoneNumber)) {
    $errors[] = "Invalid phone number format. Must have 10 digits.";
}

if (empty($password)) {
    $errors[] = "Password is required.";
}

// If there are validation errors, display them
if (!empty($errors)) {
    foreach ($errors as $error) {
        echo $error . "<br>";
    }
    exit;
}

// Hash the password
$hashedPassword = hashPassword($password);

// Insert the form data into the database
$query = "INSERT INTO company (companyID, company_name, address, contactPerson, designation, email, phoneNumber, specialization, description, password) VALUES ('$companyID', '$company_name', '$address', '$contactPerson', '$designation', '$email', '$phoneNumber', '$specialization', '$description', '$hashedPassword')";
$result = mysqli_query($conn, $query);

if ($result) {
    // Redirect to the landing page
    header("Location: company_dashboard.php");
    // Set the company name in the session
    session_start();
    $_SESSION['company_name'] = $company_name;
    exit(); // exit after the redirect
} else {
    echo 'Registration failed. Error: ' . mysqli_error($conn);
}

mysqli_close($conn);
?>
