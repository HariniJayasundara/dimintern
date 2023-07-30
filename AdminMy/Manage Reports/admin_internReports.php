<?php
// database connection file
require_once('../../db_connection.php');
session_start();

// Function to send feedback (insert or update)
function sendFeedback($conn, $report_id, $user_id, $feedback)
{
    $report_id = mysqli_real_escape_string($conn, $report_id);
    $user_id = mysqli_real_escape_string($conn, $user_id);
    $feedback = mysqli_real_escape_string($conn, $feedback);

    // Check if feedback already exists for this report_id and user_id
    $sql = "SELECT feedback_id FROM feedback WHERE doc_id = '$report_id' AND user_id = '$user_id'";
    $result = $conn->query($sql);
    if (!$result) {
        die("Error fetching existing feedback: " . $conn->error);
    }

    $feedbackRow = $result->fetch_assoc();
    $feedback_id = isset($feedbackRow['feedback_id']) ? $feedbackRow['feedback_id'] : '';

    if (!empty($feedback_id)) {
        // If feedback exists, update existing feedback
        $sql = "UPDATE feedback SET feedback = '$feedback' WHERE feedback_id = '$feedback_id'";
    } else {
        // If no existing feedback, insert new feedback
        $sql = "INSERT INTO feedback (doc_id, user_id, user_type, feedback) VALUES ('$report_id', '$user_id', 'reports', '$feedback')";
    }

    if ($conn->query($sql) === TRUE) {
        return true;
    } else {
        die("Error saving feedback: " . $conn->error);
    }
}

// Check if the feedback form is submitted
if (isset($_POST['submit_feedback'])) {
    $report_id = $_POST['report_id'];
    $feedback = $_POST['feedback'];

    // Fetch the user_id from the database based on the report_id
    $stmt = $conn->prepare("SELECT user_id FROM reports WHERE report_id = ?");
    if (!$stmt) {
        die('Error in preparing statement: ' . $conn->error);
    }

    $stmt->bind_param("s", $report_id);
    if (!$stmt->execute()) {
        die('Error in executing statement: ' . $stmt->error);
    }

    $stmt->bind_result($user_id);
    $stmt->fetch();
    $stmt->close();

    // Check if the user_id is available (whether the form was submitted with or without filter)
    // If not available, fall back to the filter value (if the filter was applied)
    if (empty($user_id) && isset($_SESSION['filterUserId'])) {
        $user_id = $_SESSION['filterUserId'];
    }

    // Call the sendFeedback function and store the feedback status in a session variable
    if (sendFeedback($conn, $report_id, $user_id, $feedback)) {
        $_SESSION['feedback_status'] = "Feedback Sent";
    } else {
        $_SESSION['feedback_status'] = "Feedback Already Available";
    }

    // Set the feedback_report_id session variable to store the report_id for which feedback status is displayed
    $_SESSION['feedback_report_id'] = $report_id;

    // Redirect back to the current page to prevent form resubmission on refresh
    header("Location: admin_internReports.php");
    exit();
}

// Check if the form is submitted with a user_id filter
$filterUserId = isset($_GET['user_id']) ? $_GET['user_id'] : '';

// Initialize the filterUserId variable
if (!isset($_SESSION['filterUserId'])) {
    $_SESSION['filterUserId'] = '';
}

// Save the filter in the session
$_SESSION['filterUserId'] = $filterUserId;

// Fetch reports filtered by user_id (if filter applied) or fetch all reports
$filterCondition = $filterUserId ? "WHERE user_id = '$filterUserId'" : "";
$result = $conn->query("SELECT * FROM reports $filterCondition");
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Reports</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.6.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="../styles.css">
    <script src="https://code.jquery.com/jquery-3.6.0.slim.min.js"></script>
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.6.0/js/bootstrap.min.js"></script>
    <script src="../sidebar.js"></script>
    <style type="text/css">
         /* Set background color and occupy full height */
        html, body {
            height: 100%;
            background-color: #f5f5f5;
        }

        /* Set the main container to have a white background */
        .main {
            background-color: #fff;
            padding: 20px;
            min-height: calc(100vh - 60px); /* Calculate height excluding the top bar */
        }

        /* Filter Options */
        .filter-form {
            width: 85%;
            display: flex;
            align-items: center;
            margin-bottom: 10px;
            background-color: white;
            margin-left: 20px;
        }

        .filter-form label {
            margin-right: 10px;
        }

        .filter-input {
            padding: 5px;
        }

        /* Buttons */
        .filter-btn {
            padding: 10px;
            margin-right: 5px;
        }


        /* Feedback Message Box */
        .feedback-alert {
            padding: 10px;
            margin-top: 10px;
            background-color: #f0f0f0;
            border: 1px solid #ccc;
        }

        .btnA {
    margin: 5px; /* Add some space between the buttons */
    padding: 5px 10px;
    color: #fff;
    border: none;
    cursor: pointer;
    transition: background-color 0.3s ease-in-out;
    border-radius: 5px; /* Add rounded corners to the buttons */
    background-color: #51b4af;
        }

        table {
  width: 85%;
  border-collapse: collapse;
  background-color: #fff;
  padding: 20px;
  margin: 20px;
}

