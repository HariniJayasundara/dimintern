<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection file
require_once('../../db_connection.php');

// Retrieve the highest company ID from the database
$query = "SELECT MAX(companyID) as max_id FROM company";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);

// Increment the highest ID and format it as CXXX (e.g., C001, C002, etc.)
$nextCompanyID = 'C' . str_pad((intval(substr($row['max_id'], 1)) + 1), 3, '0', STR_PAD_LEFT);

// Return the next company ID as the response
echo $nextCompanyID;
mysqli_close($conn);
?>
