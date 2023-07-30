<?php
session_start();

// Clear all session data
session_unset();
session_destroy();

// Redirect to the index.php page after logout
header("Location: index.php");
exit();
?>