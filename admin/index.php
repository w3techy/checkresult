<?php
$page_title = "Admin Dashboard";
require_once('admin_header.php');

// Fetch counts for dashboard overview
$user_count = 0;
$subject_count = 0;
$result_count = 0;

if ($res = mysqli_query($conn, "SELECT COUNT(*) AS total FROM user")) {
    $row = mysqli_fetch_assoc($res);
    $user_count = $row['total'] ?? 0;
}

if ($res = mysqli_query($conn, "SELECT COUNT(*) AS total FROM subjects")) {
    $row = mysqli_fetch_assoc($res);
    $subject_count = $row['total'] ?? 0;
}

if ($res = mysqli_query($conn, "SELECT COUNT(*) AS total FROM results")) {
    $row = mysqli_fetch_assoc($res);
    $result_count = $row['total'] ?? 0;
}
?>

<h2>Admin Dashboard</h2>
<p>Welcome, <strong><?php echo htmlspecialchars($_SESSION['admin_user'] ?? 'Administrator'); ?></strong>. Manage your school results portal from here.</p>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin: 25px 0;">
    <div style="background: #e7f1ff; border: 1px solid #b6d4fe; border-radius: 6px; padding: 20px; text-align: center;">
        <h3 style="margin: 0; color: #084298; font-size: 2.2rem;"><?php echo (int)$user_count; ?></h3>
        <p style="margin: 5px 0 0; color: #084298; font-weight: 500;">Registered Students</p>
    </div>
    <div style="background: #d1e7dd; border: 1px solid #badbcc; border-radius: 6px; padding: 20px; text-align: center;">
        <h3 style="margin: 0; color: #0f5132; font-size: 2.2rem;"><?php echo (int)$subject_count; ?></h3>
        <p style="margin: 5px 0 0; color: #0f5132; font-weight: 500;">Configured Subjects</p>
    </div>
    <div style="background: #fff3cd; border: 1px solid #ffecb5; border-radius: 6px; padding: 20px; text-align: center;">
        <h3 style="margin: 0; color: #664d03; font-size: 2.2rem;"><?php echo (int)$result_count; ?></h3>
        <p style="margin: 5px 0 0; color: #664d03; font-weight: 500;">Recorded Results</p>
    </div>
</div>

<h3>Quick Actions</h3>
<ul style="line-height: 2; font-size: 15px;">
    <li><a href="manage_subjects.php" style="color: #0d6efd; font-weight: 600;">Manage Examination Subjects</a></li>
    <li><a href="../index.php" target="_blank" style="color: #0d6efd;">Open Public Student Portal</a></li>
</ul>

<?php require_once('admin_footer.php'); ?>
