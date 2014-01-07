<?php 


include('../php/include/sessionAjax.php');
function checkParameters() {
	return isset($_POST['tid']);
}


$posts = array();
if ($session->logged_in) {
	/* connect to the db if database enabled in global variables*/
	if(DB_CONNECT && checkParameters()){
		$response = true;
	
		// input data
		$tid=$_POST["tid"];
		$data=array();
		if (isset($_POST["start"]))
			$data["start_date"] = convertDateAndTimeUItoDB($_POST["start"]);
		if (isset($_POST["end"]))
			$data["due_date"] = convertDateAndTimeUItoDB($_POST["end"]);
		if (isset($_POST["priority"]))
			$data["priority"] = $_POST["priority"];
		if (isset($_POST["reminderSMS"]))
			$data["reminder_sms"] = reminderUItoDB($_POST["reminderSMS"]);
		if (isset($_POST["reminderEmail"]))
			$data["reminder_email"] = reminderUItoDB($_POST["reminderEmail"]);
		if (isset($_POST["cid"]))
			$data["ID_category"] = $_POST["cid"];
		if (isset($_POST["repeat"]))
			$data["repeat_time"] = repeatUItoDB($_POST["repeat"]);
		if (isset($_POST["notes"]))
			$data["description"] = $_POST["notes"];
		if (isset($_POST["name"])) 
			$data["title"] = $_POST["name"];
		if (isset($_POST["repeatEnds"]))
			$data["repeat_ends"] = convertDateFormatUItoDB($_POST["repeatEnds"]);
		
		foreach ($data as $arg => $value) {
			$query="UPDATE ".TBL_TASK;
			if ($arg == "ID_category" || 
				$arg == "estimated_time" || 
				$arg == "repeat_time" || 
				$arg == "reminder_email" || 
				$arg == "reminder_sms" || 
				$arg == "priority") {
					$query .= " SET ".$arg."=".$value;
			} else {
					$query .= " SET ".$arg."="."'".$value."'";
			}
			$query .= " WHERE ID_task=".$tid;
			
			$result =  $database->query($query);
			
			if (!$result) {
				$response = false;
			}
		}

		//response
		$posts=array( "response" => $response );
		header('Content-type: application/json');
		echo json_encode($posts);
			
	} else {
		header($_SERVER['SERVER_PROTOCOL'] . ' 503 Service Unavailable', true, 503);
	}
} else {
	/* not logged in. */
	header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
}
?>