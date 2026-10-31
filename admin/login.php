<?php
require_once('../config.php');

if (session_status() === PHP_SESSION_NONE) {
    if (!headers_sent()) {
        ini_set('session.cookie_httponly', 1);
        ini_set('session.use_only_cookies', 1);
    }
    session_start();
}

// Redirect if already logged in as admin
if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true) {
    header("Location: index.php");
    exit();
}

// CSRF token generation
if (empty($_SESSION['admin_csrf_token'])) {
    $_SESSION['admin_csrf_token'] = bin2hex(random_bytes(32));
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $submitted_token = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['admin_csrf_token'], $submitted_token)) {
        $error = "Security validation failed. Please try again.";
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($username) || empty($password)) {
            $error = "Please enter both username and password.";
        } else {
            $stmt = mysqli_prepare($conn, "SELECT admin_id, username, password FROM admin_users WHERE username = ?");
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "s", $username);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);

                if ($result && $row = mysqli_fetch_assoc($result)) {
                    if (password_verify($password, $row['password'])) {
                        session_regenerate_id(true);
                        $_SESSION['is_admin'] = true;
                        $_SESSION['admin_user'] = $row['username'];
                        $_SESSION['admin_id'] = $row['admin_id'];
                        // Regenerate token for next actions
                        $_SESSION['admin_csrf_token'] = bin2hex(random_bytes(32));

                        header("Location: index.php");
                        exit();
                    } else {
                        $error = "Invalid username or password.";
                    }
                } else {
                    $error = "Invalid username or password.";
                }
                mysqli_stmt_close($stmt);
            } else {
                error_log("Admin login prepare failed: " . mysqli_error($conn));
                $error = "An error occurred. Please try again later.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Result Checker</title>
    <link href="../style.css" rel="stylesheet" type="text/css"/>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background-color: #f4f6f9; margin: 0; padding: 0; }
        .login-box { max-width: 400px; margin: 80px auto; background: #fff; border-radius: 8px; box-shadow: 0 4px 16px rgba(0,0,0,0.08); overflow: hidden; border: 1px solid #e1e4e8; }
        .login-header { background: #212529; color: #fff; padding: 20px; text-align: center; }
        .login-header h2 { margin: 0; font-size: 1.3rem; }
        .login-body { padding: 25px; }
        .form-group { margin-bottom: 18px; }
        .form-group label { display: block; margin-bottom: 6px; font-weight: 600; color: #495057; font-size: 14px; }
        .form-group input[type="text"], .form-group input[type="password"] {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 14px;
        }
        .form-group input[type="submit"] {
            width: 100%;
            padding: 12px;
            background-color: #0d6efd;
            border: none;
            color: #fff;
            font-size: 15px;
            font-weight: 600;
            border-radius: 4px;
            cursor: pointer;
        }
        .form-group input[type="submit"]:hover { background-color: #0b5ed7; }
        .message-error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; padding: 10px 14px; border-radius: 4px; margin-bottom: 18px; font-size: 14px; }
        .back-link { display: block; text-align: center; margin-top: 15px; font-size: 13px; color: #6c757d; text-decoration: none; }
        .back-link:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="login-box">
        <div class="login-header">
            <h2>Result Checker Admin</h2>
        </div>
        <div class="login-body">
            <?php if (!empty($error)): ?>
                <div class="message-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form action="login.php" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['admin_csrf_token']); ?>" />
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" required autofocus />
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required />
                </div>
                <div class="form-group">
                    <input type="submit" value="Sign In" />
                </div>
            </form>
            <a href="../index.php" class="back-link">&larr; Return to Student Portal</a>
        </div>
    </div>
</body>
</html>
