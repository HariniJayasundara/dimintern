
<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection file
require_once('../../db_connection.php');

// Query to fetch student details from the database

$query = "SELECT student_number, name_with_initials, email FROM student";
$result = mysqli_query($conn, $query);

$students = array();

while ($row = mysqli_fetch_assoc($result)) {
    $students[] = $row;
}

echo json_encode($students);
?>