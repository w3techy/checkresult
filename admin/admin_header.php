<?php
if (session_status() === PHP_SESSION_NONE) {
    if (!headers_sent()) {
        ini_set('session.cookie_httponly', 1);
        ini_set('session.use_only_cookies', 1);
    }
    session_start();
}

// Strict Admin Authentication Check
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: login.php");
    exit();
}

// Include configuration and database connection
require_once(__DIR__ . '/../config.php');

// Include site metadata if available
if (file_exists(__DIR__ . '/../site_meta.php')) {
    require_once(__DIR__ . '/../site_meta.php');
}

if (!defined('SITE_NAME')) {
    define('SITE_NAME', 'Result Checker');
}

// CSRF token generation for admin forms
if (empty($_SESSION['admin_csrf_token'])) {
    $_SESSION['admin_csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? htmlspecialchars($page_title) : "Admin"; ?> - <?php echo htmlspecialchars(SITE_NAME); ?> Admin</title>
    <link href="../style.css" rel="stylesheet" type="text/css"/>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; margin: 0; padding: 0; background-color: #f4f6f9; color: #333; }
        .admin-nav { background-color: #212529; color: #fff; padding: 12px 20px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .admin-nav .brand { font-size: 1.15rem; font-weight: bold; color: #fff; text-decoration: none; }
        .admin-nav ul { list-style: none; padding: 0; margin: 0; display: flex; align-items: center; gap: 20px; }
        .admin-nav ul li { display: inline; }
        .admin-nav ul li a { color: #dee2e6; text-decoration: none; font-size: 14px; font-weight: 500; transition: color 0.2s; }
        .admin-nav ul li a:hover { color: #fff; text-decoration: none; }
        .admin-nav ul li .user-badge { color: #ffc107; font-size: 13px; margin-right: 8px; }
        .admin-container { max-width: 1000px; margin: 30px auto; padding: 25px 30px; background-color: #fff; border: 1px solid #e1e4e8; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); }
        h1, h2, h3 { color: #212529; margin-top: 0; }
        table.data-table { width: 100%; margin-top: 20px; border-collapse: collapse; }
        table.data-table th, table.data-table td { border: 1px solid #dee2e6; padding: 12px 14px; text-align: left; }
        table.data-table th { background-color: #f8f9fa; font-weight: 600; color: #495057; }
        .message { padding: 12px 16px; margin-bottom: 20px; border-radius: 4px; font-size: 14px; }
        .message.success { background-color: #d1e7dd; color: #0f5132; border: 1px solid #badbcc; }
        .message.error { background-color: #f8d7da; color: #842029; border: 1px solid #f5c2c7; }
        .form-group { margin-bottom: 18px; }
        .form-group label { display: block; margin-bottom: 6px; font-weight: 600; color: #495057; }
        .form-group input[type="text"], .form-group input[type="password"] { width: 100%; padding: 10px 12px; border: 1px solid #ced4da; border-radius: 4px; box-sizing: border-box; font-size: 14px; }
        .btn-submit { background-color: #198754; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; font-size: 14px; font-weight: 600; }
        .btn-submit:hover { background-color: #157347; }
        .nav-logout { background-color: #dc3545; color: #fff !important; padding: 6px 14px; border-radius: 4px; }
        .nav-logout:hover { background-color: #bb2d3b; }
    </style>
</head>
<body>

<div class="admin-nav">
    <a href="index.php" class="brand"><?php echo htmlspecialchars(SITE_NAME); ?> Admin</a>
    <ul>
        <li><span class="user-badge">User: <?php echo htmlspecialchars($_SESSION['admin_user'] ?? 'Admin'); ?></span></li>
        <li><a href="index.php">Dashboard</a></li>
        <li><a href="manage_subjects.php">Manage Subjects</a></li>
        <li><a href="../index.php" target="_blank">Student Portal &rarr;</a></li>
        <li><a href="logout.php" class="nav-logout">Logout</a></li>
    </ul>
</div>

<div class="admin-container">
<?php
// Display flash messages
if (isset($_SESSION['message'])) {
    echo '<div class="message success">' . htmlspecialchars($_SESSION['message']) . '</div>';
    unset($_SESSION['message']);
}
if (isset($_SESSION['error_message'])) {
    echo '<div class="message error">' . htmlspecialchars($_SESSION['error_message']) . '</div>';
    unset($_SESSION['error_message']);
}
?>
