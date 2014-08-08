<?php
include("include/session.php");
if(!$session->logged_in){
  header('Location: Login.php');
  exit();
}
?>
<!DOCTYPE html>
<html>
	<head>
		<title>TODO - Tasks</title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<meta name="viewport" content="width=device-width,height=device-height,initial-scale=1 maximum-scale=0.85,user-scalable=yes" /> 
		<script type="text/javascript" src="https://code.jquery.com/jquery-1.7.1.min.js"></script>
		<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js"></script>
		<script type="text/javascript" src="../js/farbtastic.js"></script>
		<script type="text/javascript" src="../js/jquery.editable.js"></script>
		<script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>
		<script type="text/javascript" src="../js/autoresize.jquery.min.js"></script>
		<script type="text/javascript" src="../js/jquery.dataTables.rowGrouping.js"></script>
		<script type="text/javascript" src="../js/TableTools.min.js"></script>
		<script type="text/javascript" src="../js/TaskView.js"></script>
		<script type="text/javascript" src="../js/Basic.js"></script>
		<link rel="stylesheet" type="text/css" href="../css/todo.css" />
		<link rel="stylesheet" type="text/css" href="../css/taskView.css" />
	</head>
	<body>
		<div class="header">
			<div class="stiches">
				<ul class="headerLinks right">
				<li><a href="Help.php">Help</a></li>
				<li><a href="About.php">About</a></li>
				<li><a href="process.php">Logout</a></li>
				<ul>
			</div>
		</div>
		<div class="wbook">
			<div class="wbookHeader">
				<span class="clock"></span>
				<table>
					<tr>
						<td><ul class="headerIcons"></ul></td>
						<td><div class="information shadow borders"></div></td>
						<td class="search">Search: <input class="form taskSearch" type="text"/></td>
					</tr>
				</table>
			</div>
			<div class="editorWrapper">
				<div class="left details">
					
				</div>
				<div class="right categories">
					
				</div>
			</div>
		</div>
	</body>
</html>
