<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection file
require_once('../../db_connection.php');

// Function to retrieve student preferences from the database
function getStudentPreferences($conn) {
    $sql = "SELECT sp.student_number, sp.preference_id, p.preference_name 
            FROM stu_preference sp 
            INNER JOIN preferences p ON sp.preference_id = p.preference_id";
    $result = $conn->query($sql);

    $studentPreferences = array();
    while ($row = $result->fetch_assoc()) {
        $studentNumber = $row['student_number'];
        $preferenceId = $row['preference_id'];
        $preferenceName = $row['preference_name'];

        if (!isset($studentPreferences[$studentNumber])) {
            $studentPreferences[$studentNumber] = array();
        }
        $studentPreferences[$studentNumber][$preferenceId] = $preferenceName;
    }

    $result->free_result();

    return $studentPreferences;
}

// Function to retrieve company preferences from the database
function getCompanyPreferences($conn) {
    $sql = "SELECT cp.companyID, cp.preference_id, cp.num_cvs_requested, p.preference_name 
            FROM comp_preference cp 
            INNER JOIN preferences p ON cp.preference_id = p.preference_id";
    $result = $conn->query($sql);

    $companyPreferences = array();
    while ($row = $result->fetch_assoc()) {
        $companyId = $row['companyID'];
        $preferenceId = $row['preference_id'];
        $numCVsRequested = $row['num_cvs_requested'];
        $preferenceName = $row['preference_name'];

        if (!isset($companyPreferences[$companyId])) {
            $companyPreferences[$companyId] = array();
        }
        $companyPreferences[$companyId][] = array(
            'preference_id' => $preferenceId,
            'num_cvs_requested' => $numCVsRequested,
            'preference_name' => $preferenceName
        );
    }

    $result->free_result();

    return $companyPreferences;
}

// Function to check if a student has been selected for any preference
function isStudentSelected($conn, $studentNumber) {
    $sql = "SELECT selected_companyID FROM assigned_preferences WHERE student_number = '$studentNumber' AND selected_companyID IS NOT NULL";
    $result = $conn->query($sql);
    return $result->num_rows > 0;
}

// // Function to retrieve unassigned student numbers
// function getUnassignedStudentNumbers($conn) {
//     $sql = "SELECT student_number FROM assigned_preferences WHERE selected_companyID IS NULL";
//     $result = $conn->query($sql);

//     $unassignedStudents = array();
//     while ($row = $result->fetch_assoc()) {
//         $unassignedStudents[] = $row['student_number'];
//     }

//     $result->free_result();

//     return $unassignedStudents;
// }



// Add the following function to retrieve the list of student numbers that have already been selected
function getSelectedStudentNumbers($conn) {
    $sql = "SELECT DISTINCT student_number FROM assigned_preferences WHERE selected_companyID IS NOT NULL";
    $result = $conn->query($sql);

    $selectedStudentNumbers = array();
    while ($row = $result->fetch_assoc()) {
        $selectedStudentNumbers[] = $row['student_number'];
    }

    $result->free_result();

    return $selectedStudentNumbers;
}

// Retrieve student preferences
$studentPreferences = getStudentPreferences($conn);

// Retrieve company preferences
$companyPreferences = getCompanyPreferences($conn);

// Match preferences
$matchedPreferences = array();

// Get the list of student numbers that have already been selected
$selectedStudentNumbers = getSelectedStudentNumbers($conn);

// Modify the matching process to ignore the selected student numbers
foreach ($companyPreferences as $companyId => $preferences) {
    // Iterate through each preference of the company
    foreach ($preferences as $companyPreference) {
        $preferenceId = $companyPreference['preference_id'];
        $numCVsRequested = $companyPreference['num_cvs_requested'];

        // Retrieve students who have selected the current preference and have not been selected already
        $matchingStudents = array_keys(array_filter($studentPreferences, function($studentPreference) use ($preferenceId, $conn, $selectedStudentNumbers) {
            return isset($studentPreference[$preferenceId]) && !in_array($studentPreference, $selectedStudentNumbers);
        }));

        // Check if there are enough students available for the current preference
        if (count($matchingStudents) >= $numCVsRequested) {
            // Assign specified number of CVs to the company from available students
            $assignedStudents = array_slice($matchingStudents, 0, $numCVsRequested);

            // Store matched preference
            $matchedPreferences[] = array(
                'companyID' => $companyId,
                'preferenceID' => $preferenceId,
                'studentNumbers' => $assignedStudents,
                'numCVsAssigned' => $numCVsRequested
            );
        } else if (count($matchingStudents) > 0) {
            // Assign all available students to the company and update remaining number of CVs requested
            $assignedStudents = $matchingStudents;
            $numCVsAssigned = count($matchingStudents);

            // Store matched preference
            $matchedPreferences[] = array(
                'companyID' => $companyId,
                'preferenceID' => $preferenceId,
                'studentNumbers' => $assignedStudents,
                'numCVsAssigned' => $numCVsAssigned
            );

            // Update remaining number of CVs requested for the company
            $numCVsRequested -= $numCVsAssigned;

            // Remove assigned students from the available students list
            foreach ($assignedStudents as $studentNumber) {
                unset($studentPreferences[$studentNumber][$preferenceId]);
            }

            // Assign remaining CVs from other preferences based on student priority
            while ($numCVsRequested > 0) {
                $remainingStudents = array_filter($studentPreferences, function($studentPreference) {
                    return !empty($studentPreference);
                });

                // Sort remaining students based on priority (if available)
                uasort($remainingStudents, function($a, $b) {
                    return count($a) <=> count($b);
                });

                // Get the student with the highest priority (fewest remaining preferences)
                $remainingStudent = reset($remainingStudents);

                // Get the preference of the remaining student with the fewest remaining students
                $remainingPreferenceId = key($remainingStudent);

                // Get the student number of the remaining student
                $remainingStudentNumber = key($remainingStudents);

                // Assign the remaining student to the company
                if (isset($studentPreferences[$remainingStudentNumber][$remainingPreferenceId])) {
                    $assignedStudents[] = $remainingStudentNumber;
                    $numCVsAssigned++;
                    $numCVsRequested--;

                    // Update matched preference
                    $matchedPreferences[count($matchedPreferences) - 1]['studentNumbers'][] = $remainingStudentNumber;
                    $matchedPreferences[count($matchedPreferences) - 1]['numCVsAssigned'] = $numCVsAssigned;

                    // Remove assigned student from the available students list
                    unset($studentPreferences[$remainingStudentNumber][$remainingPreferenceId]);

                    // If the remaining student has no more preferences, remove them from the list
                    if (empty($studentPreferences[$remainingStudentNumber])) {
                        unset($studentPreferences[$remainingStudentNumber]);
                    }
                }
            }
        }
    }
}

// Store matched preferences in the mapped_preference table
$stmt = $conn->prepare("INSERT INTO mapped_preference (companyID, preference_id, student_number) VALUES (?, ?, ?)");
if ($stmt) {
    foreach ($matchedPreferences as $matchedPreference) {
        $companyID = $matchedPreference['companyID'];
        $preferenceID = $matchedPreference['preferenceID'];
        $studentNumbers = $matchedPreference['studentNumbers'];

        foreach ($studentNumbers as $studentNumber) {
            // Bind the parameters and execute the statement
            $stmt->bind_param("sss", $companyID, $preferenceID, $studentNumber);
            $stmt->execute();
        }
    }

    // Close the prepared statement
    $stmt->close();
} else {
    echo "Failed to prepare the statement.";
}

// Close the database connection
$conn->close();
?>
