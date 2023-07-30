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
?>

<!DOCTYPE html>
<html>
<head>
    <title>Company Dashboard</title>
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
            align-self: center;
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

        <!-- Add Cards using Bootstrap cards -->
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <h2 class="card-title">Preferences</h2>
                    <p class="card-text">Add preferences for internship allocation.</p>
                    <a href="comp_preferences.php" class="btn">Add</a>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <h2 class="card-title">Interns</h2>
                    <p class="card-text">View your allocated interns and set status.</p>
                    <a href="comp_viewAllocations.php" class="btn">View</a>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <h2 class="card-title">CV</h2>
                    <p class="card-text">View CVs of allocated interns.</p>
                    <a href="comp_viewCV.php" class="btn">View</a>
                </div>
            </div>
            <div class="col-md-4 mb-5">
                <div class="card">
                    <h2 class="card-title">Reports</h2>
                    <p class="card-text">Upload intern assessment reports.</p>
                    <a href="upload_reports.php" class="btn">Upload</a>
                </div>
            </div>
        </div>
    </div>

    <footer>
        <div class="footer-content">
            Department of Industrial Management - Faculty of Science - University of Kelaniya
        </div>
    </footer>

    <!-- Include Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
