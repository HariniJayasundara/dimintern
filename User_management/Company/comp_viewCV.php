<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Function to get CV path for a student
function getCVPath($conn, $studentNumber) {
    // Query the database to get the CV path for the given student number
    $cvPath = "CV unavailable";
    $cvSql = "SELECT cv_path FROM cvs WHERE student_number = '$studentNumber'";
    $cvResult = $conn->query($cvSql);

    // Check if the query executed successfully
    if (!$cvResult) {
        die("Error executing CV query: " . $conn->error);
    }

    // Check if any rows were returned
    if ($cvResult->num_rows > 0) {
        $cvRow = $cvResult->fetch_assoc();
        $cvPath = $cvRow['cv_path'];
    }

    return $cvPath;
}

// Database configuration and connection file
require_once('../../db_connection.php');
session_start();

// Get companyID from the logged-in company's email
$companyEmail = $_SESSION['email']; // Retrieve the logged-in company's email
$companyID = getCompanyID($conn, $companyEmail);

// Function to get the companyID based on the company email
function getCompanyID($conn, $email) {
    $sql = "SELECT companyID FROM company WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['companyID'];
    } else {
        // Return an error value if the email is not found in the company table
        return null;
    }
}

// Fetch assigned students for the given companyID
$sql = "SELECT DISTINCT student_number FROM assigned_preferences WHERE companyID = '$companyID'";
$result = $conn->query($sql);

// Array to store unique student numbers
$uniqueStudents = array();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $studentNumber = $row['student_number'];

        // Check if the student number is already in the array
        if (!in_array($studentNumber, $uniqueStudents)) {
            $uniqueStudents[] = $studentNumber;

            // Get the CV path for the unique student number and display it
            $cvPath = getCVPath($conn, $studentNumber);
            //echo "<p><a href='$cvPath'>CV for $studentNumber</a></p>";
        }
    }
}

// Close the result set
$result->close();
// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>View CVs for Company</title>
    <style>
        /* Add your CSS styles here */
    </style>
</head>
<body>
    <h1>CVs for Company</h1>
    <div id="cvList">
        <?php
        // Re-establish the database connection for querying CVs
        $conn = new mysqli("localhost", "root", "", "dimintern");
        if ($conn->connect_error) {
            die("Database connection failed: " . $conn->connect_error);
        }

        foreach ($uniqueStudents as $studentNumber) {
            $cvPath = getCVPath($conn, $studentNumber);
            echo "<p><a href='$cvPath'>CV for $studentNumber</a></p>";
        }

        // Close the connection
        $conn->close();
        ?>
    </div>

    <script>
        // Add your JavaScript code here
    </script>
</body>
</html>
