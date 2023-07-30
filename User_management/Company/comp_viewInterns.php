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

            // Prepare and execute a query to get student assignments for the company
            $assignmentsStmt = $conn->prepare("
                SELECT a.student_number, a.preference_id, a.current_status, a.selected_companyID, p.preference_name 
                FROM assigned_preferences a 
                INNER JOIN preferences p ON a.preference_id = p.preference_id 
                WHERE (a.companyID = ? AND a.selected_companyID = ?)
                OR (a.companyID = ? AND a.selected_companyID IS NULL)
                AND (NOT EXISTS (
                    SELECT 1 FROM assigned_preferences sub_a 
                    WHERE sub_a.student_number = a.student_number AND (
                        sub_a.selected_companyID <> a.companyID OR
                        sub_a.selected_companyID IS NOT NULL
                    )
                ))
            ");
            $assignmentsStmt->bind_param("sss", $companyID, $companyID, $companyID);

            if ($assignmentsStmt->execute()) {
                $assignmentsResult = $assignmentsStmt->get_result();

                if ($assignmentsResult->num_rows > 0) {
                    $assignments = array();

                    while ($assignmentRow = $assignmentsResult->fetch_assoc()) {
                        // Add each assignment to the assignments array
                        $assignments[] = array(
                            'student_number' => $assignmentRow['student_number'],
                            'preference_id' => $assignmentRow['preference_id'],
                            'current_status' => $assignmentRow['current_status'],
                            'preference_name' => $assignmentRow['preference_name'],
                            'selected_companyID' => $assignmentRow['selected_companyID']
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
                } else {
                    // No assignments found for the company
                    $response = array('response_status' => 'error', 'message' => 'No assignments found for the company.');
                }
            } else {
                // Error executing the assignments query
                $response = array('response_status' => 'error', 'message' => 'Failed to fetch assignments: ' . $conn->error);
            }
        } else {
            // Company not found in the company table
            $response = array('response_status' => 'error', 'message' => 'Company not found');
        }
    } else {
        // Error executing the companyID query
        $response = array('response_status' => 'error', 'message' => 'Failed to fetch company information: ' . $conn->error);
    }
} else {
    // Company is not logged in
    $response = array('response_status' => 'error', 'message' => 'Company not logged in');
}

// Close the database connection
$conn->close();

// Send the response as JSON
header('Content-Type: application/json');
echo json_encode($response);
?>


<?php
// // Assuming you have the database connection file included
// require_once('../../db_connection.php');
// session_start();

// // Check if the company is logged in
// if (isset($_SESSION['email'])) {
//   $companyEmail = $_SESSION['email'];

//   // Fetch the companyID based on the email
//   $stmt = $conn->prepare("SELECT companyID FROM company WHERE email = ?");
//   $stmt->bind_param("s", $companyEmail);
//   if ($stmt->execute()) {
//     $result = $stmt->get_result();

//     if ($result->num_rows > 0) {
//       $row = $result->fetch_assoc();
//       $companyID = $row['companyID'];

//       // Prepare and execute a query to get student assignments for the company
//       $assignmentsStmt = $conn->prepare("
//         SELECT a.student_number, a.preference_id, a.current_status, a.selected_companyID, p.preference_name 
//         FROM assigned_preferences a 
//         INNER JOIN preferences p ON a.preference_id = p.preference_id 
//         WHERE (a.companyID = ? AND a.selected_companyID = ?)
//           OR (a.companyID = ? AND a.selected_companyID IS NULL)
//           AND (NOT EXISTS (
//             SELECT 1 FROM assigned_preferences sub_a 
//             WHERE sub_a.student_number = a.student_number AND (
//               sub_a.selected_companyID <> a.companyID OR
//               sub_a.selected_companyID IS NOT NULL
//             )
//           ))
//       ");
//       $assignmentsStmt->bind_param("sss", $companyID, $companyID, $companyID);

//       if ($assignmentsStmt->execute()) {
//         $assignmentsResult = $assignmentsStmt->get_result();

//         if ($assignmentsResult->num_rows > 0) {
//           $assignments = array();

//           while ($assignmentRow = $assignmentsResult->fetch_assoc()) {
//             // Add each assignment to the assignments array
//             $assignments[] = array(
//               'student_number' => $assignmentRow['student_number'],
//               'preference_id' => $assignmentRow['preference_id'],
//               'current_status' => $assignmentRow['current_status'],
//               'preference_name' => $assignmentRow['preference_name'],
//               'selected_companyID' => $assignmentRow['selected_companyID']
//             );
//           }

//           // Fetch status options from the database and add them to the response
//           $statuses = array();
//           $statusStmt = $conn->prepare("SELECT status_id, status FROM internship_status");
//           if ($statusStmt->execute()) {
//             $statusResult = $statusStmt->get_result();
//             while ($statusRow = $statusResult->fetch_assoc()) {
//               $statuses[$statusRow['status_id']] = $statusRow['status'];
//             }
//           }

//           // Return the assignments and statuses as JSON
//           $response = array('response_status' => 'success', 'assignments' => $assignments, 'statuses' => $statuses);
//           header('Content-Type: application/json');
//           echo json_encode($response);
//         } else {
//           // No assignments found for the company
//           $response = array('response_status' => 'error', 'message' => 'No assignments found for the company.');
//           header('Content-Type: application/json');
//           echo json_encode($response);
//         }
//       } else {
//         // Error executing the assignments query
//         $response = array('response_status' => 'error', 'message' => 'Failed to fetch assignments: ' . $conn->error);
//         header('Content-Type: application/json');
//         echo json_encode($response);
//       }
//     } else {
//       // Company not found in the company table
//       $response = array('response_status' => 'error', 'message' => 'Company not found');
//       header('Content-Type: application/json');
//       echo json_encode($response);
//     }
//   } else {
//     // Error executing the companyID query
//     $response = array('response_status' => 'error', 'message' => 'Failed to fetch company information: ' . $conn->error);
//     header('Content-Type: application/json');
//     echo json_encode($response);
//   }
// } else {
//   // Company is not logged in
//   $response = array('response_status' => 'error', 'message' => 'Company not logged in');
//   header('Content-Type: application/json');
//   echo json_encode($response);
// }

// // Close the database connection
// $conn->close();
?>
