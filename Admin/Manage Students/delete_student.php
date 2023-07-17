<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection file
require_once('../../db_connection.php');

// Retrieve the student number from the request body
$data = json_decode(file_get_contents('php://input'), true);
$studentNumber = $data['studentNumber'];

// Perform delete operation for the student account
$query = "DELETE FROM student WHERE student_number = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $studentNumber);

$response = array();

if ($stmt->execute()) {
    // Delete from student_contact table
    $query = "DELETE FROM student_contact WHERE student_number = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $studentNumber);

    if ($stmt->execute()) {
        $response['success'] = true;
    } else {
        $response['success'] = false;
        $response['message'] = "Error deleting student account.";
    }
} else {
    $response['success'] = false;
    $response['message'] = "Error deleting student account.";
}

// Return the response as JSON
header('Content-Type: application/json');
echo json_encode($response);
?>
