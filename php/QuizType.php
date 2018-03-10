<?php 

if(!isset($_SESSION)){
    session_start();
}

// SET $page_type = 'student','teacher','public'
$page_type = 'quizzer';
require('inc.header.php');


# CONNECT TO DATABASE TO GET STUENT INFO
if (!isset($db)) {
    require_once('inc.dbc.php');
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

if(isset($_POST['submit'])){
  $location = "Quiz.php";
  header("Location: " .$location);

}


?>
 
<body>
  <div class="panel panel-default">
    <div class="panel-heading">
      <h2 class="panel-title">Welcome to Movie Quiz</h2>
    </div>
    <div class="panel-body">
        This mini project leverages Bootstrap 3.3.7 for HTML/CSS/JS, PHP7 and MariaDB 10.1.20
    </div>
  </div>
  <div class="container">
    <div class="row">
      <div class="col-sm-4">
        <ul class="nav nav-pills nav-stacked">
          <li role="presentation" class="active"><a href="Logout.php">Logout</a></li>
          <li role="presentation" class="inactive"><a href="UpdateInfo.php">Update your information</a></li>

      </div>
      <div class="col-sm-12">
        <div class="panel panel-default">
          <div class="panel-heading text-center panel-relative">Welcome, <?php echo $preferredName; ?>. Are you ready to test your knowledge?</div>
          <div class="text-center">

            <h3>Select quiz type</h3>

        		    <input type="text" placeholder="Select quiz type" name="type" class="row"/>
                <form role="form" method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <div class="form-group">
		        <button class="btn btn-sm btn-primary btn-default" type="submit" name="submit">
			       submit
		        </button>
		      </div>
            </form>
          </div>        
        </div>
      </div>
    </div>
 </div>
 <?php include("./inc.footer.php");?>