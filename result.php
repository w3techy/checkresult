<?php 
  require ('config.php');
  require ('session.php');
    $sql = "SELECT * FROM mathematics WHERE ID = '$user_check'" ;
    $query = mysqli_query($conn, $sql);
  if (!$query) 
  {
    die ('SQL Error: ' . mysqli_error($conn));
  }
  require ('header.php');
?>
  <center>
	<h1>Result</h1>
	<table class="data-table">
		<caption class="title">Result of sososo</caption>
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
		require ('english.php');
		require ('mathematics.php');
		require ('chemistry.php');
		require ('commerce.php');
		?>
		</tbody>
	</table>
	   
      <form>
         <input type="button" value="Print" onclick="window.print()" />
      </form>
  </center>
</body>
<?php 
    require('footer.php');
?>