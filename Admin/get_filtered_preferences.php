<?php
// Add this line at the top of the PHP files to display errors and warnings
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection file
require_once('../db_connection.php');

// Get the filter value from the query parameters
$filterValue = $_GET['filterValue'];

// Query to retrieve matched preferences from the mapped_preference table based on the filter value
$query = "SELECT DISTINCT preference_id, companyID, student_number FROM mapped_preference";

// If a filter value is provided and it is not empty, add the filter conditions to the query
if (!empty($filterValue)) {
    $query .= " WHERE preference_id = '$filterValue' OR companyID = '$filterValue' OR student_number = '$filterValue'";
}

$result = mysqli_query($conn, $query);

if ($result) {
    // Create an array to store the filtered matched preferences
    $matchedPreferences = array();

    // Fetch rows from the result set
    while ($row = mysqli_fetch_assoc($result)) {
        // Add each matched preference to the array
        $matchedPreferences[] = $row;
    }

    // Send the filtered matched preferences as JSON response
    header('Content-Type: application/json');
    echo json_encode($matchedPreferences);
} else {
    // Failed to retrieve filtered matched preferences
    http_response_code(500); // Set an appropriate HTTP status code
    echo "Failed to retrieve filtered matched preferences.";
}

// Close the database connection
mysqli_close($conn);
?>
