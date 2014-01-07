<?php
	/*
	* Script reads edited information about task and puts it into databasa
	*
	* input parameters:
	* cid : [int]
	* name : [string] || color : [string] || reminders : [string]
	*
	* output format:
	* {"response" : true} 
	*/
	include('../php/include/sessionAjax.php');
	
	function checkParameters() {
		return isset($_POST['cid']) && (isset($_POST['name']) || isset($_POST['color']) || isset($_POST['reminders']));
	}
	
	if($session->logged_in) {
		$posts = array();
		if(DB_CONNECT && checkParameters()) {
			$query = "SELECT * ".
					 "FROM ".TBL_USERS." ".
					 "WHERE username='$session->username'";
					 
			$result = $database->query($query);
			$dbarray = mysql_fetch_array($result);
			
			if(isset($_POST['name']))
				$query = "UPDATE ".TBL_CATEGORY." ".
				         "SET name='".$_POST['name']."' ".
						 "WHERE ID_user=".(int)$dbarray['ID_user']." AND ID_category=".$_POST['cid'];
			else if(isset($_POST['color']))
				$query = "UPDATE ".TBL_CATEGORY." ".
				         "SET color='".$_POST['color']."' ".
						 "WHERE ID_user=".(int)$dbarray['ID_user']." AND ID_category=".$_POST['cid'];
			else
				$query = "UPDATE ".TBL_CATEGORY." ".
				         "SET default_reminder_email=".$_POST['reminders'].",  default_reminder_sms=".$_POST['reminders']." ".
						 "WHERE ID_user=".(int)$dbarray['ID_user']." AND ID_category=".$_POST['cid'];
			
			$result =  $database->query($query);
			
			$posts = array ("response" => $result);
			
			header('Content-type: application/json');
						
			echo json_encode($posts);
		} else {
			header($_SERVER['SERVER_PROTOCOL'] . ' 503 Service Unavailable', true, 503);
		}
	} else {
		header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
	}
?>