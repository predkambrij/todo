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
	include('../php/include/sessionAjax.php');
	
	function checkParameters() {
		return isset($_POST['history']);
	}
	
	// database format
	function getStringTime($duedate,$curdate) {
		$mindue = 0;
		list($ddate, $dtime) = explode(" ", $duedate);
		list($cdate, $ctime) = explode(" ", $curdate);

		// daily precission
		$dtime = strtotime($ddate);
		$ctime = strtotime($cdate);
		
		// diff in days
		$diff = (($dtime - $ctime)/60/60/24);
		
		// dependent on difference in days return string
		if ($diff < 0) {
			return "Before";
		} else if ($diff == 0) {
			return "Today";
		} else if ($diff == 1) {
			return "Tomorrow";
		} else if ($diff < 7) {
			return "This week";
		} else if ($diff >= 7) {
			return "Later";
		}
	}
	
	$posts = array();
	if ($session->logged_in) {
	/* connect to the db if database enabled in global variables*/
	if(DB_CONNECT){
		// get current time
		$query = "SELECT now()";
		$result = $database->query($query);
		$post = mysql_fetch_assoc($result);
		$curdate = $post["now()"];
		
		$show_history = false;
		if (isset($_POST['history']) && ($_POST['history'] == true)) {
			$show_history = true;
		}
		
// 		$show_history = true;
		
		if ($show_history == true) {
			/* grab the posts from the db */
			$query = "SELECT 'stringtime', title, start_date, due_date, acknowledge, ID_task, ID_category ".
					 "FROM ".TBL_TASK.
					" WHERE ID_user=(".
									"SELECT ID_user ".
									"FROM ".TBL_USERS.
								   " WHERE username='$session->username')".
					" ORDER BY due_date";
		} else {
			/* grab the posts from the db */
			$query = "SELECT 'stringtime', title, start_date, due_date, acknowledge, ID_task, ID_category ".
					 "FROM ".TBL_TASK.
					" WHERE ID_user=(".
									"SELECT ID_user ".
									"FROM ".TBL_USERS.
								   " WHERE username='$session->username') AND ".
							"(true != ( acknowledge=1 and due_date <now())) ".
					"ORDER BY due_date";
		}
// 		echo $query;
		$result =  $database->query($query);
		// startdate should be changed  to duedate with word "today, tomorrow,..."
		
		/* create one master array of the records */
		$i=0;
		
		if(mysql_num_rows($result)) {
			while($post = mysql_fetch_assoc($result)) {
				$post["stringtime"]  = getStringTime($post["due_date"],$curdate);
				$post["due_date"]  	 = formatStartDueTime($post["start_date"], $post["due_date"]);
				$post["acknowledge"] = ((int)$post["acknowledge"] == 1)? true : false;
				$post["ID_task"] 	 = (int)$post["ID_task"];
				$post["ID_category"] = (int)$post["ID_category"];
				unset($post["start_date"]);
				$posts[]			 = array_values($post);
			}
		}
	}else{ 
		//send fake json for testing
		$posts = array(
					array("Today", 		"Neki za faks", "31.1.2011, 13:00", true, 8),
			   		array("Tomorrow", 	"Pošlji kup SMSov", "1.1.2012 14.00", false, 1),
			   		array("Tomorrow", 	"Nahrani kravo", "1.1.2012, 15.00", true, 2),
			   		array("This week",   "Preveri kolk si žiučn", "4.1.2012 14:00", false,  3), 
			   		array("This week",  "Pojdi na WC",    "4.1.2012 14.00", false,  6),
				);
	}
	
	header('Content-type: application/json');
	echo json_encode($posts);
	} else { echo "not logged."; }
	
?>