th,
td {
  padding: 8px;
  border: 1px solid #ddd;
  text-align: left;
}

th {
  background-color: #f2f2f2;
}

tr:hover {
  background-color: #f9f9f9;
}
    .submitF {
    margin: 5px; /* Add some space between the buttons */
    padding: 5px 10px;
    color: #fff;
    border: none;
    cursor: pointer;
    transition: background-color 0.3s ease-in-out;
    border-radius: 5px; /* Add rounded corners to the buttons */
    background-color: #51b4af;
    }

    .form-control{
    margin: 5px; /* Add some space between the buttons */
    padding: 5px 10px;
    border: none;
    border-radius: 5px; /* Add rounded corners to the buttons */    
    }
        
    .main {
  position: absolute;
  top: 60px;
  width: calc(100% - 260px);
  min-height: calc(100vh - 60px);
  left: 260px;
  background: #343a40;

}
    </style>
</head>
<body>
    <!-- Topbar -->
    <div class="container">
        <div class="topbar">
            <div class="logo">
                <h2>DIM Admin</h2>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="sidebar">
            <ul>
                <li>
                    <a href="../index.php">
                        <i class="fas fa-th-large"></i>
                        <div>Dashboard</div>
                    </a>
                </li>
                <li>
                    <a href="../Manage Students/manage_students.html">
                        <i class="fas fa-user-graduate"></i>
                        <div>Students</div>
                    </a>
                </li>
                <li>
                    <a href="../Manage Companies/manage_companies.php">
                        <i class="fas fa-user-tie" ></i>
                        <div>Companies</div>
                    </a>
                </li>
                <li>
                    <a href="../Manage Admin/admin.php">
                        <i class="fas fa-users"></i>
                        <div>DIM Staff</div>
                    </a>
                </li>
                <li>
                    <a href="../Manage Preferences/map_preferences.html">
                        <i class="fas fa-hand-sparkles"></i>
                        <div>Preferences</div>
                    </a>
                </li>
                <li>
                    <a href="../Manage Internships/admin_manageAssignments.php">
                        <i class="fas fa-clipboard-check"></i>
                        <div>Allocations</div>
                    </a>
                </li>
                <li>
                    <a>
                        <i class="fas fa-file-signature"></i>
                        <div>Documentation</div>
                    </a>
                    <ul>
                    <li>
                        <a href="admin_view_cv">
                            <i class="far fa-file-alt"></i>
                            <div>CV</div>
                        </a>
                    </li>
                    <li>
                        <a href="../Manage Reports/admin_internReports.php">
                            <i class="far fa-file-alt"></i>
                            <div>User Reports</div>
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            <i class="far fa-file-alt"></i>
                            <div>Generate Report</div>
                    </a>
                    </li>
                    </ul>
                    </li>
            </ul>
            <div class="logout-option">
                <a href="../../logout.php">
                    <i class="fas fa-sign-out-alt"></i>
                    <div>Logout</div>
            </div> </a>
        </div>
    </div>






    <?php if ($result->num_rows > 0) : ?>
        <!-- Display all reports in a table -->
    <div class="main">
    <form method="GET" class="filter-form">
        <label for="filter_user_id">Filter by User ID:</label>
        <input type="text" id="filter_user_id" name="user_id" class="filter-input" value="<?php echo htmlspecialchars($_SESSION['filterUserId']); ?>" />
        <button type="submit" class="btnA">Filter</button>
        <?php if ($_SESSION['filterUserId']) : ?>
            <a href="admin_internReports.php" class="btn btn-secondary">Remove Filter</a>
        <?php endif; ?>
    </form>
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped m-0">
                        <thead class="thead-dark">
                            <tr>
                                <th>User ID</th>
                                <th>Report File</th>
                                <th>Existing Feedback</th>
                                <th>Feedback</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()) : ?>
                                <?php
                                $report_id = $row['report_id'];
                                $reportPath = $row['report_path'];
                                $fileName = basename($reportPath);

                                // Fetch the user_id from the database based on the report_id
                                $stmt = $conn->prepare("SELECT user_id FROM reports WHERE report_id = ?");
                                if (!$stmt) {
                                    die('Error in preparing statement: ' . $conn->error);
                                }

                                $stmt->bind_param("s", $report_id);
                                if (!$stmt->execute()) {
                                    die('Error in executing statement: ' . $stmt->error);
                                }

                                $stmt->bind_result($user_id);
                                $stmt->fetch();
                                $stmt->close();

                                // Check if the user_id is available (whether the form was submitted with or without filter)
                                // If not available, fall back to the filter value (if the filter was applied)
                                if (empty($user_id) && isset($_SESSION['filterUserId'])) {
                                    $user_id = $_SESSION['filterUserId'];
                                }

                                // Fetch existing feedback for the report_id and user_id
                                $existingFeedback = '';
                                $sql = "SELECT feedback FROM feedback WHERE doc_id = '$report_id' AND user_id = '$user_id'";
                                $feedbackResult = $conn->query($sql);
                                if ($feedbackResult->num_rows > 0) {
                                    $existingFeedbackRow = $feedbackResult->fetch_assoc();
                                    $existingFeedback = $existingFeedbackRow['feedback'];
                                }
                                ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($user_id); ?></td>
                                    <td>
                                        <a href="<?php echo htmlspecialchars($reportPath); ?>" target="_blank"><?php echo htmlspecialchars($fileName); ?></a>
                                    </td>
                                    <td><?php echo htmlspecialchars($existingFeedback); ?></td>
                                    <td>
                                        <!-- Form to provide feedback for each report -->
                                        <form method="post">
                                            <input type="hidden" name="report_id" value="<?php echo htmlspecialchars($report_id); ?>">
                                            <textarea name="feedback" class="form-control" placeholder="Enter feedback"><?php echo htmlspecialchars($existingFeedback); ?></textarea>
                                            <input type="submit" name="submit_feedback" value="Send Feedback" class="submitF">
                                        </form>
                                            <?php
                                            // Display the feedback status message if set
                                            if (isset($_SESSION['feedback_status']) && $_SESSION['feedback_report_id'] === $report_id) {
                                                $feedbackStatus = htmlspecialchars($_SESSION['feedback_status']);
                                                $isSuccess = $feedbackStatus === "Feedback Sent";
                                                $alertClass = $isSuccess ? 'alert-success' : 'alert-danger';
                                                echo '<div class="feedback-alert ' . $alertClass . '">' . $feedbackStatus . '</div>';
                                                unset($_SESSION['feedback_status']);
                                                unset($_SESSION['feedback_report_id']);
                                            }
                                            ?>  
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <?php else : ?>
        <p>No reports found.</p>
    <?php endif; ?>

    <script type="text/javascript">
        function showFeedbackStatus(status, isSuccess) {
            const feedbackStatusDiv = document.createElement('div');
            feedbackStatusDiv.textContent = status;
            feedbackStatusDiv.classList.add('feedback-status');
            if (isSuccess) {
                feedbackStatusDiv.classList.add('success');
            } else {
                feedbackStatusDiv.classList.add('error');
            }

            // Append the feedback status div to the body
            document.body.appendChild(feedbackStatusDiv);

            // Automatically remove the feedback status div after 3 seconds (adjust time as needed)
            setTimeout(function() {
                feedbackStatusDiv.remove();
            }, 3000);
        }

        // Display the feedback status message if set
        <?php if (isset($_SESSION['feedback_status'])) : ?>
            <?php
            $feedbackStatus = htmlspecialchars($_SESSION['feedback_status']);
            $isSuccess = $feedbackStatus === "Feedback Sent";
            ?>
            showFeedbackStatus("<?php echo $feedbackStatus; ?>", <?php echo $isSuccess ? 'true' : 'false'; ?>);
        <?php unset($_SESSION['feedback_status']); // Clear the message after displaying it ?>
        <?php endif; ?>
    </script>
</body>
</html>        
