<?php 
/*
This class is the interface for updating user information. Takes input from user and updates database accordingly.

Author: Igor Kalezic.
*/

if(!isset($_SESSION)){
    session_start();
}

// SET $page_type = 'student','teacher','public'
$page_type = 'quizzer';
require('inc.header.php');

if(!isset($db))
{
  require('inc.dbc.php');
    $db = get_connection();
}


$q = $db->query("SELECT username
      FROM USERS 
      WHERE username = '".$_SESSION['username']."'");

$qn = $q->fetch();

$username = $qn[0];
$sql = $db->query("SELECT preferredName FROM USERS WHERE username = '".$_SESSION['username']."'");
$pNameR = $sql->fetch();
$preferredName = $pNameR[0];

$sql = $db->query("SELECT type FROM USERS WHERE username = '".$_SESSION['username']."'");
$stype = $sql->fetch();
$type = $stype[0];

// HANDLE UPDATES TO COURSES USING POST
if(isset($_POST['update']))
{
  if(strlen($_POST['newName']) == 0)
  {
    $new_message = '<p class="alert-danger">Invalid Name: </p>';
   
  } else {
  	$temp1 = $_POST['newName'];
    $db->query("UPDATE USERS SET preferredName = '$temp1' WHERE username = '$username'");
  }
}





?>
<body>
  <div class="panel panel-default">
    <div class="panel-heading">
      <h2 class="panel-title">Welcome to TSS445 Project Demo</h2>
    </div>
    <div class="panel-body">
        This mini project leverages Bootstrap 3.3.7 for HTML/CSS/JS, PHP7 and MariaDB 10.1.20
    </div>
  </div>
  <div class="container">
    <div class="row">
      <div class="col-sm-4">
        <ul class="nav nav-pills nav-stacked">
<!--  ************************** -->
<!--  SET NAVIGATION ACTIVE HERE -->
<!--  ************************** -->
          <li role="presentation" class="inactive"><a href="QuizInterface.php">Quiz</a></li>
          <li role="presentation" class="active">  <a href="UpdateInfo.php">Update Information</a></li>
          <li role="presentation" class="inactive"><a href="Logout.php">Logout</a></li>
        </ul>	   
      </div>
      <div class="col-sm-8">
        <div class="panel panel-default">
          <div class="panel-heading">Welcome, <?php echo $preferredName; ?>.  Update Information Below</div>
            <div class="panel-body">
               <hr>
                <form role="form" method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                  <div class="form-group">
                    Type a new preferred name. less than 30 characters.
                    <input type="text" placeholder="Enter Preferred Name" name="newName" class="form-control" />
                    <button class="form-group btn btn-lg btn-primary" type="submit" name="update" value="active">UPDATE</button>
                  </div>
                </form>
            </div>
          </div>
        </div>
      </div>
    </div>
 </div>
 <?php include("./inc.footer.php");?>