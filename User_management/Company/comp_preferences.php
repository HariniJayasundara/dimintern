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

<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if the company is logged in
if (!isset($_SESSION['email'])) {
    // Redirect to the login page or display an error message
    $_SESSION['error_message'] = "You are not logged in.";
    header("Location: login.php"); // Update 'login.php' with your actual login page URL
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
    $_SESSION['error_message'] = "Error retrieving company information.";
    header("Location: company_dashboard.php");
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
            // Set success message to be displayed later
            $_SESSION['success_message'] = "Preferences updated successfully.";
        } else {
            // Set error message to be displayed later
            $_SESSION['error_message'] = "Error updating preferences " . $conn->error;
        }
    }

        // Redirect back to the same page after processing the form
        header("Location: comp_preferences.php");
        exit;
    }
}

// Fetch all preferences
$sql = "SELECT * FROM preferences";
$result = $conn->query($sql);

// Retrieve the company's previous preferences and number of CVs requested
$previousPreferencesQuery = "SELECT preference_id, num_cvs_requested FROM comp_preference WHERE companyID = '$companyID'";
$previousPreferencesResult = $conn->query($previousPreferencesQuery);

// Create an array to store the company's previous preferences and CVs requested
$previousPreferences = array();
while ($row = $previousPreferencesResult->fetch_assoc()) {
    $previousPreferences[$row['preference_id']] = $row['num_cvs_requested'];
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Company Portal - Preferences</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style type="text/css">
        body {
            background-color: #f2f2f2;
        }

        .card {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin: 20px 0;
        }

        .btn {
            padding: 10px 20px;
            background-color: #51b4af;
            border: none;
            color: white;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
                }

        h1{
            font-size: 32px;
        }

        h2{
            font-size: 16px;
        }
            
            /* Style to shorten the input box for number of CVs */
            .form-control {
                max-width: 50%;
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

    <div class="container" style="padding-bottom: 70px;">
        <?php
        // Display success message (if set)
        if (isset($_SESSION['success_message'])) {
            ?>
            <div class="alert alert-success" role="alert">
                <?php echo $_SESSION['success_message']; ?>
            </div>
            <?php
            // Clear the success message from session after displaying it
            unset($_SESSION['success_message']);
        }

        // Display error message (if set)
        if (isset($_SESSION['error_message'])) {
            ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $_SESSION['error_message']; ?>
            </div>
            <?php
            // Clear the error message from session after displaying it
            unset($_SESSION['error_message']);
        }
        ?>

        <h1 class="mb-4">Select Preferences and Number of CVs</h1>

        <form  action="comp_preferences.php" method="POST">
            <h2>(Previous selections are pre-selected)</h2><br>
            <?php
            while ($row = $result->fetch_assoc()) {
                $preferenceId = $row['preference_id'];
                $preferenceName = $row['preference_name'];
                ?>

                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="preferences[<?php echo $preferenceId; ?>]" value="1" onchange="toggleNumCVsInput('<?php echo $preferenceId; ?>')" <?php if (isset($previousPreferences[$preferenceId])) echo 'checked'; ?>>
                    <label class="form-check-label"> <b>
                        <?php echo $preferenceName; ?>
                    </label> </b>
                </div>
                <div class="form-group">
                    <label>Number of CVs:</label>
                    <input type="number" class="form-control" name="num_cvs[<?php echo $preferenceId; ?>]" id="num_cvs_<?php echo $preferenceId; ?>" min="0" <?php if (isset($previousPreferences[$preferenceId])) echo 'value="' . $previousPreferences[$preferenceId] . '"'; ?> <?php if (!isset($previousPreferences[$preferenceId])) echo 'disabled'; ?>> <br>
                </div>
                <?php
            }
            ?>
            <button type="submit" class="btn">Submit</button>
        </form>
    </div>

    <footer>
        <div class="footer-content">
            Department of Industrial Management - Faculty of Science - University of Kelaniya
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.25.0/dist/js/bootstrap-icons.min.js"></script>
    <script src="https://kit.fontawesome.com/your-font-awesome-kit.js" crossorigin="anonymous"></script>

</body>
</html>