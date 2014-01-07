<?php
	/* insert global variables*/
	include('../php/include/sessionAjax.php'); 

	$posts = array();
	if ($session->logged_in) {
		if(DB_CONNECT) {
			$query = "SELECT name, color, ID_category  FROM ".TBL_CATEGORY.
				     " WHERE ID_user=".
					 "(SELECT ID_user ".
					 " FROM ".TBL_USERS.
					 " WHERE username='$session->username')";
			$result = $database->query($query);
		
			/* create one master array of the records */
			if(mysql_num_rows($result)) {
				while($post = mysql_fetch_assoc($result)) {
					$posts[] = array_values($post);
				}
			}
	} else {
		//send fake json for testing
		$posts = array(
					array("Tasks",    "#49734b", 1), 
				 	array("Wishlist", "#aeb48e", 2),
				 	array("Work",     "#efefd5", 3),
				 	array("School",   "#a9cf58", 4)
				);
	}
	
	header('Content-type: application/json');
	echo json_encode($posts);
	} else { 
		/* not logged in. */
		header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
	}
?>