<?php
// INSTALLATION SCRIPT for Result Checker

// --- Security: Check if already installed ---
if (file_exists('install.lock')) {
    die("Installation Lock Active: Application appears to be already installed. Please remove 'install.lock' manually if you intend to reinstall (this is a destructive action if data exists).");
}
if (file_exists('config.php')) {
    // A config file exists. It's safer to assume installation is complete or partially complete.
    // For a fresh install, config.php should not exist.
    // The install.lock is the primary mechanism after a successful install.
    // This check is an additional safeguard.
    // die("Configuration File Found: 'config.php' already exists. Please remove it and 'install.lock' (if present) to attempt a fresh installation. Warning: This may lead to data loss if the database was already set up.");
}


// --- Part 2: Process Form Submission (DB Connection, Config Write) ---
$error_messages = [];
$success_message = ''; // This will be used for overall success at the end.
$db_config_written = false; // Flag to track if config.php was written

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_install'])) {
    // Retrieve POST data
    $db_host = trim($_POST['db_host']);
    $db_name = trim($_POST['db_name']);
    $db_user = trim($_POST['db_user']);
    $db_pass = trim($_POST['db_pass']); // Password can be empty

    $site_name = trim($_POST['site_name']);
    $site_description = trim($_POST['site_description']);

    // Validate required fields
    if (empty($db_host)) $error_messages[] = "Database Host is required.";
    if (empty($db_name)) $error_messages[] = "Database Name is required.";
    if (empty($db_user)) $error_messages[] = "Database Username is required.";
    // Password can be empty, so no validation for empty $db_pass
    if (empty($site_name)) $error_messages[] = "Site Name is required.";

    if (empty($error_messages)) {
        // 1. Attempt to connect to MySQL server (without selecting DB yet)
        $temp_conn = @mysqli_connect($db_host, $db_user, $db_pass);

        if (!$temp_conn) {
            $error_messages[] = "Failed to connect to MySQL server: " . mysqli_connect_error() .
                                ". Please check your Database Host, Username, and Password.";
        } else {
            // 2. Try to create the database if it doesn't exist
            $sql_create_db = "CREATE DATABASE IF NOT EXISTS `" . mysqli_real_escape_string($temp_conn, $db_name) . "`";
            if (!mysqli_query($temp_conn, $sql_create_db)) {
                $error_messages[] = "Failed to create database '" . htmlspecialchars($db_name) . "': " . mysqli_error($temp_conn);
            } else {
                // 3. Try to select the database
                if (!mysqli_select_db($temp_conn, $db_name)) {
                    $error_messages[] = "Database '" . htmlspecialchars($db_name) . "' created but could not be selected: " . mysqli_error($temp_conn);
                } else {
                    // 4. Database connection successful, DB created/selected. Now write config.php
                    $config_template_path = 'config-template.php';
                    if (!file_exists($config_template_path)) {
                        $error_messages[] = "CRITICAL ERROR: `config-template.php` not found. Cannot proceed.";
                    } elseif (!is_readable($config_template_path)) {
                        $error_messages[] = "CRITICAL ERROR: `config-template.php` is not readable. Check file permissions.";
                    } else {
                        $config_content = file_get_contents($config_template_path);
                        if ($config_content === false) {
                            $error_messages[] = "CRITICAL ERROR: Could not read `config-template.php`.";
                        } else {
                            // Replace placeholders
                            $config_content = str_replace('%%DB_HOST%%', $db_host, $config_content);
                            $config_content = str_replace('%%DB_USER%%', $db_user, $config_content);
                            $config_content = str_replace('%%DB_PASS%%', $db_pass, $config_content);
                            $config_content = str_replace('%%DB_NAME%%', $db_name, $config_content);

                            if (@file_put_contents('config.php', $config_content) === false) {
                                $error_messages[] = "ERROR: Could not write to `config.php`. " .
                                                    "Please check file permissions in the application root directory. " .
                                                    "You may need to create `config.php` manually with the following content: <pre>" .
                                                    htmlspecialchars($config_content) . "</pre>";
                            } else {
                                $db_config_written = true; // Signal that config is written
                                // $success_message = "Database configured and `config.php` written successfully.";
                                // Success message will be more comprehensive after all steps.
                                // Proceed to schema setup in the next step.
                            }
                        }
                    }
                }
            }
            mysqli_close($temp_conn); // Close temporary connection
        }
    }
}
// --- Part 3: Database Schema Setup ---
$db_schema_created = false; // Flag for this step

