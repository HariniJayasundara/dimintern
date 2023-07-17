<?php
// Database connection file
require_once('../db_connection.php');

// Query to delete all existing records from the mapped_preference table
$query = "DELETE FROM mapped_preference";
$result = mysqli_query($conn, $query);

if ($result) {
    // Preferences reset successfully
    http_response_code(200); // Set an appropriate HTTP status code
    echo "Preferences reset successfully.";
} else {
    // Failed to reset preferences
    http_response_code(500); // Set an appropriate HTTP status code
    echo "Failed to reset preferences.";
}

// Close the database connection
mysqli_close($conn);
?>
