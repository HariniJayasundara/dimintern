<?php
session_start();
if (isset($_SESSION['email'])) {
    // Retrieve the preferred_name from the student table based on the logged-in email
    require_once('../../db_connection.php');

    $email = $_SESSION['email'];

    $sql = "SELECT preferred_name FROM student WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $preferred_name = $row['preferred_name'];
    } else {
        $preferred_name = "Unknown";
    }
} else {
    // If $_SESSION['email'] is not set (user not logged in), set preferred_name to "Unknown"
    $preferred_name = "Unknown";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Upload Reports</title>
    <!-- Include Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body {
            background-color: #f2f2f2;
        }

        .card {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin: 20px 0;
        }

        .card-title {
            align-self: center;
        }

        .card-text {
            align-self: center;
        }

        .btn {
            padding: 10px 20px;
            background-color: #51b4af;
            border: none;
            color: white;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
        }

        footer {
            background-color: #03a68d;
            color: white;
            text-align: center;
            padding: 20px;
            margin: 0px;
            width: 100%;
            position: fixed;
            bottom: 0;
        }

        .footer-content {
            font-size: 14px;
        }

        @media (max-width: 768px) {
            .dashboard-cards {
                grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            }
        }

        @media (min-width: 768px) and (max-width: 992px) {
            .dashboard-cards {
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            }
        }

        @media (min-width: 992px) and (max-width: 1200px) {
            .dashboard-cards {
                grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            }
        }

        .nav-item {
            color: black;
        }

        /* Custom style for file input */
        input[type="file"] {
            opacity: 0;
            position: absolute;
            z-index: -1;
        }

        label[for="fileInput"] {
            cursor: pointer;
            display: inline-block;
            padding: 8px 16px;
            background-color: #51b4af;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
    </style>
</head>
<body>
    <!-- Add navigation bar (same as the previous code) -->
    <nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #51b4af;">
        <div class="container">
            <a class="navbar-brand" href="student_dashboard.php">
                <img src="../../Images/logo.png" alt="Logo" height="35"> Home
            </a>
            <ul class="navbar-nav me-auto">
                <li class="nav-item">Welcome, <?php echo $preferred_name; ?></li>
            </ul>
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="../../logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="row">
            <div class="col-md-12">
                <h1>Upload Reports</h1>

                <div class="card">
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data">
                            <label for="fileInput" id="fileInputLabel">Select Files:</label>
                            <input type="file" id="fileInput" name="reports[]" accept=".pdf" multiple required />
                            <br /><br />
                            <input type="submit" name="submit" value="Upload" class="btn btn-card">
                        </form>
                    </div>
                </div>

<?php
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

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
        echo 'Please log in to upload your reports.';
        exit; // Stop further execution if the student is not logged in
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
                    $destination = 'reports/' . $fileName; // Modified to store relative URL

                    // Move the uploaded file to the destination directory
                    if (move_uploaded_file($files['tmp_name'][$i], $_SERVER['DOCUMENT_ROOT'] . '/' . $destination)) {

                        // Store the relative report path in the database (assuming you have a MySQL connection established)
                        $reportPath = '/' . $destination; // Modified to store relative URL

                        // Use prepared statements to prevent SQL injection
                        $stmt = $conn->prepare("INSERT INTO reports (user_id, report_path) VALUES (?, ?)");
                        $stmt->bind_param("ss", $student_number, $reportPath); // Use "ss" for two string parameters

                        if ($stmt->execute()) {
                            echo '<div class="alert alert-success" role="alert">Report uploaded successfully.</div>';
                        } else {
                            echo '<div class="alert alert-danger" role="alert">Failed to insert report information into the database: ' . $stmt->error . '</div>';
                        }

                        $stmt->close();
                    } else {
                        echo '<div class="alert alert-danger" role="alert">Failed to move the uploaded files.</div>';
                    }
                } else {
                    echo '<div class="alert alert-danger" role="alert">Only PDF Allowed.</div>';
                }
            }
        } else {
            echo '<div class="alert alert-danger" role="alert">Please select files to upload.</div>';
        }
    }
?>
            </div>
        </div>
    </div>

    <footer>
        <div class="footer-content">
            Department of Industrial Management - Faculty of Science - University of Kelaniya
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.25.0/dist/js/bootstrap-icons.min.js"></script>
    <script src="https://kit.fontawesome.com/your-font-awesome-kit.js" crossorigin="anonymous"></script>
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