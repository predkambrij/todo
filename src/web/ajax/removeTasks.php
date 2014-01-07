<?php
	/*
	* Script removes all tasks specified by parameter and sends 
	* response weather actions is completed successfully. (array input parameter should be parsed as JSON)
	*
	* input parameters:
	* tids [array]
	*
	* output format:
	* {"response" : true} 
	*/

	/* insert global variables*/
	include('../php/include/sessionAjax.php');

	function checkParameters() {
		return isset($_POST['tids']);
	}
	
	// input data
	$tids=$_POST["tids"];
	
	$posts = array();
	if ($session->logged_in) {
		/* connect to the db if database enabled in global variables*/
		if(DB_CONNECT && checkParameters()){
			
			if (count($tids) == 0) {
				$response = false;
			} else {
				$response = true;
				// if there is no periodic task there will no deletion
				$query1="DELETE FROM ".TBL_PERTASK. 
							" WHERE generated_ID_task IN (";
				$query1 .= join(", ",$tids);
				$query1 .= ")";

				
				$res2 =  $database->query($query1);
				
				// foreign key removed so we can remove task own now
				$query2="DELETE FROM ".TBL_TASK.
							" WHERE ID_task IN (";
				$query2 .= join(", ",$tids);
				$query2 .= ")";
				
				$res1 =  $database->query($query2);
				
				if (!($res1 == true && $res2 == true)) {
//					echo "$query1\n";
//					echo "$query2\n";
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