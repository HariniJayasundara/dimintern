<?php
session_start();
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection file
require_once('../../db_connection.php');

// Check if the company is logged in
if (!isset($_SESSION['email'])) {
    // Redirect to the login page or display an error message
    echo "You are not logged in.";
    exit;
}

// Retrieve the companyID using the logged-in email
$email = $_SESSION['email'];
$companyID = '';

// Fetch the companyID from the company table based on the email
$sql = "SELECT companyID FROM company WHERE email = '$email' LIMIT 1";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $companyID = $row['companyID'];
} else {
    // Handle the case where the companyID is not found for the email
    echo "Error retrieving company information.";
    exit;
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if preferences are selected
    if (isset($_POST['preferences']) && is_array($_POST['preferences'])) {
        $preferences = $_POST['preferences'];

        // Delete existing preferences for the company
        $deleteSql = "DELETE FROM comp_preference WHERE companyID = '$companyID'";
        if ($conn->query($deleteSql) === TRUE) {
            // Prepare an array to accumulate the number of CVs requested for each preference
            $numCVsRequested = array();

            // Iterate over the selected preferences
            foreach ($preferences as $preferenceId => $value) {
                // Check if the preference is selected and a valid number of CVs is entered
                if ($value == 1 && isset($_POST['num_cvs'][$preferenceId]) && is_numeric($_POST['num_cvs'][$preferenceId])) {
                    $numCVs = $_POST['num_cvs'][$preferenceId];
                    // Check if the preference already exists in the array
                    if (isset($numCVsRequested[$preferenceId])) {
                        // If it exists, accumulate the number of CVs requested
                        $numCVsRequested[$preferenceId] += $numCVs;
                    } else {
                        // If it doesn't exist, set the number of CVs requested
                        $numCVsRequested[$preferenceId] = $numCVs;
                    }
                }
            }

            // Insert new preferences for the company
            $insertSql = "INSERT INTO comp_preference (companyID, preference_id, num_cvs_requested) VALUES ";
            $values = array();
            foreach ($numCVsRequested as $preferenceId => $numCVs) {
                $values[] = "('$companyID', '$preferenceId', $numCVs)";
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
    }
}


// Fetch all preferences
$sql = "SELECT * FROM preferences";
$result = $conn->query($sql);

if ($result) {
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Company Portal - Preferences</title>
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
  display: flex;
  align-items: center;
  margin-bottom: 20px;
}

label {
  display: inline-block;
  margin-right: 10px;
  font-weight: bold;
}

input[type="text"],
input[type="number"],
select,
textarea {
  flex: 1;
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
        <script>
            function toggleNumCVsInput(checkboxId) {
                var numCVsInput = document.getElementById('num_cvs_' + checkboxId);
                numCVsInput.disabled = !numCVsInput.disabled;
            }
        </script>
    </head>
    <body>
        <h1>Select Preferences and Number of CVs</h1>

        <form action="comp_preferences.php" method="POST">
            <h2>Select your preferences:</h2>
            <?php
            while ($row = $result->fetch_assoc()) {
                $preferenceId = $row['preference_id'];
                $preferenceName = $row['preference_name'];

                ?>
                <label>
                    <input type="checkbox" name="preferences[<?php echo $preferenceId; ?>]" value="1" onchange="toggleNumCVsInput('<?php echo $preferenceId; ?>')">
                    <?php echo $preferenceName; ?>
                </label>
                <br>
                <label>
                    Number of CVs:
                    <input type="number" name="num_cvs[<?php echo $preferenceId; ?>]" id="num_cvs_<?php echo $preferenceId; ?>" min="0" disabled>
                </label>
                <br><br>
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
