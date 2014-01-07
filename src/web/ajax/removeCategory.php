<?php
	/*
	* Script reads removes category specified by parameter and sends 
	* respond weather the task is completed succesfully
	*
	* input parameters:
	* cid [int] 
	*
	* output format:
	* {"response" : true} 
	*/
	
	/* insert global variables*/
	include('../php/include/sessionAjax.php');
	function checkParameters() {
		return isset($_POST['cids']);
	}
	
		
	// input data
	$cids=$_POST["cids"];
	
	$posts = array();
	if ($session->logged_in) {
		/* connect to the db if database enabled in global variables*/
		if(DB_CONNECT && checkParameters()){
			$response = true;
			
			if (count($cids) == 0) {
				$response = false;
			} else {
				if (isset($_POST["newCategory"])) {
					$ncid = $_POST["newCategory"];
					
					//move data from all categories to new category
					// select tasks which has been in deleted categories
					$tasks = array();
					for ($i=0; $i<count($cids); $i++) {
						$query="SELECT ID_task FROM ".TBL_TASK.
												" WHERE ID_category=".$cids[$i];
						$result =  $database->query($query);
						if(mysql_num_rows($result)) {
							while($post = mysql_fetch_assoc($result)) {
								$tasks[] = $post["ID_task"];
							}
						}
					}
					//move it to new category
					for ($i=0; $i<count($tasks); $i++) {
						$query="UPDATE ".TBL_TASK.
								" SET ID_category=".$ncid.
								" WHERE ID_task=".$tasks[$i];
						$result =  $database->query($query);
						
						if (!$result) {
							$response = false;
						}
					}
					
				// remove also tasks
				} else {
					for ($i=0; $i<count($cids); $i++) {
						$query="DELETE FROM ".TBL_TASK.
								" WHERE ID_category=".$cids[$i];
						$result =  $database->query($query);
						
						if (!$result) {
							$response = false;
						}
					}
				}
				
				
				// remove categories
				$response = true;
				for ($i=0; $i<count($cids); $i++) {
					$query="DELETE FROM ".TBL_CATEGORY.
							" WHERE ID_category=".$cids[$i];
					$result =  $database->query($query);
					if (!$result) {
						$response = false;
					}
				}
				
			} // end if cids == 0	
		
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