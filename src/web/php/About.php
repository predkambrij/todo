<?php 
include("include/db.php");

?><!DOCTYPE html>
<html>
	<head>
		<title>TODO - Tasks</title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<meta name="viewport" content="width=device-width,height=device-height,initial-scale=1 maximum-scale=0.85,user-scalable=yes" /> 
		<script type="text/javascript" src="http://code.jquery.com/jquery-1.7.1.min.js"></script>
		<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js"></script>
		<script type="text/javascript" src="../js/Basic.js"></script>
		<link rel="stylesheet" type="text/css" href="../css/todo.css" />
		<link rel="stylesheet" type="text/css" href="../css/taskView.css" />
		<script type="text/javascript">
			function clock(selector){
				/*set timer that will update time each second*/
				setInterval(function(){
					var d = new Date();
					var h = (d.getHours()>9)? d.getHours() : "0" + d.getHours() ;
					var m = (d.getMinutes()>9)?d.getMinutes(): "0" + d.getMinutes();
					$(selector).html( h + " : " + m);
				},1000);
			}
			$(document).ready(function(){
				//add clock
				clock(".clock");
			});
		</script>
	</head>
	<body>
		<div class="header">
			<div class="stiches">
				<ul class="headerLinks right">
				<li><a href="About.php">About</a></li>
				<li><a href="Login.php"> Home </a></li>
				<ul>
			</div>
		</div>
		<div class="wbook">
			<div class="wbookHeader">
				<span class="clock"></span>
			</div>
			<div class="editorWrapper">
				<div class="left details">
					<span class='title'>About</span>
					<span class="content"><?php
					$query="SELECT about".
						   	" FROM app_info";
					$result =  $database->query($query);
						
					$post = mysql_fetch_assoc($result);
					$about = $post["about"];
					echo $about;		
					?><span>
				</div>
			</div>
		</div>
	</body>
</html>