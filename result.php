<?php 
  require ('config.php');
  require ('session.php'); // Ensures user is logged in and $user_check (exam_number) is available.

  // $user_check from session.php holds the logged-in user's exam_number.
  $current_exam_number = $user_check;

  $results_query_sql = "SELECT s.subject_name, r.score, r.grade, r.remarks
                        FROM results r
                        JOIN subjects s ON r.subject_id = s.subject_id
                        WHERE r.exam_number = '".mysqli_real_escape_string($conn, $current_exam_number)."'
                        ORDER BY s.subject_name ASC";

  $results_query_result = mysqli_query($conn, $results_query_sql);

  if (!$results_query_result) {
    // Query failed
    $error_message = 'SQL Error: ' . mysqli_error($conn);
    // In a production environment, log this error and show a generic message to the user.
  } else {
    $student_results = [];
    while ($row = mysqli_fetch_assoc($results_query_result)) {
        $student_results[] = $row;
    }
  }

  // Attempt to get student's name or other details if desired (Optional enhancement)
  // For now, we'll use the exam number for the caption or a generic title.
  // $user_details_sql = "SELECT name FROM user WHERE exam_number = '".mysqli_real_escape_string($conn, $current_exam_number)."'";
  // $user_details_result = mysqli_query($conn, $user_details_sql);
  // $user_name = $current_exam_number; // Default to exam number
  // if ($user_details_result && mysqli_num_rows($user_details_result) > 0) {
  //   $user_row = mysqli_fetch_assoc($user_details_result);
  //   if (!empty($user_row['name'])) { // Assuming a 'name' column exists in 'user' table
  //       $user_name = $user_row['name'];
  //   }
  // }

  require ('header.php');
?>
  <center>
	<h1>Student Result</h1>
	<table class="data-table">
		<caption class="title">Results for Exam Number: <?php echo htmlspecialchars($current_exam_number); ?></caption>
		<thead>
			<tr>
				<th>SUBJECT</th>
				<th>SCORE</th>
				<th>GRADE</th>
				<th>REMARKS</th>
			</tr>
		</thead>
		<tbody>
		<?php
        if (isset($error_message)) {
            echo '<tr><td colspan="4" style="color:red; text-align:center;">'.$error_message.'</td></tr>';
        } elseif (!empty($student_results)) {
            foreach ($student_results as $result_row) {
                echo '<tr>';
                echo '<td>'.htmlspecialchars($result_row['subject_name']).'</td>';
                echo '<td>'.htmlspecialchars($result_row['score']).'</td>';
                echo '<td>'.htmlspecialchars($result_row['grade']).'</td>';
                echo '<td>'.htmlspecialchars($result_row['remarks']).'</td>';
                echo '</tr>';
            }
        } else {
            echo '<tr><td colspan="4" style="text-align:center;">No results found for this exam number.</td></tr>';
        }
		?>
		</tbody>
	</table>
	   
      <form style="margin-top: 20px;">
         <input type="button" value="Print" onclick="window.print()" class="button"/>
      </form>
  </center>
</body>
<?php 
    require('footer.php');
?>