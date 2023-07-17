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

// ...

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
            foreach ($preferences as $preferenceId => $selected) {
                // Check if the preference is selected
                if ($selected) {
                    $numCVs = $_POST['num_cvs'][$preferenceId];

                    // Check if a valid number of CVs is entered
                    if (is_numeric($numCVs)) {
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
            /* CSS styles */
        </style>
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
                    <input type="checkbox" name="preferences[<?php echo $preferenceId; ?>]" value="1">
                    <?php echo $preferenceName; ?>
                </label>
                <br>
                <?php
            }
            ?>

            <h2>Select number of CVs:</h2>
            <?php
            // Reset the result pointer to fetch preferences again
            $result->data_seek(0);
            while ($row = $result->fetch_assoc()) {
                $preferenceId = $row['preference_id'];
                ?>
                <label>
                    Number of CVs for <?php echo $row['preference_name']; ?>:
                    <input type="number" name="num_cvs[<?php echo $preferenceId; ?>]" min="0" required>
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
