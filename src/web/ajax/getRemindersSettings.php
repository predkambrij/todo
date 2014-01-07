<?php 
	/*
	* Script sends settings about remidners 
	*
	* output format:
	* {
	 * 	phone : [number]
	 * 	email : [string]
	 * } 
	*/

// print_r($_GET);

// print_r($_POST);

// print_r($_COOKIE);

include('../php/include/sessionAjax.php');
function checkParameters() {
	return true;
	//return isset($_POST['cids']);
}


// input data
// $cids=$_POST["cids"];

$posts = array();
if ($session->logged_in) {
	/* connect to the db if database enabled in global variables*/
	if(DB_CONNECT && checkParameters()){ // TODO
		$response = true;
		
		/* get user id */
		$query="SELECT ID_user".
						   	" FROM ".TBL_USERS.
							" WHERE username='$session->username'";
		$result =  $database->query($query);
			
		$post = mysql_fetch_assoc($result);
		$uid = $post["ID_user"];
		
		$query="SELECT tel, email".
			   " FROM ". TBL_USERS.
			   " WHERE ID_user=$uid";
		
		//  category name ,
		$result =  $database->query($query);
		if(mysql_numrows($result) != 1) {
			exit(1);
		} else {
			$post = mysql_fetch_assoc($result);
			// 				print_r($post);
		}
		//response
		$posts=array(
					"phone" => $post["tel"],
					"email" => $post["email"]
					);
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