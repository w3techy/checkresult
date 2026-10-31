<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

unset($_SESSION['is_admin']);
unset($_SESSION['admin_user']);
unset($_SESSION['admin_id']);
unset($_SESSION['admin_csrf_token']);

header("Location: login.php");
exit();
