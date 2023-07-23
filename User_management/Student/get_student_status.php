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

        // Prepare and execute a query to check if the student is assigned
        $stmt = $conn->prepare("SELECT preference_id, companyID FROM assigned_preferences WHERE student_number = ?");
        $stmt->bind_param("s", $studentNumber);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Student is assigned, retrieve preference_id and companyID
            $row = $result->fetch_assoc();
            $preferenceId = $row['preference_id'];
            $companyID = $row['companyID'];

            // Construct the assigned status message
            $statusMessage = "Assigned: Preference ID - $preferenceId, Company ID - $companyID";
        } else {
            // Student is not assigned
            $statusMessage = "Not assigned";
        }

        // Return the status message as JSON
        $response = array('status' => 'success', 'message' => $statusMessage);
        header('Content-Type: application/json');
        echo json_encode($response);
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

