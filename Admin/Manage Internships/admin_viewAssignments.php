<?php
// Assuming you have the database connection file included
require_once('../../db_connection.php');
session_start();

// Check if the admin is logged in (You may need to implement admin login functionality)
//if (isset($_SESSION['admin_email'])) {
    // Fetch all student assignments with details from the database
    $assignmentsStmt = $conn->prepare("SELECT a.student_number, p.preference_name, c.company_name, s.status
                                       FROM assigned_preferences a
                                       INNER JOIN preferences p ON a.preference_id = p.preference_id
                                       INNER JOIN company c ON a.companyID = c.companyID
                                       INNER JOIN internship_status s ON a.current_status = s.status_id");
    if ($assignmentsStmt->execute()) {
        $assignmentsResult = $assignmentsStmt->get_result();
        $assignments = array();

        while ($assignmentRow = $assignmentsResult->fetch_assoc()) {
            $assignments[] = array(
                'student_number' => $assignmentRow['student_number'],
                'preference_name' => $assignmentRow['preference_name'],
                'company_name' => $assignmentRow['company_name'],
                'current_status' => $assignmentRow['status']
            );
        }

        // Fetch status options from the database and add them to the response
        $statuses = array();
        $statusStmt = $conn->prepare("SELECT status_id, status FROM internship_status");
        if ($statusStmt->execute()) {
            $statusResult = $statusStmt->get_result();
            while ($statusRow = $statusResult->fetch_assoc()) {
                $statuses[$statusRow['status_id']] = $statusRow['status'];
            }
        }

        // Return the assignments and statuses as JSON
        $response = array('response_status' => 'success', 'assignments' => $assignments, 'statuses' => $statuses);
        header('Content-Type: application/json');
        echo json_encode($response);
    } else {
        // Error executing the assignments query
        $response = array('response_status' => 'error', 'message' => 'Failed to fetch assignments: ' . $conn->error);
        header('Content-Type: application/json');
        echo json_encode($response);
    }
//} else {
    // Admin is not logged in
 //   $response = array('response_status' => 'error', 'message' => 'Admin not logged in');
 //   header('Content-Type: application/json');
  //  echo json_encode($response);
//}

// Close the database connection
$conn->close();
?>