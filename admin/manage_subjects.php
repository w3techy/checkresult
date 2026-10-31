<?php
$page_title = "Manage Subjects";
require_once('admin_header.php');

// Handle Subject Creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_subject'])) {
    $submitted_token = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['admin_csrf_token'], $submitted_token)) {
        $_SESSION['error_message'] = "Security validation failed. Please try again.";
        header("Location: manage_subjects.php");
        exit();
    }

    $subject_name = trim($_POST['subject_name'] ?? '');

    if (empty($subject_name)) {
        $_SESSION['error_message'] = "Subject name cannot be empty.";
    } else {
        $stmt = mysqli_prepare($conn, "INSERT INTO subjects (subject_name) VALUES (?)");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "s", $subject_name);
            try {
                if (mysqli_stmt_execute($stmt)) {
                    $_SESSION['message'] = 'Subject "' . htmlspecialchars($subject_name) . '" added successfully!';
                }
            } catch (mysqli_sql_exception $e) {
                if ($e->getCode() === 1062 || mysqli_stmt_errno($stmt) === 1062) {
                    $_SESSION['error_message'] = 'Error: Subject "' . htmlspecialchars($subject_name) . '" already exists.';
                } else {
                    error_log("Failed to insert subject: " . $e->getMessage());
                    $_SESSION['error_message'] = "Database error while adding subject.";
                }
            }
            mysqli_stmt_close($stmt);
        } else {
            error_log("Failed to prepare subject insert: " . mysqli_error($conn));
            $_SESSION['error_message'] = "Database query preparation failed.";
        }
    }

    header("Location: manage_subjects.php");
    exit();
}

// Fetch Existing Subjects
$subjects = [];
$error_message = '';

try {
    $sql_select = "SELECT subject_id, subject_name FROM subjects ORDER BY subject_name ASC";
    $result_select = mysqli_query($conn, $sql_select);

    if ($result_select) {
        while ($row = mysqli_fetch_assoc($result_select)) {
            $subjects[] = $row;
        }
    }
} catch (mysqli_sql_exception $e) {
    error_log("Error fetching subjects: " . $e->getMessage());
    $error_message = "Unable to load subjects list at this time.";
}
?>

<h3>Existing Subjects</h3>

<?php if (!empty($error_message)): ?>
    <div class="message error"><?php echo htmlspecialchars($error_message); ?></div>
<?php endif; ?>

<?php if (empty($subjects) && empty($error_message)): ?>
    <p style="color: #6c757d;">No subjects found. Add a new subject below.</p>
<?php elseif (!empty($subjects)): ?>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 80px;">ID</th>
                <th>Subject Name</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($subjects as $subject): ?>
                <tr>
                    <td><?php echo (int)$subject['subject_id']; ?></td>
                    <td><?php echo htmlspecialchars($subject['subject_name']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<hr style="margin: 35px 0; border: 0; border-top: 1px solid #dee2e6;">

<h3>Add New Subject</h3>
<form action="manage_subjects.php" method="POST" style="max-width: 500px;">
    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['admin_csrf_token']); ?>">
    <div class="form-group">
        <label for="subject_name">Subject Name:</label>
        <input type="text" name="subject_name" id="subject_name" placeholder="e.g. Further Mathematics" required>
    </div>
    <div class="form-group">
        <input type="submit" name="add_subject" value="Add Subject" class="btn-submit">
    </div>
</form>

<?php require_once('admin_footer.php'); ?>
