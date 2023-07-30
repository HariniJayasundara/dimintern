<?php
// Add this line at the top of the PHP files to display errors and warnings
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection file
require_once('../../db_connection.php');

// Get the data from the POST request
$preferenceID = isset($_POST['preference_id']) ? $_POST['preference_id'] : '';
$companyID = isset($_POST['companyID']) ? $_POST['companyID'] : '';
$studentNumber = isset($_POST['student_number']) ? $_POST['student_number'] : '';

if ($preferenceID && $companyID && $studentNumber) {
    // The data is available, proceed with the assignment logic

// Check if the selected row is already assigned
$queryCheckAssigned = "SELECT * FROM assigned_preferences WHERE preference_id = '$preferenceID' AND companyID = '$companyID' AND student_number = '$studentNumber'";
$resultCheckAssigned = mysqli_query($conn, $queryCheckAssigned);

if (mysqli_num_rows($resultCheckAssigned) > 0) {
    // Row already assigned, do not proceed
    http_response_code(409); // Conflict - Row already assigned
    echo "Row is already assigned.";
} else {
    // Assign the selected row to the assigned_preferences table
    $queryAssign = "INSERT INTO assigned_preferences (preference_id, companyID, student_number, date_assigned) VALUES ('$preferenceID', '$companyID', '$studentNumber', NOW())";

    $resultAssign = mysqli_query($conn, $queryAssign);

    if ($resultAssign) {
        http_response_code(200); // Success
        echo "Row assigned successfully.";
    } else {
        http_response_code(500); // Server Error
        echo "Failed to assign the row.";
    }
}

} else {
    // One or more parameters are missing, display an error message
    http_response_code(400); // Bad Request
    echo "Missing parameters for row assignment.";
}

// Close the database connection
mysqli_close($conn);
?>