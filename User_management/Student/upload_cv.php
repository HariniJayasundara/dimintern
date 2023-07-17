<!DOCTYPE html>
<html>
<head>
    <title>Upload CV</title>
</head>
<body>
    <?php
    // Check if the form is submitted
    if(isset($_POST['submit'])) {
        // Check if a file is selected
        if(isset($_FILES['cv']) && $_FILES['cv']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['cv'];
            
            // Check if the uploaded file is a PDF
            $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if($fileExtension === 'pdf') {
                // Set the destination directory to store CVs
                $destination = 'cv_uploads/' . $file['name'];
                
                // Move the uploaded file to the destination directory
                if(move_uploaded_file($file['tmp_name'], $destination)) {
                    // File uploaded successfully
                    echo 'CV uploaded successfully.';
                    
                    // Store the CV path in the database (assuming you have a MySQL connection established)
                    $cvPath = $destination; // Change this if you store the path differently
                    $studentId = 12345; // Replace with the student's ID
                    $sql = "INSERT INTO cvs (student_id, cv_path) VALUES ('$studentId', '$cvPath')";
                    // Execute the SQL query
                    
                    // Additional logic for admin actions and feedback
                    // ...
                } else {
                    echo 'Failed to move the uploaded file.';
                }
            } else {
                echo 'Only PDF files are allowed.';
            }
        } else {
            echo 'Please select a file to upload.';
        }
    }
    ?>
    
    <h1>Upload CV</h1>
    
    <form method="POST" enctype="multipart/form-data">
        <input type="file" name="cv" accept=".pdf" required />
        <br /><br />
        <input type="submit" name="submit" value="Upload" />
    </form>
</body>
</html>
