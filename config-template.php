<?php
// Database Configuration
// Replace the placeholder values below with your actual database credentials during installation.

$db_host = '%%DB_HOST%%';         // Server Name (e.g., localhost)
$db_user = '%%DB_USER%%';         // Database Username (e.g., root)
$db_pass = '%%DB_PASS%%';         // Database Password
$db_name = '%%DB_NAME%%';         // Database Name

// Attempt to connect to MySQL database
$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

// Check connection
if (!$conn) {
    // If connection fails, output error message and terminate script.
    // This is critical because the rest of the application depends on this connection.
    // In a live environment, you might want to log this error instead of outputting directly,
    // or show a more user-friendly error page.
    die ('Failed to connect to MySQL: ' . mysqli_connect_error() .
         '<br/>Please check your database credentials in config.php.');
}

// Optional: Set character set to utf8 (recommended)
if (!mysqli_set_charset($conn, "utf8")) {
    // printf("Error loading character set utf8: %s\n", mysqli_error($conn));
    // In a live environment, handle this more gracefully.
}

// The $conn variable is now available for use in other PHP scripts that include this file.
?>
