<?php
session_start();
if (isset($_SESSION['preferred_name'])) {
    $preferred_name = $_SESSION['preferred_name'];
} else {
    // Retrieve the preferred_name from the student table based on the logged-in email
    require_once('../../db_connection.php');

    if (isset($_SESSION['email'])) {
        $email = $_SESSION['email'];

        $sql = "SELECT preferred_name FROM student WHERE email = '$email'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $preferred_name = $row['preferred_name'];
        } else {
            $preferred_name = "Unknown";
        }
    } else {
        $preferred_name = "Unknown";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Student Internship Status</title>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  
  <style type="text/css">
    #status {
      margin-top: 30px;
    }

    .assignment-card {
      background-color: #ffffff;
      padding: 15px;
      border-radius: 5px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
      margin-bottom: 15px;
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
  <!-- Add navigation bar (same as the previous code) -->
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
<div class="container mt-5 pb-5">    
  <h1>Student Internship Status</h1>
  
    <div id="status">Loading...</div>
  </div>

  <footer>
    <div class="footer-content">
      Department of Industrial Management - Faculty of Science - University of Kelaniya
    </div>
  </footer>

  <script>
    $(document).ready(function() {
      // Make an AJAX request to fetch student status
      $.ajax({
        url: 'stu_assigned_status.php',
        type: 'POST',
        dataType: 'json',
        success: function(data) {
          if (data.status === 'success') {
            // Display student assignments on the page
            var assignments = data.message;
            var assignmentsList = '';
            for (var i = 0; i < assignments.length; i++) {
              assignmentsList += '<div class="assignment-card">';
              assignmentsList += '<p>' + assignments[i] + '</p>';
              assignmentsList += '</div>';
            }
            $('#status').html(assignmentsList);
          } else {
            // Display error message if status retrieval fails
            $('#status').text('Error: ' + data.message);
          }
        },
        error: function() {
          $('#status').text('Error: Failed to fetch student status.');
        }
      });
    });
  </script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.25.0/dist/js/bootstrap-icons.min.js"></script>
  <script src="https://kit.fontawesome.com/your-font-awesome-kit.js" crossorigin="anonymous"></script>
</body>
</html>
