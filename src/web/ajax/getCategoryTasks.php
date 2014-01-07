<?php
	/*
	  * Function gets category tasks for category specified by input parameter
	  * function should get JSON object in format
	  *
	  * input parameters: cid = categoryId  
	  *  dateAndTime example: October 13, 1975 11:13:00
	  * output format:
	  * {
	  * 	cId : 3,
	  * 	category : "Work",
	  * 	tasks : [
	  * 		["name", "dateAndTime", priority, completed, taskId],
	  * 		[...]
	  * 	]
	  * }
	  */

	/* insert global variables*/
	include('../php/include/sessionAjax.php'); 
	
	function checkParameters() {
		return isset($_GET['cid']);
	}
	
	$cid=$_GET["cid"];
	
	$posts = array();
	if ($session->logged_in) {
		/* connect to the db if database enabled in global variables*/
		if(DB_CONNECT && checkParameters()){
			
			/* get user id */
			$query="SELECT ID_user ".
				   "FROM ".TBL_USERS.
				  " WHERE username='$session->username'";
			
			$result =  $database->query($query);
			
			$post = mysql_fetch_assoc($result);
			$uid = $post["ID_user"];
			
			/* get category name */
			$query="SELECT name".
				   " FROM ".TBL_CATEGORY.
				   " WHERE ID_category=$cid";
				
			$result =  $database->query($query);
			
			$cname="";
			if(mysql_numrows($result) != 1) {
				exit(1); // TODO
			} else {
				$post = mysql_fetch_assoc($result);
				$cname = $post["name"];
			}
			
			$show_history = false;
			if (isset($_POST['history']) && ($_POST['history'] == true)) {
				$show_history = true;
			}
			
// 			$show_history = false;
			
			if ($show_history == true) {
				/* grab the posts from the db */
				$query = "SELECT task.title, task.priority, start_date, task.due_date, ".
				                    "task.acknowledge, task.ID_task ".
						 "FROM ".TBL_TASK.
						" WHERE ID_user=$uid AND ID_category=$cid".
						" ORDER BY task.due_date";
		
			} else {
				$query = "SELECT task.title, task.priority, start_date, task.due_date, ".
				                    "task.acknowledge, task.ID_task ".
						 "FROM ".TBL_TASK.
						" WHERE ID_user=$uid AND ID_category=$cid AND".
							"(true != ( task.acknowledge=1 and due_date <now())) ".
						" ORDER BY task.due_date";
								
			}
			$result =  $database->query($query);
			$tasks=array();
			/* create one master array of the records */
			if(mysql_num_rows($result)) {
				while($post = mysql_fetch_assoc($result)) {
// 					$post["due_date"] = convertDateAndTimeDBtoUI($post["due_date"]);
					$post["due_date"]  	 = formatStartDueTime($post["start_date"], $post["due_date"]);
					unset($post["start_date"]);
					$tasks[] = array_values($post);
				}
			}
			$posts=array(
						"cid" => $cid,
						"category" => $cname,
						"tasks" => array_values($tasks)
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