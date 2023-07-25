<?php
// Include the database connection file
require_once '../../db_connection.php';

// Function to fetch all CVs based on optional filters
function getCVs($conn, $filters = array()) {
    $sql = "SELECT cvs.cv_id, cvs.student_number, cvs.cv_path, feedback.feedback AS existing_feedback
            FROM cvs
            LEFT JOIN feedback ON cvs.cv_id = feedback.doc_id";

    if (!empty($filters)) {
        $sql .= " WHERE " . implode(" AND ", $filters);
    }

    $result = $conn->query($sql);
    if (!$result) {
        die("Error fetching CVs: " . $conn->error);
    }

    $cvs = array();
    while ($row = $result->fetch_assoc()) {
        $cvs[] = $row;
    }
    return $cvs;
}


// Function to save new feedback for a CV or update existing feedback
function saveFeedback($conn, $cv_id, $feedback) {
    $cv_id = $conn->real_escape_string($cv_id);
    $feedback = $conn->real_escape_string($feedback);

    // Get the student_number from the corresponding CV
    $sql = "SELECT student_number FROM cvs WHERE cv_id = '$cv_id'";
    $result = $conn->query($sql);
    if (!$result) {
        die("Error fetching student_number: " . $conn->error);
    }

    $row = $result->fetch_assoc();
    $user_id = $row['student_number'];

    // Check if feedback already exists for this CV
    $sql = "SELECT COUNT(*) AS feedback_count FROM feedback WHERE doc_id = '$cv_id'";
    $result = $conn->query($sql);
    if (!$result) {
        die("Error fetching existing feedback: " . $conn->error);
    }

    $row = $result->fetch_assoc();
    $feedbackCount = $row['feedback_count'];

    if ($feedbackCount > 0) {
        // If feedback exists, update existing feedback
        $sql = "UPDATE feedback SET feedback = '$feedback' WHERE doc_id = '$cv_id'";
    } else {
        // If no existing feedback, insert new feedback
        $sql = "INSERT INTO feedback (doc_id, user_id, feedback) VALUES ('$cv_id', '$user_id', '$feedback')";
    }

    if ($conn->query($sql) === TRUE) {
        return true;
    } else {
        die("Error saving feedback: " . $conn->error);
    }
}



// Handle form submission for adding feedback
$feedbackAlert = "";
if (isset($_POST['submit_feedback'])) {
    $cv_id = $_POST['cv_id'];
    $feedback = $_POST['feedback'];

    // Attempt to save the feedback
    if (saveFeedback($conn, $cv_id, $feedback)) {
        $feedbackAlert = "Feedback sent successfully!";
    } else {
        $feedbackAlert = "Failed to send feedback.";
    }
}

// Handle filter options
$filters = array();

// Add filter for student number
if (isset($_GET['student_number']) && !empty($_GET['student_number'])) {
    $student_number = $conn->real_escape_string($_GET['student_number']);
    $filters[] = "cvs.student_number = '$student_number'";
}

// Add filter to get CVs with no existing feedback
if (isset($_GET['no_feedback']) && $_GET['no_feedback'] === 'true') {
    $filters[] = "feedback.feedback IS NULL";
}

// Fetch all CVs with filters
$cvs = getCVs($conn, $filters);
?>


<!DOCTYPE html>
<html>
<head>
    <title>Admin View</title>
</head>
<body>
    <h1>Uploaded CVs</h1>

    <!-- Filter Options -->
    <form action="" method="get">
        <label for="student_number">Filter by Student Number:</label>
        <input type="text" name="student_number" id="student_number">
        <label for="no_feedback">Show CVs with no existing feedback:</label>
        <input type="checkbox" name="no_feedback" id="no_feedback" value="true">
        <button type="submit">Apply Filters</button>
        <a href="admin_view_cv.php">Reset Filters</a>
    </form>

    <!-- Display feedback message box -->
    <?php if (!empty($feedbackAlert)) { ?>
        <div style="padding: 10px; background-color: #f0f0f0; border: 1px solid #ccc; margin-bottom: 10px;">
            <?php echo $feedbackAlert; ?>
        </div>
    <?php } ?>

    <!-- Display all CVs -->
    <table>
        <thead>
            <tr>
                <th>Student Number</th>
                <th>CV Path</th>
                <th>Existing Feedback</th>
                <th>Feedback</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($cvs as $cv) { ?>
                <tr>
                    <td><?php echo $cv['student_number']; ?></td>
                    <td>
                        <a href="<?php echo $cv['cv_path']; ?>" target="_blank">View CV</a>
                    </td>
                    <td><?php echo $cv['existing_feedback']; ?></td>
                    <td>
                        <!-- Form to provide feedback for each CV -->
                        <form method="post">
                            <input type="hidden" name="cv_id" value="<?php echo $cv['cv_id']; ?>">
                            <textarea name="feedback" placeholder="Enter new feedback here"></textarea>
                            <button type="submit" name="submit_feedback">Submit Feedback</button>
                        </form>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</body>
</html>