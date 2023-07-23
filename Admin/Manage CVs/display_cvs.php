<!DOCTYPE html>
<html>
<head>
    <title>Admin View CVs</title>
</head>
<body>
    <h1>Admin View CVs</h1>

    <form method="GET">
        Filter by Student Number: <input type="text" name="student_number" />
        <input type="submit" value="Filter" />
    </form>

    <?php
    // Assuming you have the database connection file included
    require_once('../../db_connection.php');
    session_start();

    // Check if the admin is logged in
   // if (isset($_SESSION['admin_id'])) {
        // Check if the form is submitted with a student_number filter
        if (isset($_GET['student_number'])) {
            $filterStudentNumber = $_GET['student_number'];
            // Sanitize the input to prevent SQL injection
            $filterStudentNumber = mysqli_real_escape_string($conn, $filterStudentNumber);

            // Fetch CVs filtered by student_number
            $stmt = $conn->prepare("SELECT * FROM cvs WHERE student_number = ?");
            $stmt->bind_param("s", $filterStudentNumber);
            $stmt->execute();
            $result = $stmt->get_result();
        } else {
            // Fetch all CVs if no filter is applied
            $result = $conn->query("SELECT * FROM cvs");
        }

        // Display the CVs
        if ($result->num_rows > 0) {
            echo '<ul>';
            while ($row = $result->fetch_assoc()) {
                $cvPath = $row['cv_path'];
                // Extract the filename from the path
                $fileName = basename($cvPath);
                echo '<li><a href="' . $cvPath . '" target="_blank">' . $fileName . '</a></li>';
            }
            echo '</ul>';
        } else {
            echo 'No CVs found.';
        }
   // } //else {
       // echo 'Please log in as an admin to view the CVs.';
   // }
    ?>
</body>
</html>
