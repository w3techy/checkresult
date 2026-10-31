<?php
// Attempt to include site_meta.php, if it exists
if (file_exists(__DIR__ . '/site_meta.php')) {
    require_once(__DIR__ . '/site_meta.php');
}

// Define defaults if constants are not set
if (!defined('SITE_NAME')) {
    define('SITE_NAME', 'Result Checker');
}
if (!defined('SITE_DESCRIPTION')) {
    define('SITE_DESCRIPTION', 'Check your results online.');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="<?php echo htmlspecialchars(SITE_DESCRIPTION); ?>">
    <meta name="theme-color" content="#333333"/>
    <title><?php echo htmlspecialchars(SITE_NAME); ?> - Student Portal</title>
    <link href="style.css" rel="stylesheet" type="text/css"/>
</head>
<body>
<header class="site-header">
    <h1><?php echo htmlspecialchars(SITE_NAME); ?></h1>
</header>
