<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Internship Handling Platform</title>
  <!-- Add Bootstrap CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <style type="text/css">
/* Custom styles for the landing page */
.dark-background {
  background-color: #222; /* Dark background color */
  min-height: 100vh;
  display: flex;
  justify-content: center;
  align-items: center;
}

.app-info {
  color: #fff;
  padding: 20px;
}

/* Teal button styles */
.btn-teal {
  background-color: #1abc9c; /* Teal color */
  color: #fff;
  border: none;
  border-radius: 8px; /* Rounded edges */
  padding: 10px 20px;
  text-decoration: none;
  display: inline-block;
  font-size: 18px;
}

/* Apply button-like styles to h1 element */
.app-info h1 {
  font-size: 36px;
  color: black;
  display: inline-block; /* Make sure it's displayed inline to apply button styles */
  background-color: #1abc9c; /* Teal color */
  border-radius: 8px; /* Rounded edges */
  padding: 10px 20px;
}


.app-info .description {
  font-size: 18px;
  margin-bottom: 20px;
  align-self: center;
}

.buttons {
  margin-top: 20px;
}

/* Teal button styles */
.btn-teal {
  background-color: #1abc9c; /* Teal color */
  color: #fff;
  border: none;
  margin-right: 120px;
}

.btn-teal:hover {
  background-color: #149b7f; /* Darker shade on hover */
}

.app-image img {
  max-width: 100%;
}

/* Footer styles */
footer {
  background-color: #03a68d;
  color: white;
  text-align: center;
  padding: 20px;
  margin: 0;
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
  <!-- Dark background -->
  <div class="dark-background">
    <!-- Navbar (optional, if needed) -->
    <!-- <nav class="navbar navbar-expand-lg navbar-light bg-light">
      <a class="navbar-brand" href="#">Your App Name</a>
    </nav> -->

    <div class="container">
      <div class="row">
        <!-- Left section -->
        <div class="col-md-6">
          <div class="app-image">
            <img src="Images/app-image.jpg" alt="App Image">
          </div>
        </div>
        <!-- Right section -->
        <div class="col-md-6">
          <div class="app-info">
            <h1>Internship Handling Platform</h1>
            <p class="description">Welcome to the Internship Handling Platform for the Department of Industrial Management, Faculty of Science, University of Kelaniya! <br> 
              <br>A one-stop solution for all internship related services. Login to your account or if you're new, Register today!</p>
            <div class="buttons">
              <a href="login.html" class="btn btn-teal">Login</a>
              <a href="role.php" class="btn btn-teal">Register</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Footer -->
    <?php include 'User_management/footer.html'; ?>

  <!-- Add Bootstrap JS (optional, only if you need some JavaScript functionality) -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>