if ($db_config_written && empty($error_messages)) { // Proceed only if config was written and no prior errors
    // Now that config.php is written, we can include it to use $conn
    require_once('config.php');

    if (!$conn) {
        // This should ideally not happen if config.php was written correctly and DB was connectable
        $error_messages[] = "CRITICAL: `config.php` was written, but failed to connect to database using it. Please check `config.php` content and database status.";
    } else {
        // Helper function to execute SQL from a file
        function execute_sql_from_file($filepath, $connection, &$errors_array) {
            if (!file_exists($filepath)) {
                $errors_array[] = "SQL file not found: " . htmlspecialchars($filepath);
                return false;
            }
            if (!is_readable($filepath)){
                $errors_array[] = "SQL file not readable: " . htmlspecialchars($filepath) . ". Check permissions.";
                return false;
            }

            $sql_content = file_get_contents($filepath);
            if ($sql_content === false) {
                $errors_array[] = "Could not read SQL file: " . htmlspecialchars($filepath);
                return false;
            }

            // Basic SQL script splitting: split by semicolon, if not within quotes
            // More robust parsing might be needed for complex SQL with procedures/triggers or semicolons in comments/strings.
            // For simple DDL like ours, this should mostly work.
            $sql_commands = preg_split('/;(?![\s\S]*(?:\'|\").*;)/', $sql_content, -1, PREG_SPLIT_NO_EMPTY);

            $all_successful = true;
            foreach ($sql_commands as $command) {
                $command = trim($command);
                if (empty($command) || strpos(ltrim($command), '--') === 0 || strpos(ltrim($command), '#') === 0) {
                    continue; // Skip empty lines or comments
                }
                if (!mysqli_query($connection, $command)) {
                    $errors_array[] = "Error executing SQL command from " . htmlspecialchars($filepath) . ":<br><pre>" . htmlspecialchars($command) . "</pre><br>Error: " . mysqli_error($connection);
                    $all_successful = false;
                    // break; // Stop on first error or try all? Let's try all and report.
                }
            }
            return $all_successful;
        }

        $sql_files = ['sql/user.sql', 'sql/subjects.sql', 'sql/results.sql'];
        $schema_setup_successful = true;

        foreach ($sql_files as $sql_file) {
            if (!execute_sql_from_file($sql_file, $conn, $error_messages)) {
                $schema_setup_successful = false;
                // No break here, to collect all errors from all files if any
            }
        }

        if ($schema_setup_successful && empty($error_messages)) { // Re-check error_messages as execute_sql_from_file appends to it
            $db_schema_created = true;
            // $success_message .= " Database tables created successfully."; // Append to existing success or set later
        } else {
            if (empty($error_messages)) { // If schema_setup_successful is false but no specific errors were added by the function
                 $error_messages[] = "An unspecified error occurred during database schema setup.";
            }
        }
        // $conn is closed automatically at script end, or could be closed if not needed further in this script.
    }
}

// --- Part 4: Site Settings Storage ---
$site_meta_written = false; // Flag for this step

if ($db_schema_created && empty($error_messages)) { // Proceed only if schema was created and no prior errors
    $site_meta_content = "<?php\n";
    $site_meta_content .= "// Site Meta Configuration - Generated by Installer\n\n";
    $site_meta_content .= "define('SITE_NAME', '" . addslashes($site_name) . "');\n"; // $site_name from POST
    $site_meta_content .= "define('SITE_DESCRIPTION', '" . addslashes($site_description) . "');\n"; // $site_description from POST
    $site_meta_content .= "\n?>";

    if (@file_put_contents('site_meta.php', $site_meta_content) === false) {
        $error_messages[] = "ERROR: Could not write to `site_meta.php`. " .
                            "Please check file permissions in the application root directory. " .
                            "You may need to create `site_meta.php` manually with the appropriate content reflecting your site name and description.";
    } else {
        $site_meta_written = true;
        // $success_message .= " Site metadata written successfully."; // Append or set later
    }
}
// --- Part 5: Finalization & Security ---
$install_complete = false; // Flag for overall completion

