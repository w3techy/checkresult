<?php
   require ("config.php");
   session_start();
   
   if($_SERVER["REQUEST_METHOD"] == "POST") 
    {
      // username and password sent from form 
      
      $myid = mysqli_real_escape_string($conn, $_POST['examno']); // This is the exam_number
      $mypin = mysqli_real_escape_string($conn, $_POST['pin']); 
      
      // Query to check if the exam_number and pin match a user record
      $sql = "SELECT exam_number FROM user WHERE exam_number = '$myid' AND pin = '$mypin'";
      $result = mysqli_query($conn,$sql);
      
      if($result) {
          $row = mysqli_fetch_array($result,MYSQLI_ASSOC);
          $count = mysqli_num_rows($result);
      } else {
          // Query failed, handle error or set count to 0
          $count = 0;
          // Optional: log mysqli_error($conn)
      }
      
      // If result matched exam_number and pin, table row must be 1 row
		
      if($count == 1) {
         // Store the validated exam_number in the session
         $_SESSION['login_user'] = $row['exam_number'];
         
         header("location: result.php");
         exit(); // Good practice to exit after a header redirect
      }else {
         $error = "Your Exam Number or Pin is invalid";
      }
    }
    include("header.php");
  ?>
      <div align = "center">
         <div style = "width:300px; border: solid 1px #333333; " align = "left">
            <div style = "background-color:#333333; color:#FFFFFF; padding:10px;"><b>Enter Your Details</b></div>
				
            <div style = "margin:30px">
               
               <form action = "" method = "post" align="center">
                  <label for="examno">Exam Number:</label></br><input type = "text" name = "examno" class = "box"/><br /><br />
                  <label for="pin">Pin:</label></br><input type = "text" name = "pin" class = "box" /><br/><br />
                  <input type = "submit" value = "Check Result Now!" class="button"/><br />
               </form>
               
               <div style = "font-size:11px; color:#cc0000; margin-top:10px"><?php echo $error; ?></div>
					
            </div>
				
         </div>
			
      </div>

   </body>
<?php   
   include("footer.php");
?>