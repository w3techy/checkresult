<?php require('config.php');
if (session_status() == PHP_SESSION_NONE) { // Check if session is already started
    session_start();
}

   if(!isset($_SESSION['login_user'])) {
       header("location:index.php");
       exit(); // Good practice to exit after a header redirect
   }

   $user_check = $_SESSION['login_user']; // This is the exam_number

   // Optional: Verify if the user still exists in the database
   // $ses_sql = mysqli_query($conn,"SELECT exam_number FROM user WHERE exam_number = '$user_check' ");
   // $row = mysqli_fetch_array($ses_sql,MYSQLI_ASSOC);
   // if(!$row){
   //    session_destroy(); // Destroy session if user not found
   //    header("location:index.php");
   //    exit();
   // }
   // $login_session = $row['exam_number']; // Contains the exam_number

   // For simplicity, we'll rely on the session variable being set.
   // The login process in index.php is responsible for validating and setting it.
   $login_session = $user_check; // $login_session will hold the exam_number
?>