<!DOCTYPE html>
<html>
<head>
    <title>View Feedback</title>
</head>
<body>
    <h2>CV Feedback</h2>

    <?php
    // Assuming you have the database connection file included
    require_once('../../db_connection.php');
    session_start();

    // Check if the student is logged in
    if (isset($_SESSION['email'])) {
        // Fetch the student number based on the email
        $studentEmail = $_SESSION['email'];
        $stmt = $conn->prepare("SELECT student_number FROM student WHERE email = ?");
        $stmt->bind_param("s", $studentEmail);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            $student_number = $row['student_number']; // Get the student number
        } else {
            echo 'Student not found or multiple students found with the same email.';
            exit; // Stop further execution if the student is not found
        }

        // Fetch CVs and feedback for the logged-in student from the database
        $stmt = $conn->prepare("
            SELECT cvs.cv_id, cvs.cv_path, feedback.feedback
            FROM cvs
            LEFT JOIN feedback ON cvs.cv_id = feedback.doc_id
            WHERE cvs.student_number = ?
        ");
        $stmt->bind_param("s", $student_number);
        $stmt->execute();
        $cv_result = $stmt->get_result();

        if ($cv_result->num_rows > 0) {
            echo '<h3>CVs</h3>';
            echo '<ul>';
            while ($cv_row = $cv_result->fetch_assoc()) {
                $cvPath = $cv_row['cv_path'];
                // Extract the filename from the path
                $fileName = basename($cvPath);

                $feedback = $cv_row['feedback'];

                // Display the PDF link and feedback
                echo '<li>';
                echo '<a href="/cv_uploads/' . $fileName . '" target="_blank">' . $fileName . '</a>';
                if ($feedback !== null) {
                    echo '<br>Feedback: ' . $feedback;
                }
                echo '</li>';
            }
            echo '</ul>';
        } else {
            echo 'No CVs found for this student.';
        }
    ?>

    <h2>Report Feedback</h2>

        <?php

        // Fetch reports and feedback for the logged-in student from the database
        $stmt = $conn->prepare("
            SELECT reports.report_id, reports.report_path, feedback.feedback
            FROM reports
            LEFT JOIN feedback ON reports.report_id = feedback.doc_id
            WHERE reports.user_id = ?
        ");

        if(!$stmt){
            // Error occurred during query preparation
    echo 'Error: Could not prepare the SQL query.';
    exit;

        }




        $stmt->bind_param("s", $student_number);
        $stmt->execute();
        $report_result = $stmt->get_result();

        if ($report_result->num_rows > 0) {
            echo '<h3>Reports</h3>';
            echo '<ul>';
            while ($report_row = $report_result->fetch_assoc()) {
                $reportPath = $report_row['report_path'];
                // Extract the filename from the path
                $reportFileName = basename($reportPath);

                $reportFeedback = $report_row['feedback'];

                // Display the PDF link and feedback
                echo '<li>';
                echo '<a href="/reports/' . $reportFileName . '" target="_blank">' . $reportFileName . '</a>';
                if ($reportFeedback !== null) {
                    echo '<br>Feedback: ' . $reportFeedback;
                }
                echo '</li>';
            }
            echo '</ul>';
        } else {
            echo 'No reports found for this student.';
        }

    } else {
        echo 'Please log in to view and download CVs and reports.';
    }
    ?>

</body>
</html>
