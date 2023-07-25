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

      // Check if the required parameters are present in the POST request
      if (isset($_POST['student_number']) && isset($_POST['new_status']) && isset($_POST['preference_id'])) {
        $studentNumber = $_POST['student_number'];
        $newStatus = $_POST['new_status'];
        $preferenceID = $_POST['preference_id'];

        // Fetch the previous status of the student for the specific preference_id
        $prevStatusStmt = $conn->prepare("SELECT current_status FROM assigned_preferences WHERE companyID = ? AND student_number = ? AND preference_id = ?");
        $prevStatusStmt->bind_param("sss", $companyID, $studentNumber, $preferenceID);
        if ($prevStatusStmt->execute()) {
          $prevStatusResult = $prevStatusStmt->get_result();

          if ($prevStatusResult->num_rows > 0) {
            $prevStatusRow = $prevStatusResult->fetch_assoc();
            $prevStatus = $prevStatusRow['current_status'];

            if ($newStatus === 'S4' && $prevStatus !== 'S4') {
              // If the new status is 'S4' and previous status was not 'S4', update selected_companyID with companyID
              $updateStmt = $conn->prepare("UPDATE assigned_preferences SET current_status = ?, selected_companyID = ? WHERE companyID = ? AND student_number = ? AND preference_id = ?");
              $updateStmt->bind_param("sssss", $newStatus, $companyID, $companyID, $studentNumber, $preferenceID);
            } else {
              // If the new status is not 'S4', update only the current_status
              $updateStmt = $conn->prepare("UPDATE assigned_preferences SET current_status = ? WHERE companyID = ? AND student_number = ? AND preference_id = ?");
              $updateStmt->bind_param("ssss", $newStatus, $companyID, $studentNumber, $preferenceID);
            }

            if ($updateStmt->execute()) {
              // Success response
              $response = array('response_status' => 'success');
              header('Content-Type: application/json');
              echo json_encode($response);
            } else {
              // Error updating the status
              $response = array('response_status' => 'error', 'message' => 'Failed to update status: ' . $conn->error);
              header('Content-Type: application/json');
              echo json_encode($response);
            }
          } else {
            // Student not found in assigned_preferences table for the specific preference_id
            $response = array('response_status' => 'error', 'message' => 'Student not found for the given preference_id');
            header('Content-Type: application/json');
            echo json_encode($response);
          }
        } else {
          // Error executing the query
          $response = array('response_status' => 'error', 'message' => 'Failed to fetch previous status: ' . $conn->error);
          header('Content-Type: application/json');
          echo json_encode($response);
        }
      } else {
        // Missing parameters in the request
        $response = array('response_status' => 'error', 'message' => 'Missing parameters: student_number, new_status, and preference_id are required.');
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