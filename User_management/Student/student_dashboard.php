<?php
session_start();
if (isset($_SESSION['preferred_name'])) {
    $preferred_name = $_SESSION['preferred_name'];
} else {
    // Handle the case when the preferred name is not set
    $preferred_name = "Unknown";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Dashboard</title>
    <!-- Include your CSS stylesheet here -->
    <style type="text/css">
      
        
body {
  font-family: Arial, sans-serif;
  background-color: #f2f2f2;
  margin: 0;
  padding: 0;
  display: flex;
  flex-direction: column;
  min-height: 100vh;
}

.container {
  max-width: 500px;
  margin: 0 auto;
  background-color: #ffffff;
  padding: 20px;
  border-radius: 5px;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);

}

h2 {
  text-align: center;
  margin-bottom: 20px;
}

.form-group {
  margin-bottom: 20px;
}

label {
  display: block;
  margin-bottom: 5px;
  font-weight: bold;
}

input[type="text"],
input[type="number"],
select,
textarea {
  width: 100%;
  padding: 10px;
  border-radius: 5px;
  border: 1px solid #ccc;
  box-sizing: border-box;
  font-size: 14px;
}

textarea {
  height: 80px;
}

button[type="submit"] {
  padding: 10px 20px;
  background-color: #4CAF50;
  border: none;
  color: white;
  font-size: 16px;
  border-radius: 5px;
  cursor: pointer;
}

button[type="submit"]:hover {
  background-color: #45a049;
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

.dashboard-cards {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 20px;
}

.card {
  background-color: #f9f9f9;
  padding: 20px;
  border-radius: 5px;
  box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
}

.card h2 {
  margin-bottom: 10px;
}

.card p {
  margin-bottom: 10px;
}

.card a {
  display: inline-block;
  padding: 8px 16px;
  background-color: #4CAF50;
  color: #fff;
  text-decoration: none;
  border-radius: 5px;
  transition: background-color 0.3s;
}

.card a:hover {
  background-color: #45a049;
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
    <!-- Add navigation bar -->
    <nav>
        <ul>
            <li>Welcome, <?php echo $preferred_name; ?></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>

    <!-- Add Cards -->
    <div class="dashboard-cards">
        <div class="card">
            <h2>Upload CV</h2>
            <p>Upload your CV here.</p>
            <a href="upload_cv.php">Upload</a>
        </div>

        <div class="card">
            <h2>Add Preferences</h2>
            <p>Add your preferred intern positions here.</p>
            <a href="stu_preference.php">Add</a>
        </div>

        <div class="card">
            <h2>Internship Status</h2>
            <p>View current allocation status.</p>
            <a href="stu_status.php">View</a>
        </div>

        <div class="card">
            <h2>Upload Reports</h2>
            <p>Upload your reports here.</p>
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
