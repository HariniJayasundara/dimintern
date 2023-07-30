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
    <title>Upload CV</title>
    <!-- Include Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <!-- Additional custom CSS -->
    <style>
        body {
            background-color: #f2f2f2;
        }

        .dashboard-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            justify-items: center;
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

    </style>
</head>
<body>
    <!-- Add navigation bar -->
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
                <h1>Upload CV</h1>

                <div class="card">
    <div class="card-body">
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <input type="file" name="cv" accept=".pdf" required />
            </div>
            <div class="text-center">
                <input type="submit" name="submit" value="Upload" class="btn btn-card">
            </div>
        </form>
    </div>
</div>

<style>
    .btn-card {
        padding: 10px 20px;
        background-color: #51b4af;
        border: none;
        color: white;
        font-size: 16px;
        border-radius: 5px;
        cursor: pointer;
    }

    .card {
        background-color: #f9f9f9;
        padding: 20px;
        border-radius: 5px;
        box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
    }
</style>


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
                
            </div>
        </div>
    </div>

    <footer>
        <div class="footer-content">
            Department of Industrial Management - Faculty of Science - University of Kelaniya
        </div>
    </footer>

    <!-- Include Bootstrap JS and Font Awesome -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.25.0/dist/js/bootstrap-icons.min.js"></script>
    <script src="https://kit.fontawesome.com/your-font-awesome-kit.js" crossorigin="anonymous"></script>
</body>
</html>
