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


$temp = $_SESSION['lowYear'];
$temp2 = $_SESSION['highYear'];

$sql = "SELECT primaryTitle FROM Movie WHERE (startYear >= '$temp' AND startYear < '$temp2') OR startYear = '$temp' LIMIT 1  ";
$result = $db->query($sql);
$name = $result->fetch();

$sql = $db->query("CALL getMovieYearQuestion(@question, 'Caddyshack', 1980, @a2, @a3, @a4)");

$temp = $db->query("SELECT @question, '1980',@a2, @a3, @a4");
$temp2 = $temp->fetchAll();




//$sql = "SELECT primaryName FROM Actor ORDER BY rand() LIMIT 3 ";
//$result = $db->query($sql);
//$actors = $result->fetchAll();
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

            <h3><?php echo $temp2[0]['@question'];?></h3>
            
                <form role="form" method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <div class="form-group">
            <button class="btn btn-sm btn-primary btn-default" type="submit" name="A">
             <?php echo $temp2[0]['@a2']?>
            </button>
            <button class="btn btn-sm btn-primary btn-default" type="submit" name="B">
             <?php echo $temp2[0]['@a3'];?>
            </button>
            <button class="btn btn-sm btn-primary btn-default" type="submit" name="C">
             <?php echo $temp2[0]['@a4'];?>
            </button>
            <button class="btn btn-sm btn-primary btn-default" type="submit" name="D">
             <?php $temp = $db->query("SELECT startYear FROM Movie WHERE primaryTitle = 'Caddyshack'");
                   $temp2 = $temp->fetch();
                   echo $temp2[0];


             ;?>
            </button>
          </div>
            </form>
          </div>        
        </div>
      </div>
    </div>
 </div>
<?php include("./inc.footer.php");?>
 