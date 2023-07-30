<!DOCTYPE html>
<html>
<head>
    <title>Admin Registration</title>
    <!-- Add Bootstrap CSS link -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <style>
        body{
            background-color: #343a40;
        }

        .container {
        background-color: white;
        max-width: 600px; 
        margin: 0 auto; 
        }

        footer {
          background-color: #03a68d;
          color: white;
          text-align: center;
          padding: 20px;
          margin: 0px;
        }

        .footer-content {
          font-size: 14px;
        }

        .btn {
            padding: 10px 20px;
            background-color: #03a68d;
            border: none;
            color: white;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            margin-left: 200px;
            margin-bottom: 5px;
                }
        .clear-button {
            padding: 10px 20px;
            background-color: #6d6e6d;
            border: none;
            color: white;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            margin-left: 20px;
            margin-right: 100px;
            margin-bottom: 5px;
                }

        .required::after {
            content: "*";
            color: red;
        }
        
    </style>

</head>
<body>
    <main>
        <div class="container">
            <h2 class="text-center">Admin Registration</h2>
            <form id="adminRegistrationForm" action="process_admin.php" method="post">
                <div class="form-group">
                    <label for="first_name" class="required">First Name:</label>
                    <input type="text" id="first_name" name="first_name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="last_name" class="required">Last Name:</label>
                    <input type="text" id="last_name" name="last_name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="staff_id" class="required">Staff ID:</label>
                    <input type="text" id="staff_id" name="staff_id" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="email" class="required">Email:</label>
                    <input type="email" id="email" name="email" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="contact_number" class="required">Contact Number:</label>
                    <input type="tel" id="contact_number" name="contact_number" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="password" class="required">Password:</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="confirmPassword" class="required">Confirm Password:</label>
                    <input type="password" id="confirmPassword" name="confirmPassword" class="form-control" required>
                </div>

                <div class="form-group">
                    <button type="button" onclick="clearForm()" class="clear-button">Clear</button>
                    <button type="submit" value="Create Account" class="btn">Create Account</button>
                </div>
            </form>
        </div>

        <footer>
            <div class="footer-content">
                    Department of Industrial Management - Faculty of Science - University of Kelaniya
                </div>
        </footer>
    </main>

    <!-- Add Bootstrap and jQuery scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script type="text/javascript">
        function clearForm() {
        document.getElementById("email").value = "";
        document.getElementById("contact_number").value = "";
        document.getElementById("first_name").value = "";
        document.getElementById("last_name").value = "";
        document.getElementById("staff_id").value = "";
        document.getElementById("password").value = "";
        document.getElementById("confirmPassword").value = "";
        }

        document.getElementById("adminRegistrationForm").addEventListener("submit", function(event) {
            event.preventDefault(); // Prevent form submission

            // Get form input values
            var first_name = document.getElementById("first_name").value;
            var last_name = document.getElementById("last_name").value;
            var staff_id = document.getElementById("staff_id").value;
            var contact_number = document.getElementById("contact_number").value;
            var email = document.getElementById("email").value;
            var password = document.getElementById("password").value;
            var confirmPassword = document.getElementById("confirmPassword").value;
            
            // Validation logic for mandatory fields
            if (first_name === "" || last_name === "" || staff_id === "" || contact_number === "" || email === "") {
            alert("All mandatory fields must be filled out.");
            return false;
            }

            // Validation logic for email format
            var emailPattern = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
            if (!emailPattern.test(email)) {
                alert("Please enter a valid email address.");
                return false;
            }

            // Validation logic for phone number format
            var phonePattern = /^\d{10}$/;
            if (!phonePattern.test(contact_number)) {
                alert("Please enter a valid phone number with 10 digits.");
                return false;
            }

            // Validate if passwords match
            if (password !== confirmPassword){
            alert("Passwords do not match");
            return false;
            }

            // // If all client side validations pass
            // return true;
            // }

            // If client-side validation passes, submit the form to the server
            $.ajax({
                url: "process_admin.php",
                method: "POST",
                data: $("#adminRegistrationForm").serialize(),
                success: function(response) {
                    // Handle the server response
                    if (response.success) {
                        showAlert("success", response.message);
                        window.location.href = "../login.html";
                    } else {
                        // Display error alert
                        showAlert("danger", response.message);
                    }
                },
                error: function() {
                    // Display error alert for AJAX request failure
                    showAlert("danger", "Error: Failed to process the request.");
                }
            });
        });

        function showAlert(type, message) {
            // Create Bootstrap alert dynamically
            var alertElement = '<div class="alert alert-' + type + ' alert-dismissible fade show" role="alert">' +
                message +
                '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                '<span aria-hidden="true">&times;</span>' +
                '</button>' +
                '</div>';

            // Append alert to the page
            $(".container").prepend(alertElement);

            // Auto-dismiss the alert after 5 seconds
            $(".alert").delay(5000).fadeOut("slow", function() {
                $(this).remove();
            });
        }
    </script>
</body>
</html>
