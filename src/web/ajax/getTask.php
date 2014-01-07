<?php
	/*
	 * Script return data about particular task
	 * 
	 * input parameters: tid
	 * output data: check below
	 */
	/* insert global variables*/
	include('../php/include/sessionAjax.php'); 

	function checkParameters() {
		return isset($_GET['tid']);
	}
	
	$tid=$_GET["tid"];

	$posts = array();
	if ($session->logged_in) {
		/* connect to the db if database enabled in global variables*/
		if(DB_CONNECT && checkParameters()) {
			
			/* get user id */
			 $query="SELECT ID_user".
				   	" FROM ".TBL_USERS.
					" WHERE username='$session->username'";
			$result =  $database->query($query);
			
			$post = mysql_fetch_assoc($result);
			$uid = $post["ID_user"];
			
			/* get info of task*/
			$query="SELECT task.ID_category, start_date, due_date, priority, ".
			          "reminder_email, reminder_sms, acknowledge, title, ".
			          "description, repeat_time, repeat_ends, category.name ".
				   "FROM ". TBL_TASK . ", ".TBL_CATEGORY." ".
				   "WHERE ".
				   "task.ID_user=category.ID_user and  ".
				"category.ID_category=task.ID_category and ". 
			"task.ID_user=$uid and task.ID_task=$tid";

			//  category name , 
			$result =  $database->query($query);
			if(mysql_numrows($result) != 1) {
				exit(1); 
			} else {
				$post = mysql_fetch_assoc($result);
// 				print_r($post); 
			}		
			
			// translate 

			// repeat
			$repeat = repeatDBtoUI($post["repeat_time"]);
			
			//repeat ends
			if (!(($repeat == $repeats[0]) || //never 
				($post["repeat_ends"] == "0000-00-00"))) {// repeat ends not set
 				$repeat_ends =convertDateFormatDBtoUI($post["repeat_ends"]);
			} else {
				$repeat_ends = $repeats[0];
			}
			// reminder
			$reminder_email = reminderDBtoUI($post["reminder_email"]);
			$reminder_sms 	= reminderDBtoUI($post["reminder_sms"]);
			
			// done
			$done="";
			if ($post["acknowledge"] == 0) {
				$done=false;
			} else {
				$done=true;
			}
			//if($tid == 1){
			$posts = array(
							"tid" 	  		=> $tid,
							"cid"	  		=> $post["ID_category"],
							"start"   		=> convertDateAndTimeDBtoUI($post["start_date"]),
							"end"     		=> convertDateAndTimeDBtoUI($post["due_date"]),
							"priority"		=> $post["priority"],
							"reminderSMS"	=> $reminder_sms,
							"reminderEmail"	=> $reminder_email,
							"repeat"  		=> $repeat,
							"completed"		=> $done,
							"category"		=> $post["name"],
							"name" 	  		=> $post["title"],
							"notes"   		=> $post["description"],
							"repeatEnds"	=> $repeat_ends
			);
			header('Content-type: application/json');
			echo json_encode($posts);
				
		}else {
			header($_SERVER['SERVER_PROTOCOL'] . ' 503 Service Unavailable', true, 503);
		}
	} else {
		/* not logged in. */
		header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
	}
	

?>