<?php
// Assuming you have the database connection file included
require_once('../../db_connection.php');
session_start();

// Check if the company is logged in
if (isset($_SESSION['email'])) {
    $companyEmail = $_SESSION['email'];

    // Fetch the companyID based on the email
    $stmt = $conn->prepare("SELECT companyID FROM company WHERE email = ?");
    $stmt->bind_param("s", $companyEmail);
    if ($stmt->execute()) {
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $companyID = $row['companyID'];

            // Check if the required parameters are received
            if (isset($_POST['student_number']) && isset($_POST['new_status'])) {
                $studentNumber = $_POST['student_number'];
                $newStatus = $_POST['new_status'];

                // Check if the student has been selected by another company before updating the status
                $selectedByOtherCompanyStmt = $conn->prepare("SELECT selected_companyID FROM assigned_preferences WHERE student_number = ?");
                $selectedByOtherCompanyStmt->bind_param("s", $studentNumber);
                if ($selectedByOtherCompanyStmt->execute()) {
                    $selectedByOtherCompanyResult = $selectedByOtherCompanyStmt->get_result();
                    if ($selectedByOtherCompanyResult->num_rows > 0) {
                        $row = $selectedByOtherCompanyResult->fetch_assoc();
                        $selectedCompanyID = $row['selected_companyID'];

                        // If the student has been selected by another company, do not allow the status update
                        if ($selectedCompanyID !== $companyID) {
                            $response = array('response_status' => 'error', 'message' => 'Student has been selected by another company.');
                            header('Content-Type: application/json');
                            echo json_encode($response);
                            exit();
                        }
                    }
                } else {
                    // Error executing the query
                    $response = array('response_status' => 'error', 'message' => 'Failed to check if the student has been selected by another company: ' . $conn->error);
                    header('Content-Type: application/json');
                    echo json_encode($response);
                    exit();
                }

                // Prepare and execute a query to update the current_status for the student assignment
                $updateStmt = $conn->prepare("UPDATE assigned_preferences SET current_status = ? WHERE companyID = ? AND student_number = ?");
                $updateStmt->bind_param("sss", $newStatus, $companyID, $studentNumber);
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
        } else {
            // Company not found in the company table
            $response = array('response_status' => 'error', 'message' => 'Company not found');
            header('Content-Type: application/json');
            echo json_encode($response);
        }
    } else {
        // Error executing the companyID query
        $response = array('response_status' => 'error', 'message' => 'Failed to fetch company information: ' . $conn->error);
        header('Content-Type: application/json');
        echo json_encode($response);
    }
} else {
    // Company is not logged in
    $response = array('response_status' => 'error', 'message' => 'Company not logged in');
    header('Content-Type: application/json');
    echo json_encode($response);
}

// Close the database connection
$conn->close();
?>