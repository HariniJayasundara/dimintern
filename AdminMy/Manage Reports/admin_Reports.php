<!DOCTYPE html>
<html>
<head>
    <title>Admin View Reports</title>

    <!-- Include SweetAlert library -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.17/dist/sweetalert2.all.min.js"></script>
</head>
<body>
    <h1>Admin View Reports</h1>

    <?php
    // Assuming you have the database connection file included
    require_once('../../db_connection.php');
    session_start();

    // Function to send feedback (insert or update)
    function sendFeedback($conn, $report_id, $user_id, $feedback) {
        $report_id = mysqli_real_escape_string($conn, $report_id);
        $user_id = mysqli_real_escape_string($conn, $user_id);
        $feedback = mysqli_real_escape_string($conn, $feedback);

        // Check if feedback already exists for this report_id and user_id
        $sql = "SELECT COUNT(*) AS feedback_count FROM feedback WHERE doc_id = '$report_id' AND user_id = '$user_id'";
        $result = $conn->query($sql);
        if (!$result) {
            die("Error fetching existing feedback: " . $conn->error);
        }

        $row = $result->fetch_assoc();
        $feedbackCount = $row['feedback_count'];

        if ($feedbackCount > 0) {
            // If feedback exists, update existing feedback
            $sql = "UPDATE feedback SET feedback = '$feedback' WHERE doc_id = '$report_id' AND user_id = '$user_id'";
        } else {
            // If no existing feedback, insert new feedback
            $sql = "INSERT INTO feedback (feedback_id, doc_id, user_id, user_type, feedback) VALUES (UUID(), '$report_id', '$user_id', 'reports', '$feedback')";
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

    <form method="GET">
        Filter by User ID: <input type="text" name="user_id" value="<?php echo htmlspecialchars($_SESSION['filterUserId']); ?>" />
        <input type="submit" value="Filter" />
        <?php if ($_SESSION['filterUserId']) : ?>
            <a href="admin_Reports.php">Remove Filter</a>
        <?php endif; ?>
    </form>

    <?php if ($result->num_rows > 0) : ?>
        <ul>
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
                <li>
                    <a href="<?php echo htmlspecialchars($reportPath); ?>" target="_blank"><?php echo htmlspecialchars($fileName); ?></a>
                    <br>
                    Existing Feedback: <?php echo htmlspecialchars($existingFeedback); ?>
                    <br>
                    <form method="POST" action="admin_internReports.php">
                        <input type="hidden" name="report_id" value="<?php echo htmlspecialchars($report_id); ?>">
                        <textarea name="feedback" placeholder="Enter feedback"><?php echo htmlspecialchars($existingFeedback); ?></textarea>
                        <input type="submit" name="submit_feedback" value="Send Feedback">
                    </form>
                </li>
            <?php endwhile; ?>
        </ul>
    <?php else : ?>
        <p>No reports found.</p>
    <?php endif; ?>

    <script type="text/javascript">
        // Function to display the pop-up message using SweetAlert
        function showFeedbackStatus(message, isSuccess) {
            Swal.fire({
                title: message,
                icon: isSuccess ? 'success' : 'error',
                showConfirmButton: false,
                timer: 1500
            });
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