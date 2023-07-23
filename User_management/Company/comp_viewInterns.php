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

            // Fetch all students selected by other companies
            $selectedByOtherCompanyStmt = $conn->prepare("SELECT student_number, selected_companyID FROM assigned_preferences WHERE current_status = 'S4' AND selected_companyID != ?");
            $selectedByOtherCompanyStmt->bind_param("s", $companyID);
            if ($selectedByOtherCompanyStmt->execute()) {
                $selectedByOtherCompanyResult = $selectedByOtherCompanyStmt->get_result();
                $selectedByOtherCompany = array();

                while ($selectedRow = $selectedByOtherCompanyResult->fetch_assoc()) {
                    $selectedByOtherCompany[] = array(
                        'student_number' => $selectedRow['student_number'],
                        'selected_companyID' => $selectedRow['selected_companyID']
                    );
                }
            } else {
                // Error executing the query
                $response = array('response_status' => 'error', 'message' => 'Failed to check if students have been selected by other companies: ' . $conn->error);
                header('Content-Type: application/json');
                echo json_encode($response);
                exit();
            }

            // Prepare and execute a query to get student assignments for the company
            $assignmentsStmt = $conn->prepare("SELECT a.student_number, a.preference_id, a.current_status, a.selected_companyID, p.preference_name 
                                               FROM assigned_preferences a 
                                               INNER JOIN preferences p ON a.preference_id = p.preference_id 
                                               WHERE a.companyID = ? OR (a.selected_companyID IS NULL AND a.current_status = 'S3')");
            $assignmentsStmt->bind_param("s", $companyID);
            if ($assignmentsStmt->execute()) {
                $assignmentsResult = $assignmentsStmt->get_result();

                if ($assignmentsResult->num_rows > 0) {
                    $assignments = array();

                    while ($assignmentRow = $assignmentsResult->fetch_assoc()) {
                        $studentNumber = $assignmentRow['student_number'];

                        // Check if the student is selected by another company
                        $selectedByOtherCompanyIDs = array_column($selectedByOtherCompany, 'selected_companyID');
                        if ($assignmentRow['current_status'] === 'S4' && in_array($companyID, $selectedByOtherCompanyIDs)) {
                            $selectedByOtherCompany[] = array(
                                'student_number' => $studentNumber,
                                'selected_companyID' => $assignmentRow['selected_companyID']
                            );
                            continue; // Skip this student if selected by another company
                        }

                        // Add each assignment to the assignments array
                        $assignments[] = array(
                            'student_number' => $studentNumber,
                            'preference_id' => $assignmentRow['preference_id'],
                            'current_status' => $assignmentRow['current_status'],
                            'preference_name' => $assignmentRow['preference_name']
                        );
                    }

                    // If there are students selected by other companies, show the message box and remove them from the list
                    if (!empty($selectedByOtherCompany)) {
                        $studentsBeingSelectedByOtherCompanies = array();
                        foreach ($selectedByOtherCompany as $selectedStudent) {
                            if ($selectedStudent['selected_companyID'] !== $companyID) {
                                $studentsBeingSelectedByOtherCompanies[] = $selectedStudent['student_number'];
                            }
                        }

                        if (!empty($studentsBeingSelectedByOtherCompanies)) {
                            // Some students have been selected by other companies and are being displayed to this company.
                            // Remove those students from this company's list.
                            $newAssignments = array();
                            foreach ($assignments as $assignment) {
                                if (!in_array($assignment['student_number'], $studentsBeingSelectedByOtherCompanies)) {
                                    $newAssignments[] = $assignment;
                                }
                            }
                            $assignments = $newAssignments;
                        } else {
                            // The student displayed to this company is selected by another company.
                            // Display the message box.
                            $response = array('response_status' => 'message', 'selectedByOtherCompany' => $selectedByOtherCompany);
                            header('Content-Type: application/json');
                            echo json_encode($response);
                            exit();
                        }
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
                    // No assignments found for the company
                    $response = array('response_status' => 'error', 'message' => 'No assignments found for the company.');
                    header('Content-Type: application/json');
                    echo json_encode($response);
                }
            } else {
                // Error executing the assignments query
                $response = array('response_status' => 'error', 'message' => 'Failed to fetch assignments: ' . $conn->error);
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
