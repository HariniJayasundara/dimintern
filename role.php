<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Select Your Role</title>
  <!-- Add Bootstrap CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <style type="text/css">
.teal-background {
  background-color: #1abc9c; /* Teal background color */
  min-height: 100vh;
  display: flex;
  justify-content: center;
  align-items: center;
}

.role-selection {
  color: #fff;
  text-align: center;
}

.role-selection h1 {
  font-size: 36px;
  margin-bottom: 20px;
}

.role-buttons {
  margin-top: 20px;
  display: flex; /* Horizontally align buttons */
  justify-content: center; /* Center buttons horizontally */
}

/* Teal button styles */
.btn-teal {
  background-color: #1abc9c; /* Teal color */
  color: #fff;
  border: none;
  border-radius: 30px; /* Rounded edges to make it look fancy */
  padding: 12px 25px; /* Increased padding for a more prominent button */
  text-decoration: none;
  font-size: 18px;
  margin: 0 10px; /* Add spacing between buttons */
}

.card {
  border: none; /* Remove the default card border */
  border-radius: 70px; /* Rounded edges for the card */
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Optional box shadow for a subtle effect */
}

.card-body {
  background-color: #222; /* Dark background color */
  min-height: 50vh;
  display: flex;
  flex-direction: column; /* Stack content in a column layout */
  align-items: center; /* Center items horizontally */
  justify-content: center; /* Center items vertically */
}

footer {
            background-color: white;
            color: black;
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
  <!-- Teal background -->
  <div class="teal-background">
    <div class="container">
      <div class="row">
        <div class="col-md-6 offset-md-3">
          <div class="card role-selection">
            <div class="card-body">
              <h1 class="card-title">Select Your Role</h1>
              <div class="role-buttons">
                <a href="User_management/Student/create_account.html" class="btn btn-teal">Student</a>
                <a href="User_management/Company/create_account.html" class="btn btn-teal">Company</a>
                <a href="AdminMy/create_account.php" class="btn btn-teal">Admin</a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <?php include 'User_management/footer.html'; ?>
  <!-- Add Bootstrap JS (optional, only if you need some JavaScript functionality) -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
