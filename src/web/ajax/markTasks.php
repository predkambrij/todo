<?php
	/*
	* Script set acknowledge parameter for selected tasks. 
	* Server respond weather the task is completed succesfully
	*
	* input parameters:
	* tids : [array]
	*
	* output format:
	* {"response" : true} 
	*/

include('../php/include/sessionAjax.php');
function checkParameters() {
	return isset($_POST['tids']);
}


if ($session->logged_in) {
	/* connect to the db if database enabled in global variables*/
	if(DB_CONNECT && checkParameters()){
		$posts = array();
		$tids=$_POST["tids"];
		$response = true;

		if (count($tids) == 0) {
			$response = false;
		} else {
			for ($i=0; $i<count($tids); $i++) {
				// get state
				$query="SELECT acknowledge FROM ".TBL_TASK.
						" WHERE ID_task=".$tids[$i];
				$result =  $database->query($query);
				$post = mysql_fetch_assoc($result);
				$ack = $post["acknowledge"];
					
				if ($ack == "0") {
					$ack = "1";
				} else {
					$ack = "0";
				}
				
				
				// and make inverse
				$query="UPDATE ".TBL_TASK.
						" SET acknowledge=".$ack.
						" WHERE ID_task=".$tids[$i];
				$result =  $database->query($query);
			
				if (!$result) {
					$response = false;
				}
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