<?php
session_start();
if (isset($_SESSION['company_name'])) {
    $company_name = $_SESSION['company_name'];
} else {
    // Handle the case when the preferred name is not set
    $company_name = "Unknown";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Company Dashboard</title>
    <style>
        body {
  font-family: Arial, sans-serif;
}

.dashboard {
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
}

.card {
  width: 300px;
  height: 200px;
  margin: 20px;
  padding: 20px;
  border-radius: 10px;
  background-color: #f0f0f0;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
}

.card h3 {
  font-size: 20px;
  margin-bottom: 10px;
}

.card p {
  font-size: 16px;
  color: #777777;
}

.card:hover {
  transform: translateY(-5px);
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
}

nav {
      background-color: #333;
  color: #fff;
  padding: 10px;
}

nav ul {
  list-style-type: none;
  margin: 0;
  padding: 0;
  display: flex;
  justify-content: flex-end;
}

nav ul li {
  margin-left: 10px;
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

    <nav>
        <ul>
            <li>Welcome, <?php echo $company_name; ?></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>

    <div class="dashboard">
        <div class="card">
            <h3>Add Preferences</h3>
            <p>Add your preferences for intern allocation</p>
            <a href="comp_preferences.php">Add</a>
        </div>

        <div class="card">
            <h3>View Allocated CVs</h3>
            <p>View CVs of interns allocated to your company</p>
            <a href="comp_viewCV.php">View</a>
        </div>

        <div class="card">
            <h3>View Allocated Interns</h3>
            <p>View a list of allocated interns and set their internship status</p>
            <a href="comp_viewInterns.html">View</a>
        </div>

        <div class="card">
            <h3>Upload Reports</h3>
            <p>Upload reports on interns</p>
            <a href="upload_reports.php">Upload</a>
        </div>
    </div>

    <footer>
            <div class="footer-content">
                Department of Industrial Management - Faculty of Science - University of Kelaniya
            </div>
    </footer>

</body>
</html>
