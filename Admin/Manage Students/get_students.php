
<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection file
require_once('../../db_connection.php');

// Query to fetch student details from the database

$query = "SELECT student.student_number, student.name_with_initials, student_contact.email FROM student
          INNER JOIN student_contact ON student.student_number = student_contact.student_number";
$result = mysqli_query($conn, $query);

$students = array();

while ($row = mysqli_fetch_assoc($result)) {
    $students[] = $row;
}

echo json_encode($students);
?>