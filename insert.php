<!DOCTYPE html>
<html>
<head>
    <title>Admin View CVs</title>
</head>
<body>
    <h1>Admin View CVs</h1>

    <?php
    // Assuming you have the database connection file included
    require_once('../../db_connection.php');
    session_start();

    // Initialize the filterStudentNumber variable
    if (!isset($_SESSION['filterStudentNumber'])) {
        $_SESSION['filterStudentNumber'] = '';
    }
    
    // Function to send feedback
    function sendFeedback($conn, $cv_id, $student_number, $feedback) {
        // Sanitize inputs to prevent SQL injection
        $cv_id = mysqli_real_escape_string($conn, $cv_id);
        $student_number = mysqli_real_escape_string($conn, $student_number);
        $feedback = mysqli_real_escape_string($conn, $feedback);

        // Prepare the feedback insertion into the database
        $stmt = $conn->prepare("INSERT INTO cv_feedback (cv_id, student_number, feedback) VALUES (?, ?, ?)");
        if (!$stmt) {
            die('Error in preparing statement: ' . $conn->error);
        }

        // Bind parameters and execute the statement
        $stmt->bind_param("sss", $cv_id, $student_number, $feedback);
        if (!$stmt->execute()) {
            die('Error in executing statement: ' . $stmt->error);
        }

        $stmt->close();
    }

    // Check if the feedback form is submitted
    if (isset($_POST['submit_feedback'])) {
        $cv_id = $_POST['cv_id'];
        $feedback = $_POST['feedback'];
        $student_number = $_SESSION['filterStudentNumber']; // Use the filtered student number if available

        // Call the sendFeedback function
        sendFeedback($conn, $cv_id, $student_number, $feedback);
    }

    // Check if the form is submitted with a student_number filter
    $filterStudentNumber = isset($_GET['student_number']) ? $_GET['student_number'] : '';

    // Save the filter in the session
    $_SESSION['filterStudentNumber'] = $filterStudentNumber;

    // Fetch CVs filtered by student_number (if filter applied) or fetch all CVs
    $filterCondition = $filterStudentNumber ? "WHERE student_number = '$filterStudentNumber'" : "";
    $result = $conn->query("SELECT * FROM cvs $filterCondition");
    ?>

    <form method="GET">
        Filter by Student Number: <input type="text" name="student_number" value="<?php echo $filterStudentNumber; ?>" />
        <input type="submit" value="Filter" />
        <?php if ($filterStudentNumber) : ?>
            <a href="admin_view_cv.php">Remove Filter</a>
        <?php endif; ?>
    </form>

    <?php if ($result->num_rows > 0) : ?>
        <ul>
            <?php while ($row = $result->fetch_assoc()) : ?>
                <?php
                $cv_id = $row['cv_id'];
                $cvPath = $row['cv_path'];
                $fileName = basename($cvPath);
                ?>
                <li>
                    <a href="<?php echo $cvPath; ?>" target="_blank"><?php echo $fileName; ?></a>
                    <br>
                    <form method="POST" action="admin_view_cv.php">
                        <input type="hidden" name="cv_id" value="<?php echo $cv_id; ?>">
                        <textarea name="feedback" placeholder="Enter feedback"></textarea>
                        <input type="submit" name="submit_feedback" value="Send Feedback">
                    </form>
                </li>
            <?php endwhile; ?>
        </ul>
    <?php else : ?>
        <p>No CVs found.</p>
    <?php endif; ?>
    
</body>
</html>


<!DOCTYPE html>
<html>
<head>
    <title>Admin View CVs</title>
