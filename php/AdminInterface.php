<?php 
if(!isset($_SESSION)){
    session_start();
}

// SET $page_type = 'student','teacher','public'
$page_type = 'admin';
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


if(isset($_POST['add'])){
  $temp1 = $_POST['addUserName'];
  $temp2 = $_POST['prefName'];
  $temp3 = 'admin';
  
  
  
  $db->query("INSERT INTO USERS VALUES ('$temp1','$temp2', '$temp3')");
  
}

if(isset($_POST['delete'])){
  $temp = $_POST['delUserName'];
  $db->query("DELETE FROM USERS WHERE username = '$temp'");
  
}


/*
$db->query("INSERT INTO MOVIE (Id, PrimaryTitle, RuntimeMinutes, Genres, AverageRating ) VALUES ('testid', ".$_POST['movieName'].", ".$_POST['runTime'].", ".$_POST['genre']."),".$_POST['rating']." ");

// HANDLE NEW COURSES USING POST
/*
if(isset($_GET['action']))
{
    // MAKE SURE THE SESSION USER IS THE SAME AS THE USER REQUEST.
      
      switch ($_GET['action']) {
      case 'add':
        $q = $db->prepare("INSERT INTO MOVIE (Id, PrimaryTitle, Genres, RuntimeMinutes, AverageRating)VALUES (".$_POST['movieName']."
                                                      , ".$_POST['genre'].", ".$_POST['runTime']." ,".$_POST['rating']." ");
        if($q->execute(array(':course'=>$_GET['cn'])))
          $mod_message = '<p class="alert-success">Course deactivated.</p>';
        break;
      case 'delete':
        // TWO THINGS NEEDED HERE, NEED TO CLEAR ALL REGISTRATIONS BEFORE DELETING THE COURSE
        $reg = $db->prepare("DELETE FROM Registration WHERE course_number = :course");
        if($reg->execute(array(':course'=> $_GET['cn']))) {
          $mod_message .= '<p class="alert-success">' . $reg->rowCount() . ' student(s) successfully removed from course';
        }
        $q = $db->prepare("DELETE FROM Course WHERE course_number = :course");
        if($q->execute(array(':course'=> $_GET['cn'])))
          $mod_message .=  '<p class="alert-success">Course successfully deleted</p>';
        break;
      default:
        $mod_message = '<p class="alert-warning">Unable to perform the requested action: '.$_GET['action'].'</p>';
        break;
      }
     
      $mod_message = '<p class="alert-warning">Unable to perform the requested action.</p>';
        
}

*/





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
<!--  ************************** -->
<!--  SET NAVIGATION ACTIVE HERE -->
<!--  ************************** -->
          <li role="presentation" class="active"><a href="Logout.php">Logout</a></li>
          </ul>    

      </div>
      <div class="col-sm-8">
        <div class="panel panel-default">
          <div class="panel-heading">Welcome, <?php echo $preferredName; ?>.  Update Database Below</div>
            <div class="panel-body">
               <hr>
                <form role="form" method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                  <div class="input-group">                    
                    <input type="text" placeholder="enter username to delete" name="delUserName" class="form-control" />
                    
                    
                  </div>
                  <span class = "input-group-addon">-</span>
                    <div class="input-group">                    
                    <input type="text" placeholder="enter username to add as admin" name="addUserName" class="form-control" />
                    <span class = "input-group-addon">-</span>
                    <input type="text" placeholder="enter preferred name" name="prefName" class="form-control" />
                    
                  </div>
                  <hr>
                  <div class = "text-center">
                  <button class="form-group btn btn-lg btn-primary" type="submit" name="add" value="active">ADD AS ADMIN</button>
                  <button class="form-group btn btn-lg" type="submit" name="delete" value="inactive">DELETE</button>   
                </form>
                 
                </div>        
            </div>
          </div>
        </div>        
      </div>
    </div>
 </div>
 <?php include("./inc.footer.php");?>
 

 