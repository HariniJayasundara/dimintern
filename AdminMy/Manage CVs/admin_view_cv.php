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
$feedbackAlertType = ""; // To store the type of alert (success, danger, etc.)

if (isset($_POST['submit_feedback'])) {
    $cv_id = $_POST['cv_id'];
    $feedback = $_POST['feedback'];

    // Attempt to save the feedback
    if (saveFeedback($conn, $cv_id, $feedback)) {
        // Set Bootstrap alert for success
        $feedbackAlert = "Feedback sent successfully!";
        $feedbackAlertType = "success";
    } else {
        // Set Bootstrap alert for failure
        $feedbackAlert = "Failed to send feedback.";
        $feedbackAlertType = "danger";
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
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.6.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="../styles.css">
    <script src="../sidebar.js"></script>
    <style>

    <style>

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

        <!-- Main Content -->
        <div class="col-md-10 main">

        <!-- Filter Options -->
    <form class="filter-form" action="" method="get">
        <label for="student_number">Filter by Student Number:</label>
        <input type="text" name="student_number" id="student_number" class="filter-input">
        <label for="no_feedback">Show CVs with no existing feedback:</label>
        <input type="checkbox" name="no_feedback" id="no_feedback" value="true" class="filter-input">
        <button type="submit" class="btnA">Apply Filters</button>
        <a href="admin_view_cv.php" class="btn btn-secondary filter-btn">Reset Filters</a>
    </form>

    <!-- Display feedback message box -->
    <?php if (!empty($feedbackAlert)) { ?>
        <!-- Use Bootstrap alert for feedback message -->
        <div class="feedback-alert alert alert-<?php echo $feedbackAlertType; ?>">
            <?php echo $feedbackAlert; ?>
        </div>
    <?php } ?>

    <!-- Display all CVs in a Bootstrap card -->
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-striped m-0">
                    <thead class="thead-dark">
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
                                        <textarea name="feedback" class="form-control" placeholder="Enter new feedback here"></textarea>
                                        <button type="submit" name="submit_feedback" class="submitF">Submit Feedback</button>
                                    </form>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</div>
    <!-- Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.slim.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.6.0/js/bootstrap.min.js"></script>
</body>
</html>