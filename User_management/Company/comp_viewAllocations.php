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
    <title>View Interns</title>
    <!-- Include Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style type="text/css">
        body {
            background-color: #f2f2f2;
        }

        .assignments-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        h1 {
            text-align: center;
            
        }

        #assignments-container {
      margin-top: 10px;
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
    <!-- Navbar -->
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


    <h1 class="mt-4">Current Allocation Status</h1>
    <div class="container pb-5">
        <div id="assignments-container">Loading...</div>
    </div>

    <!-- Footer -->
    <footer>
        <div class="footer-content">
            Department of Industrial Management - Faculty of Science - University of Kelaniya
        </div>
    </footer>

    <!-- Include Bootstrap JS and jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        $(document).ready(function () {
            // Make an AJAX request to fetch student assignments
            $.ajax({
                url: 'comp_viewInterns.php',
                type: 'POST',
                dataType: 'json',
                success: function (data) {
                    if (data.response_status === 'success') {
                        // Display student assignments and status options on the page
                        updateAssignments(data);
                    } else {
                        // Display error message if data retrieval fails
                        $('#assignments-container').html('<div class="alert alert-danger" role="alert">Error: ' + data.message + '</div>');
                    }
                },
                error: function () {
                    $('#assignments-container').html('<div class="alert alert-danger" role="alert">Error: Failed to fetch student assignments.</div>');
                },
            });

            function updateAssignments(data) {
                var assignments = data.assignments;
                var statuses = data.statuses;
                var assignmentsList = '';

                for (var i = 0; i < assignments.length; i++) {
                    var studentNumber = assignments[i].student_number;
                    var preferenceId = assignments[i].preference_id;
                    var preferenceName = assignments[i].preference_name;
                    var currentStatus = assignments[i].current_status;

                    // Create a dropdown with status options
                    var statusOptions = '';
                    for (var statusId in statuses) {
                        statusOptions +=
                            '<option value="' +
                            statusId +
                            '"' +
                            (statusId === currentStatus ? ' selected' : '') +
                            '>' +
                            statuses[statusId] +
                            '</option>';
                    }

                    // Construct the assignment card
                    assignmentsList += '<div class="assignment-card">';
                    assignmentsList +=
                        '<p class="student-number">Student Number: ' +
                        studentNumber +
                        '</p>';
                    assignmentsList += '<p>Preference ID: ' + preferenceId + '</p>';
                    assignmentsList +=
                        '<p>Preference Name: ' + preferenceName + '</p>';
                    assignmentsList += '<p>Current Status: ';
                    // Add data-preference attribute to store the preference_id
                    assignmentsList +=
                        '<select class="status-dropdown form-select" data-student="' +
                        studentNumber +
                        '" data-preference="' +
                        preferenceId +
                        '">' +
                        statusOptions +
                        '</select>';
                    assignmentsList += '</p>';
                    assignmentsList += '</div>';
                }

                $('#assignments-container').html(assignmentsList);

                // Attach event handler for status change
                $('.status-dropdown').change(function () {
                    var studentNumber = $(this).data('student');
                    var preferenceId = $(this).data('preference'); // Get the preference_id
                    var newStatus = $(this).val();

                    // Make an AJAX request to update the current_status
                    $.ajax({
                        url: 'comp_updateStatus.php',
                        type: 'POST',
                        data: {
                            student_number: studentNumber,
                            new_status: newStatus,
                            preference_id: preferenceId, // Include the preference_id in the request
                        },
                        dataType: 'json',
                        success: function (response) {
                            if (response.response_status === 'success') {
                                // Show success alert using Bootstrap
                                var alertMessage = 'Status updated successfully!';
                                var alertHtml = '<div class="alert alert-success" role="alert">' + alertMessage + '</div>';
                                $(alertHtml).appendTo('#assignments-container');
                            } else {
                                // Show error alert using Bootstrap
                                var errorMessage = 'Error: ' + response.message;
                                var errorHtml = '<div class="alert alert-danger" role="alert">' + errorMessage + '</div>';
                                $(errorHtml).appendTo('#assignments-container');
                            }
                        },
                        error: function () {
                            // Show error alert using Bootstrap
                            var errorMessage = 'Error: Failed to update status.';
                            var errorHtml = '<div class="alert alert-danger" role="alert">' + errorMessage + '</div>';
                            $(errorHtml).appendTo('#assignments-container');
                        },
                    });
                });
            }
        });
    </script>


</body>
</html>