<?php 

include(__DIR__ . "/../web/php/include/db.php");

// rewrite this to consts
// how much days ahead
$days_ahead = 14;
$max_repeats = 5;

// get tasks which are periodic
function getPeriodicTasks($database) {
	$query = "SELECT ".TBL_PERTASK.".ID_periodic_task, ".TBL_PERTASK.".ID_task, ".TBL_PERTASK.".generated_ID_task, ".
"ID_category, ID_user, title, description, start_date, due_date, estimated_time, repeat_time, repeat_ends, acknowledge, priority, reminder_email, reminder_sms, remindered_email, remindered_sms ".
				"FROM ".TBL_PERTASK.", ".TBL_TASK." ".
				"WHERE ".TBL_PERTASK.".generated_ID_task=".TBL_TASK.".ID_task ".
				"ORDER BY ".TBL_PERTASK.".ID_task ASC, ".TBL_PERTASK.".generated_ID_task ASC";
//	echo $query . "\n";
	$result = $database->query($query);
	if (!$result) {
		die("There was an error. Error 342");
	}
	
	// convert output from database to grouped array (array of arrays) by periodic tasks
	$flag = null;
	$grouped_tasks=array();
	$tasks = array();
	while($task = mysql_fetch_assoc($result)) {
		// set entry for first loop
		if ($flag == null) {
			$flag = $task["ID_task"];
		}
		
		// when second periodic task comes make rotate
		if ($flag != $task["ID_task"]) {
			$grouped_tasks[] = $tasks;
			$tasks = array();
			$flag = $task["ID_task"];
		}
		
		// append (array) task to (array) group
		$tasks[] = $task;
	}
	
	// if value hasn't been pushed then push it now
	if ($tasks != array()) {
		$grouped_tasks[] = $tasks;
	}
	
	return $grouped_tasks;
}

// delete earlier tasks (metadata)
//$periodic_task_id is array
function removePeriodicTasks($database, $periodic_task_ids) {
	$query = "DELETE FROM periodic_task ".
				"WHERE generated_ID_task IN (";
	$query .= join(", ",$periodic_task_ids);
	$query .= ")";
	
	$result = $database->query($query);
	if ($result != true) {
		echo "Internal error";
		echo $query;
		exit(65);
	}
}

// delete additional tasks (user requested for smaller number tasks ahead)
function removeTasks($database, $task_ids) {
	// delete foreign key constraint
	removePeriodicTasks($database, $task_ids);
	
	$query = "DELETE FROM task ".
				"WHERE ID_task IN (";
	$query .= join(", ",$task_ids);
	$query .= ")";
	
	$result = $database->query($query);
	if ($result != true) {
		echo "Internal error\n";
		echo "$query\n";
		exit(69);
	}
	
	
}

// calculate next start and due date from current start and due date based on repeat time
function calculateNextTime($repeat_time, $pstart_date, $pdue_date) {
	if ($repeat_time > 0) {
		// $repeat_time is in minute
		$start_date = strtotime("+$repeat_time Minute ".$pstart_date);
		$due_date = strtotime("+$repeat_time Minute ".$pdue_date);
	} else if ($repeat_time == -3) {
		// every week
		$start_date = strtotime("+1 Day ".$pstart_date);
		$due_date = strtotime("+1 Day ".$pdue_date);
	} else if ($repeat_time == -4) {
		// every week
		$start_date = strtotime("+1 Week ".$pstart_date);
		$due_date = strtotime("+1 Week ".$pdue_date);
	} else if ($repeat_time == -5) {
		// when "last in month will be implement do this:"
		// $a_date = "2009-11-23";
		// $due_date = date("Y-m-t", strtotime($a_date));
		//(there will a problem when 2038 there is a solution)$d = new DateTime( '2009-11-23' ); echo $d->format( 'Y-m-t' ); // strtotime("-1 day", $firstOfNextMonth)
	
		// every month
		$start_date = strtotime("+1 Month ".$pstart_date);
		$due_date = strtotime("+1 Month ".$pdue_date);
	} else if ($repeat_time == -6) {
		// every year
		$start_date = strtotime("+1 Year ".$pstart_date);
		$due_date = strtotime("+1 Year ".$pdue_date);
	}
	
	// convert to form for write to database
	$start_date = date('Y-m-d H:i:s',$start_date);
	$due_date = date('Y-m-d H:i:s',$due_date);
	
	return array($start_date, $due_date);
}

