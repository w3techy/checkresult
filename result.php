<?php 
require_once('config.php');
require_once('session.php'); // Ensures user is authenticated

$current_exam_number = $user_check;
$student_results = [];
$error_message = '';

$sql = "SELECT s.subject_name, r.score, r.grade, r.remarks
        FROM results r
        JOIN subjects s ON r.subject_id = s.subject_id
        WHERE r.exam_number = ?
        ORDER BY s.subject_name ASC";

$stmt = mysqli_prepare($conn, $sql);
if ($stmt) {
    mysqli_stmt_bind_param($stmt, "s", $current_exam_number);
    mysqli_stmt_execute($stmt);
    $results_query_result = mysqli_stmt_get_result($stmt);

    if ($results_query_result) {
        while ($row = mysqli_fetch_assoc($results_query_result)) {
            $student_results[] = $row;
        }
    } else {
        error_log("Results fetch error: " . mysqli_error($conn));
        $error_message = 'Unable to retrieve results at this moment. Please try again later.';
    }
    mysqli_stmt_close($stmt);
} else {
    error_log("Results prepare error: " . mysqli_error($conn));
    $error_message = 'Unable to retrieve results at this moment. Please try again later.';
}

require_once('header.php');
?>

<div class="result-container">
    <div class="result-header">
        <h2>Student Examination Result</h2>
        <p class="exam-number-badge">Exam Number: <strong><?php echo htmlspecialchars($current_exam_number); ?></strong></p>
    </div>

    <table class="data-table">
        <caption class="title">Academic Statement of Results</caption>
        <thead>
            <tr>
                <th>Subject</th>
                <th>Score</th>
                <th>Grade</th>
                <th>Remarks</th>
            </tr>
        </thead>
        <tbody>
        <?php
        if (!empty($error_message)) {
            echo '<tr><td colspan="4" class="table-error">' . htmlspecialchars($error_message) . '</td></tr>';
        } elseif (!empty($student_results)) {
            foreach ($student_results as $result_row) {
                echo '<tr>';
                echo '<td>' . htmlspecialchars($result_row['subject_name'] ?? '') . '</td>';
                echo '<td>' . htmlspecialchars((string)($result_row['score'] ?? '')) . '</td>';
                echo '<td>' . htmlspecialchars($result_row['grade'] ?? '') . '</td>';
                echo '<td>' . htmlspecialchars($result_row['remarks'] ?? '') . '</td>';
                echo '</tr>';
            }
        } else {
            echo '<tr><td colspan="4" class="table-empty">No results found for this exam number.</td></tr>';
        }
        ?>
        </tbody>
    </table>

    <div class="action-buttons">
        <button type="button" onclick="window.print()" class="button print-button">Print Result</button>
        <a href="logout.php" class="button logout-button">Logout</a>
    </div>
</div>

<?php 
require_once('footer.php');
?>
