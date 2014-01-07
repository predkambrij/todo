<?php
include("include/session.php");
if($session->logged_in){
  header('Location: /');
  exit();
}
?>
<!DOCTYPE html>
<html>
	<head>
		<title>TODO - login</title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<!--<meta name="viewport" content="width=device-width,height=device-height,initial-scale=1,user-scalable=no" />--> 
		<script type="text/javascript" src="http://code.jquery.com/jquery-1.7.1.min.js"></script>
		<script type="text/javascript" src="../js/login.js"></script>
		<script type="text/javascript" src="../js/Basic.js"></script>
		<link rel="stylesheet" type="text/css" href="../css/todo.css" />
		<link rel="stylesheet" type="text/css" href="../css/login.css" />
	</head>
	<body>
		<div class="header">
			<div class="stiches">
				<ul class="headerLinks right">
				<li><a href="About.php">About</a></li>
				<li><a href="Register.php">Register</a></li>
				<ul>
			</div>
		</div>
		<div class="wbook">
			<div class="wbookContent">
				<span class="loginTitle">TODO</span>
				<form method="post" action="process.php">
				<table class="loginInput">
					<tr>
						<td>Username</td><td><input type="text" name="username"/></td>
					</tr>
					<tr>
						<td colspan="2"><input class="button" type="submit" value="Send new password" name="ForgotPass" /></td>
					</tr>
					<?php if($form->num_errors > 0) { ?>
					<tr>
						<td class='instructions' colspan="2">Wrong username.</td>
					</tr>
					<?php } else if (isset($_SESSION['forgotpass']) && $_SESSION['forgotpass'] == true) { ?>
						<tr>
							<td class='instructions' colspan="2">New password was sent to your e-mail address.</td>
						</tr>
					<?php unset($_SESSION['forgotpass']); }?>
				</table>
				</form>
			</div>
		</div>
	</body>
</html>