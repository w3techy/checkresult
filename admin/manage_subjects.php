<?php
$page_title = "Manage Subjects";
// The header includes session_start() and config.php
// It also has logic to display $_SESSION['message'] and $_SESSION['error_message']
require_once('admin_header.php');

// --- Part 2: Process Form Submission ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_subject'])) {
    $subject_name = trim($_POST['subject_name']);

    // Validate input
    if (empty($subject_name)) {
        $_SESSION['error_message'] = "Subject name cannot be empty.";
    } else {
        // Sanitize input
        $sanitized_subject_name = mysqli_real_escape_string($conn, $subject_name);
        // No need for htmlspecialchars before DB insert, but good for display if not using prepared statements for select.
        // However, subject_name in DB should be raw, htmlspecialchars on output.

        // Construct INSERT query
        $sql_insert_subject = "INSERT INTO subjects (subject_name) VALUES ('$sanitized_subject_name')";

        if (mysqli_query($conn, $sql_insert_subject)) {
            $_SESSION['message'] = "Subject \"".htmlspecialchars($subject_name)."\" added successfully!";
        } else {
            // Check for duplicate entry specifically
            if (mysqli_errno($conn) == 1062) { // 1062 is the MySQL error code for duplicate entry
                $_SESSION['error_message'] = "Error: Subject \"".htmlspecialchars($subject_name)."\" already exists.";
            } else {
                $_SESSION['error_message'] = "Database error: " . mysqli_error($conn);
            }
        }
    }
    // Redirect to the same page using GET to prevent form resubmission and display flash messages
    header("Location: manage_subjects.php");
    exit();
}

// --- Part 1: Display Subjects and Form (logic from admin_header displays session messages) ---

// Fetch existing subjects
// This local $error_message is for errors during fetching the list, not from form submission.
$error_message_fetch = '';
$subjects = [];
$sql_select_subjects = "SELECT subject_id, subject_name FROM subjects ORDER BY subject_name ASC";
$result_select_subjects = mysqli_query($conn, $sql_select_subjects);

if ($result_select_subjects) {
    while ($row = mysqli_fetch_assoc($result_select_subjects)) {
        $subjects[] = $row;
    }
} else {
    // This error will be displayed via admin_header.php if set to $_SESSION['error_message']
    // For a direct error on this page before implementing full POST->Redirect->GET:
    $error_message = "Error fetching subjects: " . mysqli_error($conn);
}

?>

<h3>Existing Subjects</h3>
<?php
// Display any direct error messages from fetching
if (!empty($error_message) && !isset($_SESSION['error_message'])) {
    echo '<div class="message error">' . htmlspecialchars($error_message) . '</div>';
}
?>
<?php if (empty($subjects) && empty($error_message) && !isset($_SESSION['error_message'])): ?>
    <p>No subjects found. Add some below.</p>
<?php elseif (!empty($subjects)): ?>
    <table class="data-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Subject Name</th>
                <!-- <th>Actions</th> -->
            </tr>
        </thead>
        <tbody>
            <?php foreach ($subjects as $subject): ?>
                <tr>
                    <td><?php echo htmlspecialchars($subject['subject_id']); ?></td>
                    <td><?php echo htmlspecialchars($subject['subject_name']); ?></td>
                    <!-- <td>Edit | Delete</td> -->
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<hr style="margin-top: 30px; margin-bottom: 30px;">

<h3>Add New Subject</h3>
<form action="manage_subjects.php" method="POST">
    <div class="form-group">
        <label for="subject_name">Subject Name:</label>
        <input type="text" name="subject_name" id="subject_name" required>
    </div>
    <div class="form-group">
        <input type="submit" name="add_subject" value="Add Subject" class="button">
    </div>
</form>

<?php require_once('admin_footer.php'); ?>
