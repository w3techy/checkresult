<?php
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include the main configuration file to get $conn
require_once('../config.php');

// Attempt to include site_meta.php, if it exists (after installation)
if (file_exists('../site_meta.php')) {
    require_once('../site_meta.php');
}

// Define defaults if constants are not set
if (!defined('SITE_NAME')) {
    define('SITE_NAME', 'Result Checker'); // Default if not installed
}
// SITE_DESCRIPTION is not directly used in admin header by default, but good practice if needed

// Basic security placeholder - redirect if not admin (future enhancement)
// if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
//     header("Location: ../index.php");
//     exit();
// }

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? htmlspecialchars($page_title) : "Admin"; ?> - <?php echo htmlspecialchars(SITE_NAME); ?> Admin</title>
    <link href="../style.css" rel="stylesheet" type="text/css"/>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; background-color: #f4f4f4; color: #333; }
        .admin-container { max-width: 960px; margin: 20px auto; padding: 20px; background-color: #fff; border: 1px solid #ddd; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .admin-nav { background-color: #333; color: #fff; padding: 10px 0; margin-bottom: 20px;}
        .admin-nav ul { list-style-type: none; padding: 0; margin: 0 auto; text-align: center; max-width: 960px;}
        .admin-nav ul li { display: inline; margin-right: 20px; }
        .admin-nav ul li a { color: #fff; text-decoration: none; font-weight: bold; }
        .admin-nav ul li a:hover { text-decoration: underline; }
        h1, h2, h3 { color: #333; }
        table.data-table { width: 100%; margin-top: 20px; border-collapse: collapse; }
        table.data-table th, table.data-table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        table.data-table th { background-color: #f0f0f0; }
        .message { padding: 10px; margin-bottom: 15px; border-radius: 4px; }
        .message.success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .message.error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; }
        .form-group input[type="text"], .form-group input[type="password"], .form-group select { width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        .button, input[type="submit"] { background-color: #5cb85c; color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; font-size: 1em; }
        .button:hover, input[type="submit"]:hover { background-color: #4cae4c; }
        /* Style for the main site's .button if needed */
        .button.main-site-style { background-color:#333333; color:#FFFFFF; padding:10px; text-decoration: none;}
    </style>
</head>
<body>

<div class="admin-nav">
    <ul>
        <li><a href="index.php">Admin Dashboard</a></li>
        <li><a href="manage_subjects.php">Manage Subjects</a></li>
        <li><a href="../index.php" target="_blank">View Main Site</a></li>
        <!-- Add other admin navigation links here -->
    </ul>
</div>

<div class="admin-container">
<!-- Main content of admin pages will go here -->
<?php
// Display flash messages (success/error)
if (isset($_SESSION['message'])) {
    echo '<div class="message success">' . htmlspecialchars($_SESSION['message']) . '</div>';
    unset($_SESSION['message']); // Clear the message after displaying
}
if (isset($_SESSION['error_message'])) { // Changed from 'error' to 'error_message' to avoid conflict if 'error' is used differently
    echo '<div class="message error">' . htmlspecialchars($_SESSION['error_message']) . '</div>';
    unset($_SESSION['error_message']); // Clear the error message after displaying
}
?>
