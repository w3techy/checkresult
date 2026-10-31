<?php
require_once("config.php");

if (session_status() === PHP_SESSION_NONE) {
    if (!headers_sent()) {
        ini_set('session.cookie_httponly', 1);
        ini_set('session.use_only_cookies', 1);
    }
    session_start();
}

// Redirect if already logged in
if (isset($_SESSION['login_user']) && !empty($_SESSION['login_user'])) {
    header("Location: result.php");
    exit();
}

$error = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $myid = trim($_POST['examno'] ?? '');
    $mypin = trim($_POST['pin'] ?? '');

    if (empty($myid) || empty($mypin)) {
        $error = "Please enter both Exam Number and PIN.";
    } else {
        $stmt = mysqli_prepare($conn, "SELECT exam_number, pin FROM user WHERE exam_number = ?");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "s", $myid);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if ($result && $row = mysqli_fetch_assoc($result)) {
                $pin_verified = false;

                // Check hashed PIN
                if (password_verify($mypin, $row['pin'])) {
                    $pin_verified = true;
                } elseif ($mypin === $row['pin']) {
                    // Transparent upgrade of legacy plain-text PIN to secure hash
                    $pin_verified = true;
                    $new_hash = password_hash($mypin, PASSWORD_DEFAULT);
                    $upgrade_stmt = mysqli_prepare($conn, "UPDATE user SET pin = ? WHERE exam_number = ?");
                    if ($upgrade_stmt) {
                        mysqli_stmt_bind_param($upgrade_stmt, "ss", $new_hash, $myid);
                        mysqli_stmt_execute($upgrade_stmt);
                        mysqli_stmt_close($upgrade_stmt);
                    }
                }

                if ($pin_verified) {
                    session_regenerate_id(true);
                    $_SESSION['login_user'] = $row['exam_number'];
                    header("Location: result.php");
                    exit();
                } else {
                    $error = "Your Exam Number or PIN is invalid.";
                }
            } else {
                $error = "Your Exam Number or PIN is invalid.";
            }
            mysqli_stmt_close($stmt);
        } else {
            error_log("Database query preparation failed: " . mysqli_error($conn));
            $error = "A system error occurred. Please try again later.";
        }
    }
}

include("header.php");
?>

<div class="login-wrapper">
    <div class="login-card">
        <div class="login-header">
            <h2>Student Portal Login</h2>
        </div>
        <div class="login-body">
            <?php if (!empty($error)): ?>
                <div class="message error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form action="index.php" method="post">
                <div class="form-group">
                    <label for="examno">Exam Number:</label>
                    <input type="text" id="examno" name="examno" class="box" value="<?php echo htmlspecialchars($_POST['examno'] ?? ''); ?>" required autofocus />
                </div>
                <div class="form-group">
                    <label for="pin">PIN:</label>
                    <input type="password" id="pin" name="pin" class="box" required />
                </div>
                <div class="form-group" style="text-align: center; margin-top: 20px;">
                    <input type="submit" value="Check Result Now" class="button" />
                </div>
            </form>
        </div>
    </div>
</div>

<?php
include("footer.php");
?>