// calculate and insert one task ahead of given start and due date
function insertOneTask($database, $previous_task, $start_date, $due_date) {
	// rewrite value from last task
	$repeat_time = $previous_task["repeat_time"];
	
	// rewrite because we will use it multiple times
	$user_id = $previous_task["ID_user"];
	$category_id = $previous_task["ID_category"];
	
	// insert statement for new task
	$query = "INSERT into task (ID_category, ID_user, title, description,".
				"start_date,due_date, estimated_time,".
				"repeat_time, repeat_ends, acknowledge,".
				"priority, reminder_email,reminder_sms,".
				"remindered_email,remindered_sms)".
			  "VALUES(".$category_id.", ".$user_id.", '".$previous_task["title"]."', '".$previous_task["description"]."',".
				"'".$start_date."','".$due_date."', ".$previous_task["estimated_time"].",".
				"'".$previous_task["repeat_time"]."','".$previous_task["repeat_ends"]."', ".$previous_task["acknowledge"].",". 
				"".$previous_task["priority"].", '".$previous_task["reminder_email"]."', ".$previous_task["reminder_sms"].",".
				"".$previous_task["remindered_email"].", ".$previous_task["remindered_sms"].")";
	
	// insert new task and check if task executed successfully
	$result = $database->query($query);
	if ($result != true) {
		echo "Internal error";
		echo $query;
		exit(43);
	}
	
	// get id (generated by auto_increment)
	$generated_task_id = mysql_insert_id();
	
	// ID_task
	$id_task = $previous_task["ID_task"];
	
	// add entry for this generated task
	$query = "INSERT INTO periodic_task (ID_task, generated_ID_task) VALUES ($id_task, $generated_task_id)";
//	echo $query;
	// insert this entry and check if it executed successfully
	$result = $database->query($query);
	if ($result != true) {
		echo "Internal error";
		exit(45);
	}
	return $generated_task_id;
}

