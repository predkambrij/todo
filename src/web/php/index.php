<?php 

include("include/session.php");
if($session->logged_in){
  header('Location: TaskView.php');
  exit();
} else {
	header('Location: Login.php');
}



?>