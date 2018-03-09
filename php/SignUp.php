
<?php 
if(!isset($_SESSION)){
    session_start();
}

// SET $page_type = 'student','teacher','public'
$page_type = 'public';
require('inc.header.php');

if(!isset($db))
{
  require('inc.dbc.php');
    $db = get_connection();
}



// HANDLE UPDATES TO COURSES USING POST
if(isset($_POST['submit']))
{
  if(strlen($_POST['username']) == 0)
  {
    $new_message = '<p class="alert-danger">Invalid Name: </p>';
    echo $new_message;
  } else {
    $temp1 = $_POST['username'];
    $temp2 = $_POST['preferredName'];
    $db->query("INSERT INTO USERS  VALUES ('$temp1','$temp2','quizzer' )");
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
          
          <li role="presentation" class="active">  <a href="SignUp.php">Sign Up</a></li>
          <li role="presentation" class="inactive"><a href="Logout.php">Back</a></li>
        </ul>	   
      </div>
      <div class="col-sm-8">
        <div class="panel panel-default">
          <div class="panel-heading">Welcome, Guest. Sign up below.</div>
            <div class="panel-body">
               <hr>
                <form role="form" method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                  <div class="form-group">
                    Type a username. less than 30 characters.
                    <input type="text" placeholder="Enter username" name="username" class="form-control" />
                    <input type="text" placeholder="Enter preferred name" name="preferredName" class="form-control" />
                    <button class="form-group btn btn-lg btn-primary" type="submit" name="submit" value="active">SUBMIT</button>                   
                  </div>
                </form>
            </div>
          </div>
        </div>
      </div>
    </div>
 </div>
 <?php include("./inc.footer.php");?>