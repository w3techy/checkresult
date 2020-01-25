<?php
   require ("config.php");
   session_start();
   
   if($_SERVER["REQUEST_METHOD"] == "POST") 
    {
      // username and password sent from form 
      
      $myid = mysqli_real_escape_string($conn, $_POST['examno']);
      $mypin = mysqli_real_escape_string($conn, $_POST['pin']); 
      
      $sql = "Select id FROM user WHERE pin = '$mypin'";
      $result = mysqli_query($conn,$sql);
      $row = mysqli_fetch_array($result,MYSQLI_ASSOC);
      $active = $row['active'];
      
      $count = mysqli_num_rows($result);
      
      // If result matched $myusername and $mypassword, table row must be 1 row
		
      if($count == 1) {
         $_SESSION['login_user'] = $myid;
         
         header("location: result.php");
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