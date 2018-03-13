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

$message = ($q) ? "Success" : die();
$qn = $q->fetch();

$username = $qn[0];
$sql = $db->query("SELECT preferredName FROM USERS WHERE username = '".$_SESSION['username']."'");
$message = ($sql) ? "Success" : die();
$pNameR = $sql->fetch();
$preferredName = $pNameR[0];

$lowYear = $_SESSION['lowYear'];
$highYear = $_SESSION['highYear'];
$lowYear = $_SESSION['lowYear'];
$difficulty = $_SESSION['difficulty'];

$db->query("SET @DifficultyPercent = $difficulty");
$db->query("SET @MinMovieYear = $lowYear");
$db->query("SET @MaxMovieYear = $highYear");
$db->query("SET @MinNumVotes = 10000");


$sql = $db->query("SELECT type FROM USERS WHERE username = '".$_SESSION['username']."'");
$message = ($sql) ? "Success" : die();
$stype = $sql->fetch();
$type = $stype[0];


$lowYear = $_SESSION['lowYear'];


$sql = $db->query("CALL makeQuestionWhatMovieStarsTheseActors(@q, @a, @w1, @w2, @w3)");


$temp = $db->query("SELECT @q, @a, @w1, @w2, @w3");
$temp2 = $temp->fetchAll();
$message = ($temp) ? "Success" : die();

$possibleAnswers = array(0 => $temp2[0]['@w1'],
                 1 => $temp2[0]['@w2'],
                 2 => $temp2[0]['@w3'],
                 3 => $temp2[0]['@a']);



shuffle($possibleAnswers);




$correctAnswer =$temp2[0]['@a'];
var_dump($correctAnswer);


var_dump($_SESSION['correctCount']);


//$sql = "SELECT primaryName FROM Actor ORDER BY rand() LIMIT 3 ";
//$result = $db->query($sql);
//$actors = $result->fetchAll();

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

            <h3><?php echo $temp2[0]['@q'];?></h3>
            
                <form role="form" method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <div class="form-group">
            <button class="btn btn-sm btn-primary btn-default" type="submit" name="A">
             <?php 
                

                echo $possibleAnswers[0];?>
            </button>
            <button class="btn btn-sm btn-primary btn-default" type="submit" name="B">
             <?php 

                echo $possibleAnswers[1];?>
            </button>
            <button class="btn btn-sm btn-primary btn-default" type="submit" name="C">
             <?php 
                echo $possibleAnswers[2];?>
            </button>
            <button class="btn btn-sm btn-primary btn-default" type="submit" name="D">
             <?php 
              
               echo $possibleAnswers[3];

             ?>
            </button>
          </div>
                  <?php 
                  

                  
                  
                  if(isset($_POST['A']) && strcmp($possibleAnswers[0], $temp2[0]['@a'])==0){
                      
                      $_SESSION['correctCount']++;
                      $new_message = '<p class="alert-success">Correct</p>' ; 
                    }else if(isset($_POST['B']) && strcmp($possibleAnswers[1], $temp2[0]['@a'])==0){
                    
                      $_SESSION['correctCount']++;

                      $new_message = '<p class="alert-success">Correct</p>' ; 
                      
                    }else if(isset($_POST['C']) && strcmp($possibleAnswers[2], $temp2[0]['@a'])==0){
                  
                      $_SESSION['correctCount']++;

                      $new_message = '<p class="alert-success">Correct</p>' ; 
                      
                    }else if(isset($_POST['D']) && strcmp($possibleAnswers[3],$temp2[0]['@a'])==0){
                      $new_message = '<p class="alert-success">Correct</p>' ; 
                      $_SESSION['correctCount']++;

                      
                    }else{
                      $new_message = '<p class="alert-warning">Incorrect</p>' ; 
                    }
                    

                    echo $new_message;
                    
                   
                   
                   ?>
            </form>
          </div>        
        </div>
      </div>
    </div>
 </div>
<?php include("./inc.footer.php");?>
 