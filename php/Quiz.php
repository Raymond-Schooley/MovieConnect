
<?php 
/*
Class for the quiz page. This class sets the necessary variables, selects the quiz questions, displays them, takes the answer from the user
and counts the correct amount of questions answered.

Author: Igor Kalezic
*/
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

$name = $q->fetch();
$username = $name[0];

//Get preferred name
$sql = $db->query("SELECT preferredName FROM USERS WHERE username = '".$_SESSION['username']."'");
$pNameR = $sql->fetch();
$preferredName = $pNameR[0];


$lowYear = $_SESSION['lowYear'];
$highYear = $_SESSION['highYear'];
$lowYear = $_SESSION['lowYear'];
$difficulty = $_SESSION['difficulty'];


//Set necessary variables for quiz
$db->query("SET @DifficultyPercent = $difficulty");
$db->query("SET @MinMovieYear = $lowYear");
$db->query("SET @MaxMovieYear = $highYear");
$db->query("SET @MinNumVotes = 10000");
$db->query("SET @MinRelatedness = 2");

//Get user type. Admin or Quiz taker.
$sql = $db->query("SELECT type FROM USERS WHERE username = '".$_SESSION['username']."'");
$stype = $sql->fetch();
$type = $stype[0];

//Random number generator for different questions.
$rng = rand(0,4);
//Different quiz questions.
if($rng == 0){
  $sql = $db->query("CALL makeQuestionWhatMovieStarsTheseActors(@q, @a, @w1, @w2, @w3)");
}else if($rng == 1){
  $sql = $db->query("CALL makeQuestionWhoWasLeadInRandMovie(@q, @a, @w1, @w2, @w3)");
}else if($rng == 2){
  $sql = $db->query("CALL makeQuestionWhatActorStarredInTheseMovies(@q, @a, @w1, @w2, @w3)");
}else if($rng == 3){
  $sql = $db->query("CALL createQuestionWhatYearWasRandMovie(@q, @a, @w1, @w2, @w3)");
}else {
  $sql = $db->query("CALL makeQuestionWhatMovieRandActorLeadIn(@q, @a, @w1, @w2, @w3)");
}

$temp = $db->query("SELECT @q, @a, @w1, @w2, @w3");
$answers = $temp->fetchAll();
$message = ($temp) ? "Success" : die();
$message = ($q) ? "Success" : die();
if($_SESSION['overallCount'] == 9){

    $location = 'Result.php';
    header("Location: " . $location);
}

$message = ($sql) ? "Success" : die();
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

            <h3><?php echo $answers[0]['@q'];?></h3>
            
                <form role="form" method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <div class="form-group">
            <button class="btn btn-sm btn-primary btn-default" type="submit" name="A">
             <?php 
                
                echo $answers[0]['@w1'];?>
            </button>
            <button class="btn btn-sm btn-primary btn-default" type="submit" name="B">
             <?php 
                echo $answers[0]['@w2'];?>
            </button>
            <button class="btn btn-sm btn-primary btn-default" type="submit" name="C">
             <?php 
                echo $answers[0]['@w3'];?>
            </button>
            <button class="btn btn-sm btn-primary btn-default" type="submit" name="D">
             <?php 
              
               echo $answers[0]['@a'];
             ?>
            </button>
          </div>
                  <?php 
                  
                  
                  if($_SERVER['REQUEST_METHOD'] === 'POST'){

                     if(isset($_POST['D'])){
                      
                      $_SESSION['correctCount']++;
                      $_SESSION['overallCount']++;
                      $new_message = '<p class="alert-success">Correct</p>' ; 
                    }else if(isset($_POST['B'])|| isset($_POST['A']) || isset($_POST['C']) ){
                    
                      $_SESSION['overallCount']++;
                      $new_message = '<p class="alert-warning">Incorrect</p>' ; 
                      
                    }
                    
                    echo $new_message;


                  }
                 
                    
                   
                   
                   ?>
            </form>
          </div>        
        </div>
      </div>
    </div>
 </div>
<?php include("./inc.footer.php");?>