<?php
session_start();
if (isset($_SESSION['company_name'])) {
    $preferred_name = $_SESSION['company_name'];
} else {
    // Retrieve the preferred_name from the student table based on the logged-in email
    require_once('../../db_connection.php');

    if (isset($_SESSION['email'])) {
        $email = $_SESSION['email'];

        $sql = "SELECT company_name FROM company WHERE email = '$email'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $company_name = $row['company_name'];
        } else {
            $company_name = "Unknown";
        }
    } else {
        $company_name = "Unknown";
    }
}

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Function to get CV path for a student
function getCVPath($conn, $studentNumber) {
    // Query the database to get the CV path for the given student number
    $cvPath = "CV unavailable";
    $cvSql = "SELECT cv_path FROM cvs WHERE student_number = '$studentNumber'";
    $cvResult = $conn->query($cvSql);

    // Check if the query executed successfully
    if (!$cvResult) {
        die("Error executing CV query: " . $conn->error);
    }

    // Check if any rows were returned
    if ($cvResult->num_rows > 0) {
        $cvRow = $cvResult->fetch_assoc();
        $cvPath = $cvRow['cv_path'];
    }

    return $cvPath;
}

// Get companyID from the logged-in company's email
$companyEmail = $_SESSION['email']; // Retrieve the logged-in company's email
$companyID = getCompanyID($conn, $companyEmail);

// Function to get the companyID based on the company email
function getCompanyID($conn, $email) {
    $sql = "SELECT companyID FROM company WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['companyID'];
    } else {
        // Return an error value if the email is not found in the company table
        return null;
    }
}

// Fetch assigned students for the given companyID
$sql = "SELECT DISTINCT student_number FROM assigned_preferences WHERE companyID = '$companyID'";
$result = $conn->query($sql);

// Array to store unique student numbers
$uniqueStudents = array();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $studentNumber = $row['student_number'];

        // Check if the student number is already in the array
        if (!in_array($studentNumber, $uniqueStudents)) {
            $uniqueStudents[] = $studentNumber;

            // Get the CV path for the unique student number and display it
            $cvPath = getCVPath($conn, $studentNumber);
            //echo "<p><a href='$cvPath'>CV for $studentNumber</a></p>";
        }
    }
}

// Close the result set
$result->close();
// Close the connection
$conn->close();
?>


<!DOCTYPE html>
<html>
<head>
    <title>View CVs for Company</title>
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
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #51b4af;">
        <div class="container">
            <a class="navbar-brand" href="company_dashboard.php">
                <img src="../../Images/logo.png" alt="Logo" height="35"> Home
            </a>
            <ul class="navbar-nav me-auto">
                <li class="nav-item">Welcome, <?php echo $company_name; ?></li>
            </ul>
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="../../logout.php">Logout</a>
                </li>
            </ul>
        </div>
</nav>

<div class="container">
    <h2 class="text-center mt-5">CVs for Company</h2>
    <div id="cvList" class="container mt-3 pb-5">
        <?php
        // Re-establish the database connection for querying CVs
        $conn = new mysqli("localhost", "root", "", "dimintern");
        if ($conn->connect_error) {
            die("Database connection failed: " . $conn->connect_error);
        }

        foreach ($uniqueStudents as $studentNumber) {
            $cvPath = getCVPath($conn, $studentNumber);
            echo '<div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title">CV for ' . $studentNumber . '</h5>
                        <a href="' . $cvPath . '" target="_blank" class="btn">View CV</a>
                    </div>
                </div>';
        }

        // Close the connection
        $conn->close();
        ?>
    </div>
</div>
    <?php include'../footer.html'; ?>

    <!-- Include Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>