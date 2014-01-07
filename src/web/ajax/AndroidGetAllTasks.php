<?php
	/*
	 * Script sends data about this week's tasks witch are grouped by first element in second 
	 * dimension of array. Server should check witch tasks belong to todays date to tomorrows and the ones 
	 * that belong to the rest of the week.
	 * 
	 * input format: none
	 * 
	 * output format:
	 * [
	 * 		["Today", 	 "taskName", "date and time", marked, taskId, categoryId],
	 * 		["Tomorrow", "taskName", "date and time", marked, taskId, categoryId],
	 * 		[...]
	 * ]
	 */
	
	/* insert global variables*/
	$database=true;
	/* require the user as the parameter */
	error_reporting(E_ALL);
	
	
	$gusername ="";
	$gpassword ="";
	
	if (isset($_GET["username"]) && isset($_GET["password"])) {
//              $gusername = mysql_real_escape_string($_GET["username"]);
//              $gpassword = mysql_real_escape_string($_GET["password"]);
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
		$query = "SELECT task.ID_task, task.title, task.description, ".
					"task.estimated_time, task.priority, category.name, ".
					"task.start_date, task.due_date, task.repeat_time, ".
					"task.repeat_ends, task.acknowledge, task.reminder_email, ".
					"task.reminder_sms ".
				"FROM task, user, category ".
				"WHERE task.ID_user=user.ID_user and user.ID_user=category.ID_user and ".
				"category.ID_category=task.ID_category and ".
				"user.username=" . "'$gusername'" ." and user.password=". "'$gpassword'";
		
		
		$result = mysql_query($query,$link) or die('Errant query:  '.$query);
		
		/* create one master array of the records */
		if(mysql_num_rows($result)) {
			while($post = mysql_fetch_assoc($result)) {
				$posts[] = array_values($post);
			}
		}
	}else{
		//send fake json for testing
		$posts = array(
					array("Tomorrow", 	"Pošlji kup SMSov", "1.1.2012 14.00", false, 1),
			   		array("Tomorrow", 	"Nahrani kravo", "1.1.2012, 15.00", true, 2),
			   		array("This week",   "Preveri kolk si žiučn", "4.1.2012 14:00", false,  3), 
			   		array("This week",  "Pojdi na WC",    "4.1.2012 14.00", false,  6),
				);
	}
	
	header('Content-type: application/json');
	echo json_encode($posts);
		
	/* disconnect from the db */
	if($database == "true"){
		@mysql_close($link);
	}

?>