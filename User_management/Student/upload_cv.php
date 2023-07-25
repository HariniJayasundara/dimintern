<!DOCTYPE html>
<html>
<head>
    <title>Upload CV</title>
</head>
<body>
    <h1>Upload CV</h1>

    <form method="POST" enctype="multipart/form-data">
        <input type="file" name="cv" accept=".pdf" required />
        <br /><br />
        <input type="submit" name="submit" value="Upload" />
    </form>

    <?php
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

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

        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            $student_number = $row['student_number']; // Get the student number
        } else {
            echo 'Student not found or multiple students found with the same email.';
            exit; // Stop further execution if the student is not found
        }
    } else {
        echo 'Please log in to upload your CV.';
        exit; // Stop further execution if the student is not logged in
    }

    // Check if the form is submitted
    if (isset($_POST['submit'])) {
        // Check if a file is selected
        if (isset($_FILES['cv']) && $_FILES['cv']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['cv'];

            // Check if the uploaded file is a PDF
            $allowedMimeTypes = ['application/pdf'];
            if (in_array($file['type'], $allowedMimeTypes)) {
                // Sanitize the file name to prevent potential security issues
                $fileName = basename($file['name']);

                // Set the destination directory to store CVs using a relative URL
                $destination = '/cv_uploads/' . $fileName;

                // Check if the student_number is already present in the CVs table
                $stmt = $conn->prepare("SELECT COUNT(*) AS count FROM cvs WHERE student_number = ?");
                $stmt->bind_param("s", $student_number);
                $stmt->execute();
                $result = $stmt->get_result();

                $row = $result->fetch_assoc();
                if ($row['count'] > 0) {
                    echo 'You have already uploaded your CV.';
                    exit; // Stop further execution if the student_number already exists in the CVs table
                }

                // Move the uploaded file to the destination directory
                if (move_uploaded_file($file['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . $destination)) {
                    // File uploaded successfully
                    echo 'CV uploaded successfully.';

                    // Store the CV path in the database (assuming you have a MySQL connection established)
                    $cvPath = $destination;

                    // Debug output to check data before insertion
                    echo "Student Number: " . $student_number . "<br>";
                    echo "CV Path: " . $cvPath . "<br>";

                    // Use prepared statements to prevent SQL injection
                    $stmt = $conn->prepare("INSERT INTO cvs (student_number, cv_path) VALUES (?, ?)");
                    $stmt->bind_param("ss", $student_number, $cvPath); // Use "ss" for two string parameters

                    if ($stmt->execute()) {
                        echo 'CV information recorded in the database.';
                    } else {
                        echo 'Failed to insert CV information into the database: ' . $stmt->error;
                    }

                    $stmt->close();
                } else {
                    echo 'Failed to move the uploaded file.';
                }
            } else {
                echo 'Only PDF files are allowed.';
            }
        } else {
            echo 'Please select a file to upload.';
        }
    }
    ?>
</body>
</html>