if ($site_meta_written && empty($error_messages)) { // Proceed only if site_meta.php was written and no prior errors
    if (@file_put_contents('install.lock', 'Installation completed on: ' . date("Y-m-d H:i:s")) === false) {
        // Could not create lock file, installation is complete but show a warning.
        $success_message = "Installation Complete! However, the 'install.lock' file could not be created automatically. " .
                           "For security, please create an empty file named 'install.lock' in the application root directory, " .
                           "or delete 'install.php' manually.";
        // Even if lock file fails, we consider installation complete for user flow.
        $install_complete = true;
    } else {
        $success_message = "Installation Complete! 'install.lock' created. Your site is ready.";
        $install_complete = true;
    }
}


// --- Part 1: HTML Form (and display of any messages set above) ---
// The form should only be displayed if installation is NOT complete.
// The initial check for install.lock at the top handles subsequent page loads.
// This logic here handles the case where installation completes within the SAME request.

if (!$install_complete) { // Only show form if installation is not yet completed in this request
// The existing HTML form structure from <!DOCTYPE html> to </html> goes here.
// To achieve this with the replace tool, I will need to wrap the existing form.
// The following is a conceptual placement. The actual tool usage will be more complex.
// [Existing HTML Form code block will be wrapped by this if condition]
// For the purpose of this step, I'll assume the form is wrapped.
// The actual implementation will require moving the form into this conditional block.
}

/*
    The following shows how the HTML part should be structured:

    ... (PHP logic from Part 2, 3, 4, 5) ...

    <?php if (!$install_complete): ?>
        <!DOCTYPE html>
        ... all the form HTML ...
        </html>
    <?php else: ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <title>Installation Complete</title>
            <link href="style.css" rel="stylesheet" type="text/css"/>
            <style> ... minimal styles for success page ... </style>
        </head>
        <body>
            <div class="container">
                <h2>Installation Complete!</h2>
                <?php if (!empty($success_message)): ?>
                    <div class="success-message"><?php echo htmlspecialchars($success_message); ?></div>
                <?php endif; ?>
                <p><a href="index.php" class="button">Go to your website</a></p>
                <p style="margin-top: 20px; color: #777;">For security, it's recommended to delete the <code>install.php</code> file from your server.</p>
            </div>
        </body>
        </html>
    <?php endif; ?>

*/
// --- End of conceptual structure ---
// For now, I'll just add the PHP logic. The HTML conditional display will be a subsequent refinement if needed,
// or handled by the fact that if $install_complete is true, the script might effectively end after printing the success message
// if we structure it that way. The top check for install.lock handles future visits.

// The current `replace_with_git_merge_diff` tool is not ideal for conditionally showing/hiding large HTML blocks
// without re-pasting the entire block. I'll add the PHP logic for `install.lock` and success message.
// The visual part of hiding the form on success will be handled by the user refreshing or the top `install.lock` check.

// The success_message set above will be displayed by the existing message display logic
// at the top of the form if $install_complete is false.
// If $install_complete is true, we should ideally show a different page.
// Let's adjust the logic slightly: if install_complete, we will output a success page directly.

if ($install_complete) {
    // Clear any potential error messages if we reached full completion
    $error_messages = [];
}

// --- Part 1: HTML Form (and display of any messages set above) ---
// This section will be conditionally displayed based on $install_complete
// The actual HTML will be handled in a follow-up if this direct approach isn't clean.
// For now, the messages will appear above the form if it's re-displayed due to an error.
// If $install_complete is true, we will output a dedicated success page below.

