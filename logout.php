<?php
session_start();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Logout Confirmation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
    <div class="container mt-5">
        <div class="card shadow-lg">
            <div class="card-body">
                <h2 class="mb-4 text-center">Are you sure you want to logout?</h2>
                <div class="text-center">
                    <a id="cancelBtn" class="btn btn-secondary">Cancel</a>
                    <a href="logout_process.php" class="btn btn-danger">Logout</a>
                </div>
            </div>
        </div>
    </div>
    <script>
// JavaScript function to redirect the user to the appropriate dashboard
function redirectToDashboard() {
  // Replace 'student', 'company', and 'admin' with the actual role values
  // used in your application for students, companies, and admins, respectively.
  <?php if (isset($_SESSION['role'])): ?>
    <?php if ($_SESSION['role'] === 'student'): ?>
      window.location.href = "User_management/Student/student_dashboard.php";
    <?php elseif ($_SESSION['role'] === 'company'): ?>
      window.location.href = "User_management/Company/company_dashboard.php";
    <?php elseif ($_SESSION['role'] === 'admin'): ?>
      window.location.href = "admin_dashboard.php";
    <?php endif; ?>
  <?php else: ?>
    // If the user role is not defined or any other case, redirect to the default dashboard.
    window.location.href = "index.php";
  <?php endif; ?>
}

// Attach a click event listener to the "Cancel" button
document.getElementById("cancelBtn").addEventListener("click", redirectToDashboard);
</script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