</head>
<body>
    <h1>Admin View CVs</h1>

    <?php
    // Assuming you have the database connection file included
    require_once('../../db_connection.php');
    session_start();

    // Function to send feedback
    function sendFeedback($conn, $cv_id, $student_number, $feedback) {
        // Sanitize inputs to prevent SQL injection
        $cv_id = mysqli_real_escape_string($conn, $cv_id);
        $student_number = mysqli_real_escape_string($conn, $student_number);
        $feedback = mysqli_real_escape_string($conn, $feedback);

        // Check if feedback for the same cv_id already exists
        $stmt = $conn->prepare("SELECT cv_id FROM cv_feedback WHERE cv_id = ?");
        if (!$stmt) {
            die('Error in preparing statement: ' . $conn->error);
        }

        $stmt->bind_param("s", $cv_id);
        if (!$stmt->execute()) {
            die('Error in executing statement: ' . $stmt->error);
        }

        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            // Feedback for this CV already exists
            $_SESSION['feedback_status'] = "Feedback for this CV already exists.";
            $stmt->close();
            return false;
        }

        $stmt->close();

        // Prepare the feedback insertion into the database
        $stmt = $conn->prepare("INSERT INTO cv_feedback (cv_id, student_number, feedback) VALUES (?, ?, ?)");
        if (!$stmt) {
            die('Error in preparing statement: ' . $conn->error);
        }

        // Bind parameters and execute the statement
        $stmt->bind_param("sss", $cv_id, $student_number, $feedback);
        if (!$stmt->execute()) {
            die('Error in executing statement: ' . $stmt->error);
        }

        $stmt->close();
        return true;
    }

    // Check if the feedback form is submitted
    if (isset($_POST['submit_feedback'])) {
        $cv_id = $_POST['cv_id'];
        $feedback = $_POST['feedback'];

        // Fetch the student_number from the database based on the cv_id
        $stmt = $conn->prepare("SELECT student_number FROM cvs WHERE cv_id = ?");
        if (!$stmt) {
            die('Error in preparing statement: ' . $conn->error);
        }

        $stmt->bind_param("s", $cv_id);
        if (!$stmt->execute()) {
            die('Error in executing statement: ' . $stmt->error);
        }

        $stmt->bind_result($student_number);
        $stmt->fetch();
        $stmt->close();

        // Check if the student_number is available (whether the form was submitted with or without filter)
        // If not available, fall back to the filter value (if the filter was applied)
        if (empty($student_number) && isset($_SESSION['filterStudentNumber'])) {
            $student_number = $_SESSION['filterStudentNumber'];
        }

        // Call the sendFeedback function and store the feedback status in a session variable
        if (sendFeedback($conn, $cv_id, $student_number, $feedback)) {
            $_SESSION['feedback_status'] = "Feedback Sent";
        }

        // Redirect back to the current page to prevent form resubmission on refresh
        header("Location: admin_view_cv.php");
        exit();
    }

    // ... (rest of the code)

    // Call the sendFeedback function and store the feedback status in a session variable
    if (sendFeedback($conn, $cv_id, $student_number, $feedback)) {
        $_SESSION['feedback_status'] = "Feedback Sent";
    }

    // ... (rest of the code)
    ?>

    <script type="text/javascript">
        // Display the feedback status message if set
        <?php if (isset($_SESSION['feedback_status'])) : ?>
            alert("<?php echo $_SESSION['feedback_status']; ?>");
        <?php unset($_SESSION['feedback_status']); // Clear the message after displaying it ?>
        <?php endif; ?>
    </script>
</body>
</html>


<!DOCTYPE html>
<html>
<head>
    <title>Admin View CVs</title>
