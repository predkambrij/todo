<?php 
	/*
	* Script changes password of user if old one matches if not it sends response : false
	* 
	* input format: 
	*  oldPassword  : [string]
	*  newPassword  : [string]
	* 
	* output format:
	* { "response" : true } 
	*/
	/* insert global variables*/
	include('../php/include/sessionAjax.php');
	
	function checkParameters() {
		return isset($_POST['oldPassword']) && isset($_POST['newPassword']);
	}
	
	if($session->logged_in) {
		$posts = array();
		if(DB_CONNECT && checkParameters()) {		
			$result = $session->editAccount($_POST['oldPassword'], $_POST['newPassword'], false);
			if($result) {
				$session->login($session->username, $_POST['newPassword']);
			}
					
			$posts = array ("response" => $result);
			
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