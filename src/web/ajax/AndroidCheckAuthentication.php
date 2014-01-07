<?php
	/* insert global variables*/
	$database = true;
	/* require the user as the parameter */
	error_reporting(E_ALL);
	$gusername ="";
	$gpassword ="";
	
	if (isset($_GET["username"]) && isset($_GET["password"])) {
		//$gusername = mysql_real_escape_string($_GET["username"]);
		//$gpassword = mysql_real_escape_string($_GET["password"]);
		$gusername = $_GET["username"];
		$gpassword = $_GET["password"];
		
	// no auth parameters
	} else {
		echo "false";
		exit();
	}
	
	$posts = array();
	/* connect to the db if database enabled in global variables*/
	if($database == "true"){
		$link = mysql_connect('localhost','tpo','tpo') or die('Cannot connect to the DB');
		mysql_select_db('tpo',$link) or die('Cannot select the DB');
		
		/* grab the posts from the db */
		$query = 'SELECT username FROM user where username='.
                            "'$gusername'" . " and password=" . "'$gpassword'";
		
		
		$result = mysql_query($query,$link) or die('Errant query:  '.$query);
		
		$num = mysql_num_rows($result);
		if ($num == 1) {
			echo "true";	
		} else {
			echo "false";
		}
		//echo "num=$num";
	} else {
		echo "false";
		exit();
	}
		
	/* disconnect from the db */
	if($database == "true"){
		@mysql_close($link);
	}

?>