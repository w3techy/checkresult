<?php
require_once(__DIR__ . '/config.php');

if (session_status() === PHP_SESSION_NONE) {
    if (!headers_sent()) {
        ini_set('session.cookie_httponly', 1);
        ini_set('session.use_only_cookies', 1);
    }
    session_start();
}

if (!isset($_SESSION['login_user']) || empty($_SESSION['login_user'])) {
    header("Location: index.php");
    exit();
}

$user_check = $_SESSION['login_user']; // exam_number

// Validate that user still exists in the database
$stmt = mysqli_prepare($conn, "SELECT exam_number FROM user WHERE exam_number = ?");
if ($stmt) {
    mysqli_stmt_bind_param($stmt, "s", $user_check);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    if (mysqli_stmt_num_rows($stmt) === 0) {
        mysqli_stmt_close($stmt);
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
        header("Location: index.php");
        exit();
    }
    mysqli_stmt_close($stmt);
}

$login_session = $user_check;
