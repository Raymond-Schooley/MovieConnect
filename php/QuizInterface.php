
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


$sql = "SELECT PrimaryTitle FROM MOVIE WHERE PrimaryTitle = 'Titanic'";

$result = $db->query($sql);
$name = $result->fetch();

$sql = "SELECT FullName FROM ACTOR ORDER BY rand() LIMIT 3 ";
$result = $db->query($sql);
$actors = $result->fetchAll();

if(isset($_POST['D'])){
   $new_message = '<p class="alert-success">Correct</p>' ; 
   echo $new_message;


}else if(isset($_POST['A']) or isset($_POST['B']) or isset($_POST['C'])){
  $new_message = '<p class="alert-success">Incorrect</p>' ; 
  echo $new_message;
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

            <h3>Which actor is in the movie <?php echo $name[0];?></h3>
        		
                <form role="form" method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <div class="form-group">
		        <button class="btn btn-sm btn-primary btn-default" type="submit" name="A">
			       <?php echo $actors[0]['FullName']?>
		        </button>
            <button class="btn btn-sm btn-primary btn-default" type="submit" name="B">
             <?php echo $actors[1]['FullName'];?>
            </button>
            <button class="btn btn-sm btn-primary btn-default" type="submit" name="C">
             <?php echo $actors[2]['FullName'];?>
            </button>
            <button class="btn btn-sm btn-primary btn-default" type="submit" name="D">
             Leo DiCaprio
            </button>
		      </div>
            </form>
          </div>        
        </div>
      </div>
    </div>
 </div>
 <?php include("./inc.footer.php");?>
 
