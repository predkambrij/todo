<?php
	/*
	  * Function gets searches for tasks in all categories
	  *
	  * input parameters: 
	  *  query : [string]
	  * 
	  * output format:
	  * {
	  * 	tasks : [
	  * 			["name", "dateAndTime", priority, completed, taskId],
	  * 			[...]
	  * 		]
	  * }
	  */

	/* insert global variables*/
	include('../php/include/sessionAjax.php'); 
	
	function checkParameters() {
		return isset($_POST['search']);
	}
	
	$q = $_POST["search"];
	
	$posts = array();
	if ($session->logged_in) {
		/* connect to the db if database enabled in global variables*/
		if(DB_CONNECT && checkParameters()){
			
			/* grab the posts from the db */
			$query = "SELECT title, priority, due_date, acknowledge, ID_task"." ".
					 "FROM ".TBL_TASK." ".
					 "WHERE ID_user=(".
								"SELECT ID_user "." ".
								"FROM ".TBL_USERS." ".
							    "WHERE username='$session->username')"." ".
					 "AND title like'%$q%' ".
					 "ORDER BY due_date";
					 
			$result =  $database->query($query);
			
			if(mysql_num_rows($result)) {
				while($post = mysql_fetch_assoc($result)) {
					$post["due_date"]  = convertDateAndTimeDBtoUI($post["due_date"]);
					$post["priority"]  	 = (int)$post["due_date"];
					$post["acknowledge"] = ((int)$post["acknowledge"] == 1)? true : false;
					$post["ID_task"] 	 = (int)$post["ID_task"];
					$posts[] = array_values($post);
				}
			}
			
			$posts=array(
						"tasks" => array_values($posts)
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