?>
<?php if ($install_complete): ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Result Checker - Installation Complete</title>
    <link href="style.css" rel="stylesheet" type="text/css"/>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f4f4f4; color: #333; }
        .container { max-width: 700px; margin: auto; padding: 20px; background-color: #fff; border: 1px solid #ddd; box-shadow: 0 0 10px rgba(0,0,0,0.1); text-align: center; }
        h2 { color: #333; }
        .success-message { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; padding: 10px; margin-bottom: 15px; border-radius: 4px; text-align: left;}
        .button-link {
            display: inline-block;
            background-color: #5cb85c;
            color: white !important;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 4px;
            font-size: 1.1em;
            margin-top: 10px;
        }
        .button-link:hover { background-color: #4cae4c; }
        .security-note { margin-top: 30px; color: #777; font-size: 0.9em; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Installation Complete!</h2>
        <?php if (!empty($success_message)): ?>
            <div class="success-message"><?php echo nl2br(htmlspecialchars($success_message)); ?></div>
        <?php endif; ?>
        <p><a href="index.php" class="button-link">Go to Your Website</a></p>
        <p class="security-note">For enhanced security, please delete the <code>install.php</code> file from your server now.</p>
    </div>
</body>
</html>
<?php exit(); // Stop further script execution, do not display the form again ?>
<?php endif; ?>

<?php
// --- Original Part 1: HTML Form ---
// This will only be reached if $install_complete is false.
// The check for install.lock at the very top handles subsequent visits.
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Result Checker - Installation</title>
    <link href="style.css" rel="stylesheet" type="text/css"/> <!-- Link to existing style.css -->
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f4f4f4; color: #333; }
        .container { max-width: 700px; margin: auto; padding: 20px; background-color: #fff; border: 1px solid #ddd; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h2 { text-align: center; color: #333; }
        .form-section { margin-bottom: 25px; padding-bottom: 15px; border-bottom: 1px solid #eee; }
        .form-section:last-child { border-bottom: none; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group input[type="text"], .form-group input[type="password"] {
            width: calc(100% - 18px); /* Account for padding/border */
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .form-group input[type="submit"] {
            background-color: #5cb85c;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1.1em;
        }
        .form-group input[type="submit"]:hover { background-color: #4cae4c; }
        .error-message { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; padding: 10px; margin-bottom: 15px; border-radius: 4px;}
        .success-message { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; padding: 10px; margin-bottom: 15px; border-radius: 4px;}
        small { color: #777; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Result Checker Installation</h2>

        <?php if (!empty($error_messages)): ?>
            <div class="error-message">
                <strong>Please correct the following errors:</strong><br>
                <?php foreach ($error_messages as $msg): ?>
                    - <?php echo htmlspecialchars($msg); ?><br>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($success_message)): ?>
            <div class="success-message">
                <?php echo htmlspecialchars($success_message); ?>
            </div>
        <?php endif; ?>

        <form action="install.php" method="POST">
            <div class="form-section">
                <h3>1. Database Configuration</h3>
                <div class="form-group">
                    <label for="db_host">Database Host:</label>
                    <input type="text" name="db_host" id="db_host" value="<?php echo isset($_POST['db_host']) ? htmlspecialchars($_POST['db_host']) : 'localhost'; ?>" required>
                </div>
                <div class="form-group">
                    <label for="db_name">Database Name:</label>
                    <input type="text" name="db_name" id="db_name" value="<?php echo isset($_POST['db_name']) ? htmlspecialchars($_POST['db_name']) : 'result'; ?>" required>
                </div>
                <div class="form-group">
                    <label for="db_user">Database Username:</label>
                    <input type="text" name="db_user" id="db_user" value="<?php echo isset($_POST['db_user']) ? htmlspecialchars($_POST['db_user']) : 'root'; ?>" required>
                </div>
                <div class="form-group">
                    <label for="db_pass">Database Password:</label>
                    <input type="password" name="db_pass" id="db_pass" value="<?php echo isset($_POST['db_pass']) ? htmlspecialchars($_POST['db_pass']) : ''; ?>">
                </div>
            </div>

            <div class="form-section">
                <h3>2. Site Settings</h3>
                <div class="form-group">
                    <label for="site_name">Site Name:</label>
                    <input type="text" name="site_name" id="site_name" value="<?php echo isset($_POST['site_name']) ? htmlspecialchars($_POST['site_name']) : 'Result Checker'; ?>" required>
                </div>
                <div class="form-group">
                    <label for="site_description">Site Description: <small>(Optional)</small></label>
                    <input type="text" name="site_description" id="site_description" value="<?php echo isset($_POST['site_description']) ? htmlspecialchars($_POST['site_description']) : 'Online Student Result Portal'; ?>">
                </div>
            </div>

            <!-- Admin User Setup - Future Enhancement
            <div class="form-section">
                <h3>3. Administrator Account</h3>
                <div class="form-group">
                    <label for="admin_user">Admin Username:</label>
                    <input type="text" name="admin_user" id="admin_user" required>
                </div>
                <div class="form-group">
                    <label for="admin_pass">Admin Password:</label>
                    <input type="password" name="admin_pass" id="admin_pass" required>
                </div>
            </div>
            -->

            <div class="form-group">
                <input type="submit" name="submit_install" value="Install Now">
            </div>
        </form>
    </div>
</body>
</html>
