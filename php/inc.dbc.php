<?php

	function get_connection() {
		$userid   = 'UWID'; //Change this to yours
		$password = 'PASSWORD'; //Change this to yours
		$host     = 'cssgate.insttech.washington.edu';
		$dbname   = 'DBNAME'; //Change this to yours
		//jdbc:mysql://localhost:3306/tcss445
		$dsn = 'mysql:host='.$host. ';dbname='. $dbname;
		
		try {
		    global $db; 
		    $db = new PDO($dsn, $userid, $password);

		}
		catch(PDOException $e) {
			echo "Error connecting to database";
			echo $e->getMessage();
			
	    }

	   	    return $db;
	}
?>
