<?php
// Attempt to include site_meta.php, if it exists (after installation)
if (file_exists('site_meta.php')) {
    require_once('site_meta.php');
}

// Define defaults if constants are not set (e.g., before installation or if site_meta.php is missing)
if (!defined('SITE_NAME')) {
    define('SITE_NAME', 'Result Checker');
}
if (!defined('SITE_DESCRIPTION')) {
    define('SITE_DESCRIPTION', 'Check your results online.');
}
?>
<html>
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<meta name="description" content="<?php echo htmlspecialchars(SITE_DESCRIPTION); ?>">
	<meta name="theme-color" content="wheat"/>
	<title><?php echo htmlspecialchars(SITE_NAME); ?> - Student Portal</title>
<link href="style.css" rel="stylesheet" type="text/css"/>
</head>
<body>
<header style="background-color: #333; color: #fff; padding: 10px 0; text-align: center;">
    <h1><?php echo htmlspecialchars(SITE_NAME); ?></h1>
</header>