<?php
session_start();
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection file
require_once('../../db_connection.php');

// Check if the student is logged in
if (isset($_SESSION['email'])) {
    $studentEmail = $_SESSION['email'];

    // Fetch the student number based on the email
    $stmt = $conn->prepare("SELECT student_number FROM student WHERE email = ?");
    $stmt->bind_param("s", $studentEmail);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        $student_number = $row['student_number']; // Get the student_number for later use

        // Continue with the CV upload process

        $pdf = $_FILES['pdf']['name'];
        $pdf_type = $_FILES['pdf']['type'];
        $pdf_size = $_FILES['pdf']['size'];
        $pdf_tem_loc = $_FILES['pdf']['tmp_name'];

        // Set the destination directory to store CVs
        $destinationDirectory = 'D:/wamp64/www/dimintern/User_management/Student/cv_uploads/';
        $pdf_store = $destinationDirectory . $pdf;

        // Validate that the uploaded file is a PDF
        if ($pdf_type !== 'application/pdf') {
            echo 'Only PDF files are allowed.';
            exit;
        }

        move_uploaded_file($pdf_tem_loc, $pdf_store);

        // Use prepared statements to prevent SQL injection
        $stmt = $conn->prepare("INSERT INTO cvs (student_number, cv_path) VALUES (?, ?)");
        $stmt->bind_param("is", $student_number, $pdf_store);
        $stmt->execute();
        $stmt->close();

        echo 'CV uploaded successfully.';
    } else {
        echo 'Student not found or multiple students found with the same email.';
    }
} else {
    echo 'Please log in before uploading the CV.';
}
?>