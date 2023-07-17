<?php
session_start();
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection file
require_once('../../db_connection.php');

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if preferences are selected
    if (isset($_POST['preferences']) && is_array($_POST['preferences']) && count($_POST['preferences']) >= 3 && count($_POST['preferences']) <= 5) {
        // Assuming the email is stored in $_SESSION['email']
        if (isset($_SESSION['email'])) {
            $email = $_SESSION['email'];

            // Fetch the student number based on the email
            $studentQuery = "SELECT student_number FROM student WHERE email = '$email'";
            $studentResult = $conn->query($studentQuery);

            if ($studentResult && $studentResult->num_rows > 0) {
                $studentRow = $studentResult->fetch_assoc();
                $student_number = $studentRow['student_number'];

                // Delete existing preferences for the student
                $deleteSql = "DELETE FROM stu_preference WHERE student_number = '$student_number'";
                if ($conn->query($deleteSql) === TRUE) {
                    // Insert new preferences for the student
                    $insertSql = "INSERT INTO stu_preference (student_number, preference_id) VALUES ";
                    $values = array();
                    foreach ($_POST['preferences'] as $preference) {
                        $values[] = "('$student_number', '$preference')";
                    }
                    $insertSql .= implode(", ", $values);

                    if ($conn->query($insertSql) === TRUE) {
                        echo "Preferences updated successfully.";
                    } else {
                        echo "Error updating preferences: " . $conn->error;
                    }
                } else {
                    echo "Error deleting existing preferences: " . $conn->error;
                }
            } else {
                echo "Error fetching student information: " . $conn->error;
            }
        } else {
            echo "Email not found in session data.";
        }
    } else {
        echo "Please select a minimum of 3 and a maximum of 5 preferences.";
    }
}

// Fetch all preferences
$sql = "SELECT preference_id, preference_name FROM preferences";
$result = $conn->query($sql);

if ($result) {
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Student Portal - Preferences</title>
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
        <h1>Select Preferences</h1>

        <form action="stu_preference.php" method="POST">
            <h2>Select your preferences:</h2>
            <?php
            while ($row = $result->fetch_assoc()) {
                $preferenceId = $row['preference_id'];
                $preferenceName = $row['preference_name'];

                ?>
                <label>
                    <input type="checkbox" name="preferences[]" value="<?php echo $preferenceId; ?>">
                    <?php echo $preferenceName; ?>
                </label>
                <br>
                <?php
            }
            ?>

            <button type="submit">Submit</button>
        </form>
    </body>
    </html>
    <?php
} else {
    echo "Error fetching preferences: " . $conn->error;
}

// Close the database connection
$conn->close();
?>
