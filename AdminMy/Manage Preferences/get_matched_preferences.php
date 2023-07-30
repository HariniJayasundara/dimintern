<?php
// Add this line at the top of the PHP files to display errors and warnings
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection file
require_once('../../db_connection.php');

// Get the filter value from the query parameters or set it as an empty string if not provided
$filterValue = isset($_GET['filterValue']) ? $_GET['filterValue'] : '';

// Add the following function to retrieve the list of student numbers that have already been selected
$selectedStudentNumbers = getSelectedStudentNumbers($conn);

function getSelectedStudentNumbers($conn) {
    $sql = "SELECT DISTINCT student_number FROM assigned_preferences WHERE selected_companyID IS NOT NULL";
    $result = $conn->query($sql);

    $selectedStudentNumbers = array();
    while ($row = $result->fetch_assoc()) {
        $selectedStudentNumbers[] = $row['student_number'];
    }

    $result->free_result();

    return $selectedStudentNumbers;
}

// Query to retrieve all matched preferences with human-readable names for unassigned students
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
        LEFT JOIN student ON mapped_preference.student_number = student.student_number
        LEFT JOIN assigned_preferences ap ON mapped_preference.student_number = ap.student_number
        WHERE ap.selected_companyID IS NULL"; // Only retrieve preferences for unassigned students

// If a filter value is provided and it is not empty, add the filter conditions to the query
if (!empty($filterValue)) {
    $query .= " AND (mapped_preference.preference_id = '$filterValue' OR mapped_preference.companyID = '$filterValue' OR mapped_preference.student_number = '$filterValue')";
}

$result = mysqli_query($conn, $query);

if ($result) {
    // Create an array to store the matched preferences
    $matchedPreferences = array();

    // Fetch rows from the result set
    while ($row = mysqli_fetch_assoc($result)) {
        // Check if the student number is in the list of selected student numbers
        if (!in_array($row['student_number'], $selectedStudentNumbers)) {
            // Add each matched preference to the array
            $matchedPreferences[] = $row;
        }
    }

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
        LEFT JOIN student ON mapped_preference.student_number = student.student_number
        LEFT JOIN assigned_preferences ap ON mapped_preference.student_number = ap.student_number
        WHERE ap.selected_companyID IS NULL"; // Only retrieve preferences for unassigned students

        $resultAll = mysqli_query($conn, $queryAll);

        if ($resultAll) {
            // Fetch all rows from the result set
            while ($row = mysqli_fetch_assoc($resultAll)) {
                // Check if the student number is in the list of selected student numbers
                if (!in_array($row['student_number'], $selectedStudentNumbers)) {
                    // Add each matched preference to the array
                    $matchedPreferences[] = $row;
                }
            }
        }
    }

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
