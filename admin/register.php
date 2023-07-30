<?php include('includes/header.php')?>
<?php include('includes/navbar.php')?>

session_start();

<body class="bg-gradient-primary">

    <div class="container">

        <div class="card o-hidden border-0 shadow-lg my-5">
            <div class="card-body p-0">
                <!-- Nested Row within Card Body -->
                <div class="row">
                    <div class="col-lg-5 d-none d-lg-block bg-register-image"></div>
                    <div class="col-lg-7">
                        <div class="p-5">
                            <div class="text-center">
                                <h1 class="h4 text-gray-900 mb-4">Create Account</h1>
                            </div>
                        <form action="reg_code.php" method="POST">
                        <div class="form-group row">
                            <div class="col-sm-6 mb-3 mb-sm-0">
                                <input type="text" class="form-control form-control-user" id="first_name" name="first_name"placeholder="First Name">
                            </div>
                            <div class="col-sm-6">
                                <input type="text" class="form-control form-control-user" id="last_name" name="last_name" placeholder="Last Name">
                            </div>
                        </div>
                        <div class="form-group">
                            <input type="text" class="form-control form-control-user" id="staff_id" name="staff_id" placeholder="Staff ID">
                        </div>
                        <div class="form-group">
                            <input type="email" class="form-control form-control-user" id="email" name="email" placeholder="Email Address">
                        </div>
                        <div class="form-group">
                            <input type="tel" class="form-control form-control-user" id="contact_number" name="contact_number" placeholder="Contact Number">
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-6 mb-3 mb-sm-0">
                                <input type="password" class="form-control form-control-user" id="password" name="password"placeholder="Password">
                            </div>
                            <div class="col-sm-6">
                                <input type="password" class="form-control form-control-user" id="confirmPassword" name="confirmPassword" placeholder="Confirm Password">
                            </div>
                        </div>
                        <button type="submit" name="registerbtn" class="btn btn-primary btn-user btn-block">Register Account
                        </button>
                        <hr>
                    </form>

                            <div class="text-center">
                                <a class="small" href="forgot-password.html">Forgot Password?</a>
                            </div>
                            <div class="text-center">
                                <a class="small" href="login.html">Already have an account? Login</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    <!-- Display Confirmation Message -->
    <?php if (isset($_SESSION['success'])) : ?>
        <div class="alert alert-success" role="alert">
            <?php echo $_SESSION['success']; ?>
        </div>
    <?php unset($_SESSION['success']); // Clear the success message after displaying ?>
    <?php endif; ?>

<?php include('includes/script.php')?>
<?php include('includes/footer.php')?>