// generate additional tasks and crop earlier tasks
function correctTasks($database, $days_ahead, $max_repeats) {
	// get periodic tasks as array
	$grouped_tasks = getPeriodicTasks($database);
	
	// save current time to variable (take as same time)
	$current_time = mktime(); // TODO
	//$current_time = strtotime("2012-05-14 20:21:00"); //'Y-m-d H:i:s
	
	echo "current_time $current_time\n"; // TODO
    // go over groups
    // find first task which has start time bigger then current time
    foreach ($grouped_tasks as $tasks) {
    	$first_task = null;
    	
    	// how much repetas must be generated
    	$estimated_repeats = $max_repeats;
    	
    	// earlier periodic tasks which waiting for deletion
    	$old_periodic_tasks = array();
    	
    	// flag when we can start count down for $estimated_repeats
    	$flag_countdown = false;
    	
    	// go over generated tasks for this periodic task
    	foreach($tasks as $generated_task) {
    		// convert time to absolute time (comparable with $current_time)
    		$start_date = strtotime($generated_task["start_date"]);
    		
    		//echo "cur $current_time tt $task_time \n";
    		//print_r($generated_task);
    		
    		// if we got a task which has start time bigger then current time
    		// save it to $first_task variable and start count down estimated tasks ahead
    		if ($current_time < $start_date && $flag_countdown == false) {
    			$flag_countdown = true;
    			$first_task = $generated_task["generated_ID_task"];
    		}
    		
    		// count down if there are additional tasks after first task
    		if ($flag_countdown == true) {
    			$estimated_repeats--;
    		} else {
    			// this is an old periodic task which has no sense
    			$old_periodic_tasks[] = $generated_task["generated_ID_task"];
    		}
    	}
    	
    	// rewrite repeat data from last task
	    $repeat_time = $generated_task["repeat_time"];
	    $repeat_never_ends = false;
		if ($generated_task["repeat_ends"] != "0000-00-00") {
    		// calculate absolute time
	    	$repeat_ends = strtotime($generated_task["repeat_ends"]);
		} else {
			$repeat_never_ends = true;
		}
		
	    // get last start and due date from last task
    	// these will be maybee updated if sole task has start_date earlier of current_time 
    	// rewrite for counting
    	$start_date = $generated_task["start_date"];;
    	$due_date = $generated_task["due_date"];;
    	// new start - step ahead then start
	    $nstart_date = $start_date;
	    $ndue_date = $due_date;
	    
	    // if we didn't find task which has start_date bigger then current
    	// generate additional tasks that we will find task with 
		// start time bigger then current time or met repeat_ends
		if ($first_task == null) {
	    	// generate additional tasks until we get task with start time bigger then current time
	    	while (strtotime($start_date) < $current_time) {
	    		// $generated_task is there because of additional fields in array
	    		list($nstart_date, $ndue_date) = calculateNextTime($repeat_time, $start_date, $due_date);
	    		
	    		// add task if repeat isn't met unless end with $estimated_repeats = 0;
	    		if (($repeat_never_ends == true) || (strtotime($nstart_date) < $repeat_ends)) {
	    			$start_date = $nstart_date;
	    			$due_date = $ndue_date;
	    			$task_id = insertOneTask($database, $generated_task, $start_date, $due_date);
	    			
	    			// if this task is earlier then current time delete it from periodic
	    			if (strtotime($start_date) < $current_time) {
	    				$old_periodic_tasks[] = $task_id;
	    			}
	    		}else {
	    			$estimated_repeats = 0;
	    			break;
    			}
	    	}
	    	
	    	// if start date of last task has 
	    	if (($repeat_never_ends == false) && (strtotime($nstart_date) >= $repeat_ends)) {
		    	$estimated_repeats = 0;
		    	} else {
	    		$estimated_repeats--;
	    	}
    	}
    	
    	// correct $estimated_repeats if repeat_ends is near 
    	if ($repeat_never_ends == false) {
    		// rewrite varieables for calculating (temporary variable)
    		$tstart_date = $start_date;
    		$tdue_date = $due_date;
    		
	    	// correct $estimated_repeats if repeat_ends is here
	    	for ($i=0; $i< $estimated_repeats; $i++) {
	    		// calculate next start date and due date
	    		list($tstart_date, $tdue_date) = calculateNextTime($repeat_time, $tstart_date, $tdue_date);
	    
	    		// if repeat ends is here break (with smaller estimated repeats)
	    		if (!(strtotime($tstart_date) < $repeat_ends)) {
	    			$estimated_repeats = $i;
	    			break;
	    		}
	    	} // end of for loop
    	}
    	
    	// currect number tasks ahead
    	if ($estimated_repeats < 0) {
    		// delete additional tasks ahead (user requested for less tasks ahead)
    		$task_ids = array();
    		foreach (array_slice($tasks, $estimated_repeats) as $task) {
    			$task_ids[] = $task["generated_ID_task"];
    		}
    		removeTasks($database, $task_ids);
    	} else {
	    	// ok we have now all periodic tasks with $max_repeats - $estimated_repeats tasks ahead
	    	// generate estimated
	    	for ($i=0; $i<$estimated_repeats; $i++) {
	    		list($start_date, $due_date) = calculateNextTime($repeat_time, $start_date, $due_date);
	    		insertOneTask($database,$generated_task, $start_date, $due_date);
	    	}	
    	}
    	
    	// correct earlier tasks from periodic_table which are before first task
    	// (this will also delete last task if repeat_ends is here)
    	if (count($old_periodic_tasks) > 0) {
    		removePeriodicTasks($database, $old_periodic_tasks);
    	}
    	
    	// report status
    	echo "periodic task has ID_task: ". $generated_task["ID_task"] . " old repeats: ".count($old_periodic_tasks) ." estimated repeats: $estimated_repeats\n";
    	
    	//insertTasksAfter($database, $generated_task, $days_ahead, $estimated_repeats);
    	
    } // end of foreach ($grouped_tasks as $tasks)
    
} // end of correctTasks function

correctTasks($database,$days_ahead,$max_repeats);
exit();

// TODO show 5 next tasks AND start task must be in less than 14 days

?>
