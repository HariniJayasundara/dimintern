<?php include('includes/header.php')?>
<?php include('includes/navbar.php')?>

		<!-- Button trigger modal -->
<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
  Launch demo modal
</button>

<!-- Modal -->
<div class="modal fade" id="adminProfile" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="exampleModalLabel">Add Admin Data</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
          <span aria-hidden= "true">&times;</span>
        </button>
      </div>

      <form action="reg_code.php" method="POST">
      <div class="modal-body">
          <div class="form-group">
            <label for="first_name">First Name:</label>
            <input type="text" id="first_name" name="first_name" placeholder="Enter your First Name" required>
            <br>
          </div>

          <div class="form-group">
            <label for="last_name">Last Name:</label>
            <input type="text" id="last_name" name="last_name" placeholder="Enter your Last Name" required>
            <br>
          </div>

          <div class="form-group">
            <label for="staff_id">Staff ID:</label>
            <input type="text" id="staff_id" name="staff_id" placeholder="Enter your Staff ID" required>
            <br>
          </div>

          <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" placeholder="Enter your Email" required>
            <br>
          </div>

          <div class="form-group">
            <label for="contact_number">Contact Number:</label>
            <input type="tel" id="contact_number" name="contact_number" placeholder="Enter your Contact Number" required>
            <br>
          </div>

          <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" placeholder="Enter your Password" required>
            <br>
          </div>

          <div class="form-group">
            <label for="confirm_password">Confirm Password:</label>
            <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm your Password" required>
            <br>
          </div>

        </div>

        <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" name="registerbtn" class="btn btn-primary">Save</button>
        </div>

        <div class="text-center">
          <a class="small" href="forgot-password.html">Forgot Password?</a>
        </div>
        <div class="text-center">
          <a class="small" href="login.html">Already have an account? Login!</a>
        </div>

        </form>
      
    </div>
  </div>
</div>






<?php include('includes/script.php')?>
<?php include('includes/footer.php')?>