<?php 
	/*
	* Script parses new settings about remidners
	* 
	* input format: 
	* 
	*  phone : [number 9 digit] 
	*  pass  : [string]
	*  email : [string]
	* 
	* 
	* output format:
	* { "response" : true } 
	*/


include('../php/include/sessionAjax.php');
function checkParameters() {
 	return (isset($_POST['phone'])&&
 			isset($_POST['pass'])&&
 			isset($_POST['email']));
}

$posts = array();
if ($session->logged_in) {
	/* connect to the db if database enabled in global variables*/
	if(DB_CONNECT && checkParameters()){
		$response = true;
		
		/* get user id */
		$query="SELECT ID_user ".
			   "FROM ".TBL_USERS.
			  " WHERE username='$session->username'";
			
		$result =  $database->query($query);
		$post = mysql_fetch_assoc($result);
		$uid = $post["ID_user"];

		// update phone
		$query="UPDATE ".TBL_USERS.
					" SET tel="."'".$_POST["phone"]."'".
					" WHERE ID_user=".$uid;
		$result =  $database->query($query);
		if (!$result) {
			$response = false;
		}
		
		// update password
		$query="UPDATE ".TBL_USERS.
						" SET telpw="."'".$_POST["pass"]."'".
						" WHERE ID_user=".$uid;
		$result =  $database->query($query);
		if (!$result) {
			$response = false;
		}
		
		// update email
		$query="UPDATE ".TBL_USERS.
						" SET email="."'".$_POST["email"]."'".
						" WHERE ID_user=".$uid;
		$result =  $database->query($query);
		if (!$result) {
			$response = false;
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