</head>
<body>
    <h1>Admin View CVs</h1>

    <?php
    // Assuming you have the database connection file included
    require_once('../../db_connection.php');
    session_start();

    // Function to send feedback
    function sendFeedback($conn, $cv_id, $student_number, $feedback) {
        // Sanitize inputs to prevent SQL injection
        $cv_id = mysqli_real_escape_string($conn, $cv_id);
        $student_number = mysqli_real_escape_string($conn, $student_number);
        $feedback = mysqli_real_escape_string($conn, $feedback);

        // Prepare the feedback insertion into the database
        $stmt = $conn->prepare("INSERT INTO cv_feedback (cv_id, student_number, feedback) VALUES (?, ?, ?)");
        if (!$stmt) {
            die('Error in preparing statement: ' . $conn->error);
        }

        // Bind parameters and execute the statement
        $stmt->bind_param("sss", $cv_id, $student_number, $feedback);
        if (!$stmt->execute()) {
            die('Error in executing statement: ' . $stmt->error);
        }

        $stmt->close();

        return true;
    }

        // Check if the feedback form is submitted
        if (isset($_POST['submit_feedback'])) {
            $cv_id = $_POST['cv_id'];
            $feedback = $_POST['feedback'];

            // Fetch the student_number from the database based on the cv_id
            $stmt = $conn->prepare("SELECT student_number FROM cvs WHERE cv_id = ?");
            if (!$stmt) {
                die('Error in preparing statement: ' . $conn->error);
            }

            $stmt->bind_param("s", $cv_id);
            if (!$stmt->execute()) {
                die('Error in executing statement: ' . $stmt->error);
            }

            $stmt->bind_result($student_number);
            $stmt->fetch();
            $stmt->close();

            // Check if the student_number is available (whether the form was submitted with or without filter)
            // If not available, fall back to the filter value (if the filter was applied)
            if (empty($student_number) && isset($_SESSION['filterStudentNumber'])) {
                $student_number = $_SESSION['filterStudentNumber'];
            }

            // Call the sendFeedback function and store the feedback status in a session variable
            if (sendFeedback($conn, $cv_id, $student_number, $feedback)) {
                $_SESSION['feedback_status'] = "Feedback Sent";
            } else {
                $_SESSION['feedback_status'] = "Feedback Not Sent. Please Try Again.";
            }

            // Redirect back to the current page to prevent form resubmission on refresh
            header("Location: admin_view_cv.php");
            exit();
        }

        // Check if the form is submitted with a student_number filter
        $filterStudentNumber = isset($_GET['student_number']) ? $_GET['student_number'] : '';

        // Initialize the filterStudentNumber variable
        if (!isset($_SESSION['filterStudentNumber'])) {
            $_SESSION['filterStudentNumber'] = '';
        }

        // Save the filter in the session
        $_SESSION['filterStudentNumber'] = $filterStudentNumber;

        // Fetch CVs filtered by student_number (if filter applied) or fetch all CVs
        $filterCondition = $filterStudentNumber ? "WHERE student_number = '$filterStudentNumber'" : "";
        $result = $conn->query("SELECT * FROM cvs $filterCondition");
        ?>

        <form method="GET">
            Filter by Student Number: <input type="text" name="student_number" value="<?php echo $_SESSION['filterStudentNumber']; ?>" />
            <input type="submit" value="Filter" />
            <?php if ($_SESSION['filterStudentNumber']) : ?>
                <a href="admin_view_cv.php">Remove Filter</a>
            <?php endif; ?>
        </form>

        <?php if ($result->num_rows > 0) : ?>
            <ul>
                <?php while ($row = $result->fetch_assoc()) : ?>
                    <?php
                    $cv_id = $row['cv_id'];
                    $cvPath = $row['cv_path'];
                    $fileName = basename($cvPath);
                    ?>
                    <li>
                        <a href="<?php echo $cvPath; ?>" target="_blank"><?php echo $fileName; ?></a>
                        <br>
                        <form method="POST" action="admin_view_cv.php">
                            <input type="hidden" name="cv_id" value="<?php echo $cv_id; ?>">
                            <textarea name="feedback" placeholder="Enter feedback"></textarea>
                            <input type="submit" name="submit_feedback" value="Send Feedback">
                        </form>
                    </li>
                <?php endwhile; ?>
            </ul>
        <?php else : ?>
            <p>No CVs found.</p>
        <?php endif; ?>

        <script type="text/javascript">
            // Display the feedback status message if set
            <?php if (isset($_SESSION['feedback_status'])) : ?>
                alert("<?php echo $_SESSION['feedback_status']; ?>");
            <?php unset($_SESSION['feedback_status']); // Clear the message after displaying it ?>
            <?php endif; ?>
        </script>
    </body>
    </html>
