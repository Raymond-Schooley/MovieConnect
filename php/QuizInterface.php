
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
$message = ($q) ? "Success" : die();
if(isset($_POST['submit'])){

  //Set global variables.
  $location = "Quiz.php";
  $_SESSION['lowYear'] = $_POST['lowYear'];
  $_SESSION['highYear'] = $_POST['highYear'];
  $_SESSION['difficulty']= $_POST['difficulty'];
  $_SESSION['tenMovies'] = $_POST['lowYear'];
  $_SESSION['correctCount'] = 0;
  $_SESSION['overallCount'] = 0;



  


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

               <hr>
                <form role="form" method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                  <div class="input-group">                    
                    <input type="text" placeholder="enter year span" name="lowYear" class="form-control" />
                    <span class = "input-group-addon">-</span>
                    <input type="text" placeholder="enter year span" name="highYear" class="form-control" />
                    <span class = "input-group-addon">-</span>
                    <input type="text" placeholder="enter percentage difficulty (1 - 100)" name="difficulty" class="form-control" />
                    
                  </div>
                  <hr>
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
 
