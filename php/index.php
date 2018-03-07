<?php 
 /* ************************
  * This page will do one ofthe following:
  *   A. Send an unautorized user to the sign in page
  *   B. An authorized teacher to the teacher portal
  *   C. An authorized student to the student partal
  */

if(!isset($_SESSION)){
  session_start();
}

if (isset($_SESSION['username'])) {  // determine if there is an authenticated user.
  switch (strtolower($_SESSION['role'])) {
  case "quiz taker":
     header( "Location: QuizInterface.php");
     break;
  case "admin":
    header("Location: AdminInterface.php");
    break;
  default:
    header("Location: Login.php");
    break;
  }  
} else { // send then back to the signin page
  header ("Location:  Login.php");
}
  


?>