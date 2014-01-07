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
		<script type="text/javascript" src="../js/Register.js"></script>
		<script type="text/javascript" src="../js/Basic.js"></script>
		<link rel="stylesheet" type="text/css" href="../css/todo.css" />
		<link rel="stylesheet" type="text/css" href="../css/register.css" />
	</head>
	<body>
		<div class="header">
			<div class="stiches">
				<ul class="headerLinks right">
				<li><a href="About.php">About</a></li>
				<li><a href="Login.php">Register</a></li>
				<ul>
			</div>
		</div>
		<div class="wbook">
			<div class="wbookContent">
				<span class="loginTitle">TODO</span>
				<form method="post" action="process.php">
				<table class="loginInput">
					<tr>
						<td class='instructions' colspan="2">Please fill out the form below. After that you will receive an e-mail to your address and from there you are all set to start using TODO. </td>
					</tr>
					<?php if($form->num_errors > 0) { ?>
					<tr>
						<td class='instructions' colspan="2">
						<?php echo $form->error("user");
							  echo $form->error("pass");
							  echo $form->error("email");
						?>
						</td>
					</tr>
					<?php } else if(isset($_SESSION['regsuccess']) && $_SESSION['regsuccess'] == true) { ?>
					<tr>
						<td class='instructions' colspan="2">Registration successful.</td>
					</tr>
					<?php unset($_SESSION['regsuccess']); } ?>
					<tr>
						<td>Username</td><td><input type="text" name="username" value="<?php echo $form->value("username"); ?>"/></td>
					</tr>
					<tr>
						<td>Password</td><td><input type="password" name="password" value="<?php echo $form->value("password"); ?>"/></td>
					</tr>
					<tr>
						<td>E-mail</td><td><input type="text" name="email" value="<?php echo $form->value("email"); ?>"/></td>
					</tr>
					<tr>
						<td colspan="2"><input class="button" type="submit" value="Register" name="Register" /></td>
					</tr>
				</table>
				</form>
			</div>
		</div>
	</body>
</html>