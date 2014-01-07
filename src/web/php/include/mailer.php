<?php 
/**
 * Mailer.php
 *
 * The Mailer class is meant to simplify the task of sending
 * emails to users. Note: this email system will not work
 * if your server is not setup to send mail.
 *
 *
 */
 
class Mailer
{
   /**
    * sendWelcome - Sends a welcome message to the newly
    * registered user, also supplying the username and
    * password.
    */
   function sendWelcome($user, $email){
      $headers = "MIME-Version: 1.0\r\n";
	  $headers .= "From: ".EMAIL_FROM_NAME." <".EMAIL_FROM_ADDR."\r\n";
	  $headers .= "Content-type: text/plain; charset=UTF-8\r\n";
	  $headers .= "X-Mailer: PHP/".phpversion();
	  
      $subject = "Welcome on TODO.si!";
      $body = $user.",\n\n"
             ."Welcome! You've just registered at TODO.si "
             ."with the following information:\n\n"
             ."Username: ".$user."\n"
             ."If you ever lose or forget your password, a new "
             ."password will be generated for you and sent to this "
             ."email address.\n\n";

      return mail($email,$subject,$body,$headers);
   }
   
   /**
    * sendNewPass - Sends the newly generated password
    * to the user's email address that was specified at
    * sign-up.
    */
   function sendNewPass($user, $email, $pass){
      $headers = "MIME-Version: 1.0\r\n";
	  $headers .= "From: ".EMAIL_FROM_NAME." <".EMAIL_FROM_ADDR."\r\n";
	  $headers .= "Content-type: text/plain; charset=UTF-8\r\n";
	  $headers .= "X-Mailer: PHP/".phpversion();
	  
      $subject = "TODO.si - Your new password";
      $body = $user.",\n\n"
             ."We've generated a new password for you at your "
             ."request, you can use this new password with your "
             ."username to log in to TODO.si.\n\n"
             ."Username: ".$user."\n"
             ."New Password: ".$pass."\n\n"
             ."It is recommended that you change your password "
             ."to something that is easier to remember, which "
             ."can be done by going to the My Account page "
             ."after signing in.\n\n";
             
      return mail($email,$subject,$body,$headers);
   }
};

/* Initialize mailer object */
$mailer = new Mailer;
 
?>
