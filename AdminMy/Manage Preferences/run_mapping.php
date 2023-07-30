<?php
// Add this line at the top of the PHP file to display errors and warnings
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection file
require_once('../../db_connection.php');

// Include the map_preferences.php script
require_once('map_preferences.php');

// Call the function to match preferences and populate the database
matchAndMapPreferences($conn);

// Close the database connection
$conn->close();
?>