<!DOCTYPE html>
<html>
<head>
    <title>Upload Reports</title>
    <style>
        input[type="file"] {
            opacity: 0;
            position: absolute;
            z-index: -1;
        }
        label[for="fileInput"] {
            cursor: pointer;
            display: inline-block;
            padding: 8px 16px;
            background-color: #4CAF50;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        input[type="submit"]{
            cursor: pointer;
            display: inline-block;
            padding: 8px 16px;
            background-color: #4CAF50;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
    </style>
</head>
<body>
    <h1>Upload Reports</h1>

    <form method="POST" enctype="multipart/form-data">
        <label for="fileInput" id="fileInputLabel">Select Files: </label>
        <input type="file" id="fileInput" name="reports[]" accept=".pdf" multiple required />
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
        $companyEmail = $_SESSION['email'];

        // Fetch the company ID based on the email
        $stmt = $conn->prepare("SELECT companyID FROM company WHERE email = ?");
        $stmt->bind_param("s", $companyEmail);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            $companyID = $row['companyID']; // Get the companyID
        } else {
            echo 'Company not found or multiple companies found with the same email.';
            exit; // Stop further execution
        }
    } else {
        echo 'Please log in to upload your reports.';
        exit; // Stop further execution if not logged in
    }

    // Check if the form is submitted
    if (isset($_POST['submit'])) {
        // Check if files are selected
        if (isset($_FILES['reports'])) {
            $files = $_FILES['reports'];

            // Loop through each selected file
            for ($i = 0; $i < count($files['name']); $i++) {
                // Check if the uploaded file is a PDF
                $allowedMimeTypes = ['application/pdf'];
                if (in_array($files['type'][$i], $allowedMimeTypes)) {
                    // Sanitize the file name to prevent potential security issues
                    $fileName = basename($files['name'][$i]);

                    // Set the destination directory to store reports
                    $destination = $_SERVER['DOCUMENT_ROOT'] . '/reports/' . $fileName;

                    // Move the uploaded file to the destination directory
                    if (move_uploaded_file($files['tmp_name'][$i], $destination)) {
                        // File uploaded successfully
                        echo 'Report ' . ($i + 1) . ' uploaded successfully.';

                        // Store the report path in the database (assuming you have a MySQL connection established)
                        $reportPath = $destination;

                        // Debug output to check data before insertion
                        echo "Company ID: " . $companyID . "<br>";
                        echo "Report Path: " . $reportPath . "<br>";

                        // Use prepared statements to prevent SQL injection
                        $stmt = $conn->prepare("INSERT INTO reports (user_id, report_path) VALUES (?, ?)");
                        $stmt->bind_param("ss", $companyID, $reportPath); // Use "ss" for two string parameters

                        if ($stmt->execute()) {
                            echo 'Report information recorded in the database.';
                        } else {
                            echo 'Failed to insert report information into the database: ' . $stmt->error;
                        }

                        $stmt->close();
                    } else {
                        echo 'Failed to move the uploaded file.';
                    }
                } else {
                    echo 'Only PDF files are allowed.';
                }
            }
        } else {
            echo 'Please select at least one file to upload.';
        }
    }
    ?>
    <script>
        document.getElementById('fileInput').addEventListener('change', function () {
            var fileInput = document.getElementById('fileInput');
            var label = document.getElementById('fileInputLabel');
            var labelText = 'Select Files';

            if (fileInput.files && fileInput.files.length > 0) {
                if (fileInput.files.length === 1) {
                    labelText += fileInput.files[0].name;
                } else {
                    labelText += fileInput.files.length + ' files selected';
                }
            }

            label.innerText = labelText;
        });
    </script>

</body>
</html>