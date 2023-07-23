<!DOCTYPE html>
<html>
<head>
    <title>View and Download CVs</title>
</head>
<body>
    <h1>CVs</h1>

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
        $stmt = $conn->prepare("SELECT cv_path FROM cvs WHERE student_number = ?");
        $stmt->bind_param("s", $student_number);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            echo '<ul>';
            while ($row = $result->fetch_assoc()) {
                $cvPath = $row['cv_path'];
                // Extract the filename from the path
                $fileName = basename($cvPath);

                // Fetch feedback for the current CV
                $stmt_feedback = $conn->prepare("SELECT feedback FROM cv_feedback WHERE cv_id = ?");
                // Assuming 'cv_id' is the correct column to identify CVs in the 'cv_feedback' table
                $stmt_feedback->bind_param("s", $cvPath); // Use the correct column for cv_id
                $stmt_feedback->execute();
                $result_feedback = $stmt_feedback->get_result();
                $feedback = $result_feedback->num_rows > 0 ? $result_feedback->fetch_assoc()['feedback'] : '';

                // Display the PDF link and feedback
                echo '<li>';
                echo '<a href="/cv_uploads/' . $fileName . '" target="_blank">' . $fileName . '</a>';
                if ($feedback !== '') {
                    echo '<br>Feedback: ' . $feedback;
                }
                echo '</li>';
            }
            echo '</ul>';
        } else {
            echo 'No CVs found for this student.';
        }
    } else {
        echo 'Please log in to view and download CVs.';
    }
    ?>
</body>
</html>
