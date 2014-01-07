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
	
	$link = mysql_connect('localhost','tpo','tpo') or die('Cannot connect to the DB');
	mysql_select_db('tpo',$link) or die('Cannot select the DB');
	if (isset($_POST["json"])) {
		$json_arr = json_decode($_POST["json"]);
		print_r($json_arr);
		/*
		dbHelper.execSQL("insert into task (_id,title,description,"+
				            "estimated_time, priority, categoryname, start_date, "+
				            "due_date, repeat_time, repeat_ends, acknowledge, "+
		            		"reminder_email, reminder_sms) values("+atodo[0]+","+ //tid
		            		" '"+atodo[1]+"' "+","+" '"+atodo[2]+"' "+","+ //title, description
		atodo[3]+","+atodo[4]+","+" '"+atodo[5]+"' "+ // estimated time, priority, categoryname
		            		","+" '"+atodo[6]+"' "+","+" '"+atodo[7]+"' "+","+ //start_date, due_date
		atodo[8]+","+" '"+atodo[9]+"' "+","+atodo[10]+","+ //repeat_time, repeat_ends, acknowledge
		            		" '"+atodo[11]+"' "+","+" '"+atodo[12]+"' "+")"); // reminder_email, reminder_sms
		*/
	} else {
		echo "prazen post";
		exit();
	}
	
	
	header('Content-type: application/json');
	echo json_encode($posts);
		
	/* disconnect from the db */
	if($database == "true"){
		@mysql_close($link);
	}

?>