<?php
// process_admin.php

// Include the database connection file
require_once "../db_connection.php";

// Function to sanitize input data
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize the form input
    $first_name = sanitize_input($_POST["first_name"]);
    $last_name = sanitize_input($_POST["last_name"]);
    $staff_id = sanitize_input($_POST["staff_id"]);
    $email = sanitize_input($_POST["email"]);
    $contact_number = sanitize_input($_POST["contact_number"]);
    $password = $_POST["password"]; // Password hashing will be done later
    $confirmPassword = $_POST["confirmPassword"];

    // Insert data into the database
    try {
        // You should hash the password before storing it in the database for security
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Prepare the SQL query to insert data into the "admin" table
        $sql = "INSERT INTO admin (first_name, last_name, staff_id, email, contact_number, password) VALUES (?, ?, ?, ?, ?, ?)";

        // Prepare and execute the statement
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssss", $first_name, $last_name, $staff_id, $email, $contact_number, $hashed_password);
        $stmt->execute();

        // Check if the query was successful
        if ($stmt->affected_rows > 0) {
            // Return a success response
            $response = array(
                "success" => true,
                "message" => "Admin registration successful!"
            );
        } else {
            // Return an error response if the insertion failed
            $response = array(
                "success" => false,
                "message" => "Error: Failed to insert data into the database."
            );
        }

        // Close the statement
        $stmt->close();
    } catch (Exception $e) {
        // Return an error response if an exception occurs
        $response = array(
            "success" => false,
            "message" => "Error: " . $e->getMessage()
        );
    }

    // Close the database connection
    $conn->close();

    // Send the JSON response back to the frontend
    header("Content-type: application/json");
    echo json_encode($response);
}