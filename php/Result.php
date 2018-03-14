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

//Get username
$q = $db->query("SELECT username
      FROM USERS 
      WHERE username = '".$_SESSION['username']."'");
$message = ($q) ? "Success" : die();


//Get preferred name
$qn = $q->fetch();
$username = $qn[0];
$sql = $db->query("SELECT preferredName FROM USERS WHERE username = '".$_SESSION['username']."'");
$message = ($sql) ? "Success" : die();


$pNameR = $sql->fetch();
$preferredName = $pNameR[0];


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
          <div class="panel-heading text-center panel-relative">Congratulations, <?php echo $preferredName; ?>. You have finished the quiz</div>
          <div class="text-center">

            <h3>You scored <?php echo $_SESSION['correctCount']?> out of 10</h3>
            
                <form role="form" method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <div class="form-group">
            <button class="btn btn-sm btn-primary btn-default" type="submit" name="try">
                TRY AGAIN
            </button>
            <button class="btn btn-sm btn-primary btn-default" type="submit" name="back">
                EXIT
            </button>

            
          </div>
                  <?php 
                    if(isset($_POST['try'])){
                      $location = 'QuizInterface.php';
                      header("Location: " .$location);
                    } else if(isset($_POST['back'])){
                      $location = 'Login.php';

                      header("Location: " .$location);

                    }

                   ?>
            </form>
          </div>        
        </div>
      </div>
    </div>
 </div>
<?php include("./inc.footer.php");?>