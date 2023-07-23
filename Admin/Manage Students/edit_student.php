<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection file
require_once('../../db_connection.php');

// Retrieve the student number from the URL parameter
$studentNumber = $_GET['studentNumber'];

// Fetch the student details from the database
$query = "SELECT * FROM student WHERE student_number = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $studentNumber);

if ($stmt->execute()) {
    $result = $stmt->get_result();
    $student = $result->fetch_assoc();
} else {
    echo "Error retrieving student details: " . $stmt->error;
    exit();
}

// Check if the student exists
if (!$student) {
    echo "Student not found.";
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {   // Retrieve the form data
    $name_with_initials = $_POST['name_with_initials'];
    $preferred_name = $_POST['preferred_name'];
    $email = $_POST['email'];
    $phone_number = $_POST['phone_number'];
    $specialization = $_POST['specialization'];
    $current_gpa = $_POST['current_gpa'];
    $qualifications = $_POST['qualifications'];
    $special_achievements = $_POST["special_achievements"];
    $extra_curricular_activities = $_POST['extra_curricular_activities'];
    $linkedin_account = $_POST['linkedin_account'];

    // Validate the form data
    // Add your validation logic here
    $errors = [];

    // Validate phone number (10 digits)
    if (!preg_match('/^\d{10}$/', $phone_number)) {
        $errors[] = 'Phone number should have exactly 10 digits.';
    }

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email format.';
    }
// Add this code to check $_POST data
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";



    // Check for any validation errors
    if (!empty($errors)) {
        foreach ($errors as $error) {
            echo $error . "<br>";
        }
        exit();
    }

// Update the student details
$query = "UPDATE student
          SET preferred_name = ?,
              email = ?,
              phone_number = ?,
              specialization = ?,
              current_gpa = ?,
              qualifications = ?,
              special_achievements = ?,
              extra_curricular_activities = ?,
              linkedin_account = ?
          WHERE student_number = ?";

$stmt = $conn->prepare($query);
if ($stmt === false) {
    echo "Error in SQL query: " . $conn->error;
    exit();
}

// Construct the $types string for bind_param
$types = "ssssssssss"; // For all possible fields (preferred_name, email, phone_number, specialization, current_gpa, qualifications, special_achievements, extra_curricular_activities, linkedin_account, student_number)

// Add the student data and student number to the $bindings array
$bindings = array(
    $preferred_name,
    $email,
    $phone_number,
    $specialization,
    $current_gpa,
    $qualifications,
    $special_achievements,
    $extra_curricular_activities,
    $linkedin_account,
    $studentNumber // Add the student number parameter to the $bindings array
);

// Bind the parameters using the dynamically generated $types string and $bindings array
$stmt->bind_param($types, ...$bindings);

if ($stmt->execute()) {
    // Student details updated successfully
    echo "<script>alert('Student details updated successfully.')</script>";
    echo "<script>window.location.href = 'manage_students.html';</script>";
} else {
    // Error updating student details
    echo "<script>alert('Error updating student details: " . $stmt->error . "')</script>";
    echo "<script>window.location.href = 'edit_student.php?studentNumber=" . $studentNumber . "';</script>";
}

// Close the prepared statement
$stmt->close();
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Student</title>
    <style type="text/css">
        body {
  font-family: Arial, sans-serif;
  background-color: #f5f5f5;
  padding: 20px;
  margin: 0;
}

h1 {
  font-size: 24px;
  color: #333;
  margin-bottom: 20px;
}

form {
  max-width: 400px;
  margin: 0 auto;
  background-color: #fff;
  padding: 20px;
  border-radius: 6px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

label {
  display: block;
  font-weight: bold;
  margin-bottom: 6px;
  color: #555;
}

input[type="text"],
input[type="email"],
input[type="tel"],
input[type="number"],
textarea {
  width: 100%;
  padding: 8px;
  border: 1px solid #ccc;
  border-radius: 4px;
  box-sizing: border-box;
  margin-bottom: 12px;
  font-size: 14px;
}

textarea {
  resize: vertical;
}

input[type="submit"] {
  background-color: #03a68d;
  color: white;
  border: none;
  padding: 10px 16px;
  font-size: 16px;
  border-radius: 4px;
  cursor: pointer;
}

input[type="submit"]:hover {
  background-color: #b45156;
}

input:read-only {
  background-color: #03a68d;
}


    </style>
    <script>
        function validateForm() {
            // Validate phone number (10 digits)
            var phoneNumberInput = document.getElementById("phone_number");
            var phoneNumber = phoneNumberInput.value;
            var phoneRegex = /^\d{10}$/;
            if (!phoneRegex.test(phoneNumber)) {
                alert("Phone number should contain 10 digits.");
                phoneNumberInput.focus();
                return false;
            }

            // Validate email format
            var emailInput = document.getElementById("email");
            var email = emailInput.value;
            var emailRegex = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
            if (!emailRegex.test(email)) {
                alert("Please enter a valid email address.");
                emailInput.focus();
                return false;
            }

            // Validate mandatory fields
            var mandatoryFields = ["preferred_name", "phone_number", "specialization", "current_gpa"];
            for (var i = 0; i < mandatoryFields.length; i++) {
                var field = document.getElementById(mandatoryFields[i]);
                if (field.value.trim() === "") {
                    alert("Please fill in all mandatory fields.");
                    field.focus();
                    return false;
                }
            }

            return true;
        }
    </script>
</head>
<body>
    <h1>Edit Student Details</h1>
    <form method="POST" action="" onsubmit="return validateForm();">
        <label for="name_with_initials">Name with Initials:</label>
        <input type="text" id="name_with_initials" name="name_with_initials" value="<?php echo $student['name_with_initials']; ?>" readonly><br>

        <label for="preferred_name">Preferred Name:</label>
        <input type="text" id="preferred_name" name="preferred_name" value="<?php echo $student['preferred_name']; ?>" required><br>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?php echo $student['email']; ?>" required><br>

        <label for="phone_number">Phone Number:</label>
        <input type="tel" id="phone_number" name="phone_number" value="<?php echo $student['phone_number']; ?>" required><br>

        <label for="specialization">Specialization:</label>
        <input type="text" id="specialization" name="specialization" value="<?php echo $student['specialization']; ?>" required><br>

        <label for="current_gpa">Current GPA:</label>
        <input type="number" id="current_gpa" name="current_gpa" step="0.01" value="<?php echo $student['current_gpa']; ?>" required><br>

        <label for="qualifications">Qualifications:</label>
        <textarea id="qualifications" name="qualifications"><?php echo $student['qualifications']; ?></textarea><br>

        <label for="special_achievements">Special Achievements:</label>
        <textarea id="special_achievements" name="special_achievements"><?php echo $student['special_achievements']; ?></textarea><br>

        <label for="extra_curricular_activities">Extra Curricular Activities:</label>
        <textarea id="extra_curricular_activities" name="extra_curricular_activities"><?php echo $student['extra_curricular_activities']; ?></textarea><br>

        <label for="linkedin_account">LinkedIn Account:</label>
        <input type="text" id="linkedin_account" name="linkedin_account" value="<?php echo $student['linkedin_account']; ?>"><br>

        <input type="submit" value="Submit">
    </form>
</body>
</html>