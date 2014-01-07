<?php
	/*
	 * returns all posible values for dropdowns and select fields 
	 */
	/* insert global variables*/
	include('../php/include/sessionAjax.php');
	if ($session->logged_in) {
		$posts = array(
					"reminderTypes" 	=> $reminderTypes,
					"categoryTypes"     => $categoryTypes,
					"priorities"		=> $priorities,
					"taskReminders"		=> $taskReminders,
					"repeats"		    => $repeats
				);
	
		header('Content-type: application/json');
		echo json_encode($posts);
	} else {
		/* not logged in. */
		header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
	}
?>