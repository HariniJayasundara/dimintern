<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection file
require_once('../../db_connection.php');

// Fetch all preferences
$sql = "SELECT preference_id, preference_name FROM preferences";
$result = $conn->query($sql);

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Add preference
    if (isset($_POST['add_preference'])) {
        $preferenceName = $_POST['preference_name'];

        // Check if preference name is provided
        if (!empty($preferenceName)) {
            // Insert preference into the preferences table
            $sql = "INSERT INTO preferences (preference_name) VALUES ('$preferenceName')";
            if ($conn->query($sql) === TRUE) {
                echo "Preference added successfully.";
            } else {
                echo "Error adding preference: " . $conn->error;
            }
        } else {
            echo "Please provide a preference name.";
        }
    }
    // Remove preference
if (isset($_POST['remove_preference'])) {
    $preferenceId = $_POST['preference_id'];

    // Delete preference from the preferences table
    $sql = "DELETE FROM preferences WHERE preference_id = '$preferenceId'";
    if ($conn->query($sql) === TRUE) {
        echo "Preference removed successfully.";
    } else {
        echo "Error removing preference: " . $conn->error;
    }
}

    
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Portal - Preferences</title>
</head>
<body>
    <h1>Manage Preferences</h1>

    <h2>Add Preference</h2>
    <form action="preferences.php" method="POST">
        <label>
            Preference Name:
            <input type="text" name="preference_name" required>
        </label>
        <button type="submit" name="add_preference">Add Preference</button>
    </form>

    <h2>Remove Preference</h2>
    <form action="preferences.php" method="POST">
        <label>
            Select Preference:
            <select name="preference_id" required>
                <option value="" disabled selected>Select Preference</option>
                <?php
                // Populate the select dropdown with existing preferences
                if ($result) {
                    while ($row = $result->fetch_assoc()) {
                        $preferenceId = $row['preference_id'];
                        $preferenceName = $row['preference_name'];

                        echo "<option value='$preferenceId'>$preferenceName</option>";
                    }
                } else {
                    echo "Error fetching preferences: " . $conn->error;
                }
                ?>
            </select>
        </label>
        <button type="submit" name="remove_preference">Remove Preference</button>
    </form>
</body>
</html>
