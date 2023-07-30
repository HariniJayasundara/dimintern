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
    <title>View Feedback</title>
    <!-- Include Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style type="text/css">
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
    </style>
</head>
<body>
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
        <h2>CV Feedback</h2>

        <div class="row mt-3">
            <?php
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
                    while ($cv_row = $cv_result->fetch_assoc()) {
                        $cvPath = $cv_row['cv_path'];
                        // Extract the filename from the path
                        $fileName = basename($cvPath);
                        $feedback = $cv_row['feedback'];
                        ?>
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo $fileName; ?></h5>
                                    <p class="card-text">Feedback: <?php echo $feedback; ?></p>
                                    <!-- Add other information as needed for each CV -->
                                    <a href="/cv_uploads/<?php echo $fileName; ?>" target="_blank" class="btn">View CV</a>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                } else {
                    echo '<p>No CVs found for this student.</p>';
                }
            } else {
                echo '<p>Please log in to view and download CVs and reports.</p>';
            }
            ?>
        </div>
    </div>

    <div class="container mt-5 mb-5">
        <h2>Report Feedback</h2>

        <div class="row mt-3">
            <?php
            // Fetch reports and feedback for the logged-in student from the database
            $stmt = $conn->prepare("
                SELECT reports.report_id, reports.report_path, feedback.feedback
                FROM reports
                LEFT JOIN feedback ON reports.report_id = feedback.doc_id
                WHERE reports.user_id = ?
            ");
            $stmt->bind_param("s", $student_number);
            $stmt->execute();
            $report_result = $stmt->get_result();

            if ($report_result->num_rows > 0) {
                while ($report_row = $report_result->fetch_assoc()) {
                    $reportPath = $report_row['report_path'];
                    // Extract the filename from the path
                    $reportFileName = basename($reportPath);
                    $reportFeedback = $report_row['feedback'];
                    ?>
                    <div class="col-md-6">
                        <div class="card mb-3">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $reportFileName; ?></h5>
                                <p class="card-text">Feedback: <?php echo $reportFeedback; ?></p>
                                <!-- Add other information as needed for each Report -->
                                <a href="/reports/<?php echo $reportFileName; ?>" target="_blank" class="btn">View Report</a>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo '<p>No reports found for this student.</p>';
            }

            // Close the database connection
            $conn->close();
            ?>
        </div>
    </div>

    <?php include '../footer.html'; ?> <!-- Include the footer at the end of the page -->

    <!-- Include Bootstrap JS and Font Awesome -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>