<?php
// Add this line at the top of the PHP files to display errors and warnings
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection file
require_once('../../db_connection.php');

// Get the filter value from the query parameters
$filterValue = $_GET['filterValue'];

// Query to retrieve all matched preferences with human-readable names
$query = "SELECT DISTINCT
            mapped_preference.preference_id,
            preferences.preference_name,
            mapped_preference.companyID,
            company.company_name,
            mapped_preference.student_number,
            student.preferred_name AS student_name
        FROM mapped_preference 
        LEFT JOIN preferences ON mapped_preference.preference_id = preferences.preference_id 
        LEFT JOIN company ON mapped_preference.companyID = company.companyID 
        LEFT JOIN student ON mapped_preference.student_number = student.student_number";

// If a filter value is provided and it is not empty, add the filter conditions to the query
if (!empty($filterValue)) {
    $query .= " WHERE mapped_preference.preference_id = '$filterValue' OR mapped_preference.companyID = '$filterValue' OR mapped_preference.student_number = '$filterValue'";
}

$result = mysqli_query($conn, $query);

if ($result) {
    // Create an array to store the matched preferences
    $matchedPreferences = array();

    // Fetch rows from the result set
    while ($row = mysqli_fetch_assoc($result)) {
        // Add each matched preference to the array
        $matchedPreferences[] = $row;
    }
//
   // If no matches were found with the filter, retrieve all matches
    if (empty($matchedPreferences) && !empty($filterValue)) {
        $queryAll = "SELECT DISTINCT
            mapped_preference.preference_id,
            preferences.preference_name,
            mapped_preference.companyID,
            company.company_name,
            mapped_preference.student_number,
            student.preferred_name AS student_name
        FROM mapped_preference 
        LEFT JOIN preferences ON mapped_preference.preference_id = preferences.preference_id 
        LEFT JOIN company ON mapped_preference.companyID = company.companyID 
        LEFT JOIN student ON mapped_preference.student_number = student.student_number";

        $resultAll = mysqli_query($conn, $queryAll);

        if ($resultAll) {
            // Fetch all rows from the result set
            while ($row = mysqli_fetch_assoc($resultAll)) {
                // Add each matched preference to the array
                $matchedPreferences[] = $row;
            }
        }
    }
 
//
    // Send the matched preferences as JSON response
    header('Content-Type: application/json');
    echo json_encode($matchedPreferences);
} else {
    // Failed to retrieve matched preferences
    http_response_code(500); // Set an appropriate HTTP status code
    echo "Failed to retrieve matched preferences.";
}

// Close the database connection
mysqli_close($conn);
?>
