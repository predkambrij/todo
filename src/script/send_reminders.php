#!/usr/bin/php
<?php
	include(__DIR__ . "/../web/php/include/db.php");
	include(__DIR__ . "/twilio-php-master/Services/Twilio.php");
	
	function timeDiff($dueDate) {
		$due = strtotime($dueDate);
		$now = time();
		
		$result = ($due-$now)/60;
		
		return $result;
	}
	
	function sendEmail($task, $database) {
		$query = "SELECT *".
				 "FROM ".TBL_USERS." ".
				 "WHERE ID_user=".(int)$task["ID_user"];
		
		$result = $database->query($query);
		
		if(!$result || (mysql_numrows($result) != 1)) {
			return false;
		}
		
		$user = mysql_fetch_array($result);
		
		$headers = "MIME-Version: 1.0\r\n";
		$headers .= "From: ".EMAIL_FROM_NAME." <".EMAIL_FROM_ADDR."\r\n";
		$headers .= "Content-type: text/plain; charset=UTF-8\r\n";
		$headers .= "X-Mailer: PHP/".phpversion();
		
	  
		$subject = "TODO: ".$task["title"];
		$body = "Dear ".$user["username"].",\n\n"
               ."your task '".$task["title"]."' "
               ."will be overdue on the ".convertDateAndTimeDBtoUI($task["due_date"]).".\n\n"
               ."Please do your work :-)\n";

		return mail($user["email"],$subject,$body,$headers);
	}
	
	function sendSMS($task, $database) {
		$query = "SELECT *".
				 "FROM ".TBL_USERS." ".
				 "WHERE ID_user=".(int)$task["ID_user"];
		
		$result = $database->query($query);
		
		if(!$result || (mysql_numrows($result) != 1)) {
			echo mysql_numrows($result);
			return false;
		}
		
		$user = mysql_fetch_array($result);
		
		$body = "Your task '".$task["title"]."' "
               ."will be overdue on the ".convertDateAndTimeDBtoUI($task["due_date"]).". "
               ."Please do your work :-)";
			   
		if (strlen($body) > 160) {
			return false;
		}

		$cl = new SoapClient("https://moj.mobitel.si/mobidesktop-v2/wsdl.xml");
		$cl->SendSMS(
			array(
			"Username" => $user["tel"],
			"Password" => $user["telpw"],
			"Recipients" => array($user["tel"]),
			"Message" => $body
			)
		);
		
		return true;
	}

	$query = "SELECT * ".
		 "FROM ".TBL_TASK." ".
		 "WHERE acknowledge <> 1 AND (remindered_email = 0 OR remindered_sms = 0)";

	$result = $database->query($query);

	if(mysql_num_rows($result)) {
		while($task = mysql_fetch_assoc($result)) {
			if($task["remindered_email"] == "0" && $task["reminder_email"] != "-1" && 
				($diff=timeDiff($task["start_date"])) >= 0 && abs($diff-(int)$task["reminder_email"]) <= 5) {
				if(sendEmail($task, $database)) {
					echo $task["title"].": E-mail sent. ";
					$update = "UPDATE ".TBL_TASK." ".
				              "SET remindered_email=1 ".
						      "WHERE ID_task=".(int)$task["ID_task"];
					echo "DB update: ".$database->query($update)."\n";
				}
			}
			
			if($task["remindered_sms"] == "0" && $task["reminder_sms"] != "-1" &&
				 ($diff=timeDiff($task["start_date"])) >= 0 && abs($diff-(int)$task["reminder_sms"]) <= 5) {
				#$ret =  exec("/bin/bash /var/www/najdismsgw/send_sms.sh \"". $task["title"]. "\"");
                                $client = new Services_Twilio($AccountSid, $AuthToken);


				try {
				    $message = $client->account->messages->create(array(
				        "From" => "$twilio_from",
				        "To" => "$twilio_to",
				        "Body" =>"\n.\n.\n". $task["title"]. "",
				    ));
				    echo "SMS successfuly sent";
				} catch (Services_Twilio_RestException $e) {
				    echo "SMS error: ".$e->getMessage();
				}

				//if(sendSMS($task, $database)) {
				//if($ret == "True") {
					echo $task["title"].": SMS sent. ";
					$update = "UPDATE ".TBL_TASK." ".
				              "SET remindered_sms=1 ".
						      "WHERE ID_task=".(int)$task["ID_task"];
					echo "DB update: ".$database->query($update)."\n";
				//} else {
				//	echo "Sending SMS FAILED! Response:";
				//	echo $ret;
				//}
			}
		}
	}

?>

