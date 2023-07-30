<?php
// Assuming you have the database connection file included
require_once('../../db_connection.php');
session_start();

// Check if the admin is logged in
//if (isset($_SESSION['admin_email'])) {
    // Check if the required parameters are received
    if (isset($_POST['student_number']) && isset($_POST['new_status'])) {
        $studentNumber = $_POST['student_number'];
        $newStatus = $_POST['new_status'];

        // Prepare and execute a query to update the current_status for the student assignment
        $updateStmt = $conn->prepare("UPDATE assigned_preferences SET current_status = ? WHERE student_number = ?");
        $updateStmt->bind_param("ss", $newStatus, $studentNumber);
        if ($updateStmt->execute()) {
            // Successfully updated status
            $response = array('response_status' => 'success');
            header('Content-Type: application/json');
            echo json_encode($response);
        } else {
            // Failed to update status
            $response = array('response_status' => 'error', 'message' => 'Failed to update status: ' . $conn->error);
            header('Content-Type: application/json');
            echo json_encode($response);
        }
    } else {
        // Required parameters not received
        $response = array('response_status' => 'error', 'message' => 'Missing parameters.');
        header('Content-Type: application/json');
        echo json_encode($response);
    }
// } else {
//     // Admin is not logged in
//     $response = array('response_status' => 'error', 'message' => 'Admin not logged in');
//     header('Content-Type: application/json');
//     echo json_encode($response);
// }

// Close the database connection
$conn->close();
?>
