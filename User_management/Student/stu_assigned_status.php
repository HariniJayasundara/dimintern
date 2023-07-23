<?php
// Assuming you have the database connection file included
require_once('../../db_connection.php');
session_start();

// Check if the student is logged in
if (isset($_SESSION['email'])) {
    $studentEmail = $_SESSION['email'];

    // Fetch the student number based on the email
    $stmt = $conn->prepare("SELECT student_number FROM student WHERE email = ?");
    $stmt->bind_param("s", $studentEmail);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $studentNumber = $row['student_number'];

        // Prepare and execute a query to fetch all assignments of the student with corresponding status names
        $stmt = $conn->prepare("SELECT a.preference_id, p.preference_name, a.companyID, c.company_name, s.status AS current_status
                                FROM assigned_preferences a 
                                INNER JOIN preferences p ON a.preference_id = p.preference_id 
                                INNER JOIN company c ON a.companyID = c.companyID 
                                INNER JOIN internship_status s ON a.current_status = s.status_id
                                WHERE a.student_number = ?");
        $stmt->bind_param("s", $studentNumber);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Student is assigned, construct the status message with all assignments
            $assignments = array();
            while ($row = $result->fetch_assoc()) {
                $preferenceId = $row['preference_id'];
                $preferenceName = $row['preference_name'];
                $companyID = $row['companyID'];
                $companyName = $row['company_name'];
                $currentStatus = $row['current_status'];

                // Add the assignment details to the assignments array
                $assignments[] = "Preference - $preferenceName, Company - $companyName, Current Status - $currentStatus";
            }
            // Return the assignments as JSON
            $response = array('status' => 'success', 'message' => $assignments);
            header('Content-Type: application/json');
            echo json_encode($response);
        } else {
            // Student is not assigned
            $response = array('status' => 'error', 'message' => 'Not assigned');
            header('Content-Type: application/json');
            echo json_encode($response);
        }
    } else {
        // Student not found in the student table
        $response = array('status' => 'error', 'message' => 'Student not found');
        header('Content-Type: application/json');
        echo json_encode($response);
    }
} else {
    // Student is not logged in
    $response = array('status' => 'error', 'message' => 'Student not logged in');
    header('Content-Type: application/json');
    echo json_encode($response);
}

// Close the database connection
$conn->close();
?>
