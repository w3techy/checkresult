<?php
// Database Configuration
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'result';

$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
if (!$conn) {
    error_log('Database connection error: ' . mysqli_connect_error());
    die('Database connection error. Please verify your configuration in config.php.');
}

mysqli_set_charset($conn, 'utf8mb4');
