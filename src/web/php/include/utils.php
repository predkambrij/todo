<?php
	// example input: 2003-05-05 00:00:00
	// example output: 31.1.2011 23:55
	function convertDateAndTimeDBtoUI($databaseFormat) {
		list($date, $time) = explode(" ", $databaseFormat);
		list($year, $month, $day) = explode("-", $date);
		list($hour, $min, $sec) = explode(":", $time);
		
		return sprintf("%d.%d.%d %d:%02d",(int)$day,(int)$month,(int)$year,
														(int)$hour,(int)$min);
	}
	// example input: Monday
	// example output: Mon
	function formatDayInWeek($input) {
		switch ($input) {
			case "Monday":
				return "Mon";
			case "Tuesday":
				return "Tue";
			case "Wednesday":
				return "Wed";
			case "Thursday":
				return "Thu";
			case "Friday":
				return "Fri";
			case "Saturday":
				return "Sat";
			case "Sunday":
				return "Sun";
			default:
				return $input;
		}
	}

	// example input: 2003-05-05 00:00:00 2003-05-05 00:00:00
	// example output:
	// same day:
	// fri 17.2 20:00-23:55
	// same time:
	// fri 17.2 23:55
	// other:
	// fri 17.2 20:00 - sat 18.2 23:55
	function formatStartDueTime($startdate, $enddate) {
		list($sdate, $stime) = explode(" ", $startdate);
		list($syear, $smonth, $sday) = explode("-", $sdate);
		list($shour, $smin, $ssec) = explode(":", $stime);
	
		list($edate, $etime) = explode(" ", $enddate);
		list($eyear, $emonth, $eday) = explode("-", $edate);
		list($ehour, $emin, $esec) = explode(":", $etime);
		
		$sdayinweek= date("l", mktime($shour, $smin, $ssec, $smonth, $sday, $syear));
		$edayinweek= date("l", mktime($ehour, $emin, $esec, $emonth, $eday, $eyear));
		if ($sday == $eday && $smonth == $emonth && $syear == $eyear &&
				$shour==$ehour && $smin==$emin) {
			return sprintf("%s %d.%d %d:%02d",formatDayInWeek($sdayinweek),
				(int)$sday,(int)$smonth, (int)$shour,(int)$smin);
			
		} else if ($sday == $eday && $smonth == $emonth && $syear == $eyear) {
			return sprintf("%s %d.%d %d:%02d-%d:%02d",formatDayInWeek($sdayinweek)
					,(int)$sday,(int)$smonth, (int)$shour,(int)$smin,(int)$ehour,
																(int)$emin);
		} else {
			return sprintf("%s %d.%d - %s %d.%d",
				formatDayInWeek($sdayinweek),(int)$sday,(int)$smonth, 
				formatDayInWeek($edayinweek),(int)$eday,(int)$emonth);
		}
	}
	
	// example input: 2003-05-05 00:00:00
	// example output: 5.5.2003
	function convertDateDBtoUI($databaseFormat) {
		list($date, $time) = explode(" ", $databaseFormat);
		list($year, $month, $day) = explode("-", $date);
		list($hour, $min, $sec) = explode(":", $time);
	
		return sprintf("%d.%d.%d",(int)$day,(int)$month,(int)$year);
	}
	
 	// example input: 2003-05-05
 	// example output: 31.1.2011
	function convertDateFormatDBtoUI($date) {
		list($year, $month, $day) = explode("-", $date);
		
		return sprintf("%d.%d.%d",(int)$day,(int)$month,(int)$year);
	}
		
	// example input: 31.1.2011 23:55
	// example output: 2003-05-05 00:00:00
	function convertDateAndTimeUItoDB($databaseFormat) {
		list($date, $time) = explode(" ", $databaseFormat);
		list($day,$month,$year) = explode(".", $date);
		if ($time != null) {
			list($hour, $min) = explode(":", $time);
		} else {
			$hour = 0;
			$min = 0;
		}
		
		return sprintf("%4d-%02d-%02d %02d:%02d:00",(int)$year,(int)$month,
												(int)$day,(int)$hour,(int)$min);
	}
	
 	// example input: 31.1.2011
 	// example output: 2003-05-05
	function convertDateFormatUItoDB($date) {
		list($day,$month,$year) = explode(".", $date);
	
		return sprintf("%4d-%02d-%02d",(int)$year,(int)$month,(int)$day);
	}
	
	// example input: 31.1.2011
	// example output: 2003-05-05
	function convertDateUItoDB($databaseFormat) {
		list($day,$month,$year) = explode(".", $databaseFormat);
	
		return sprintf("%4d-%02d-%02d",(int)$year,(int)$month, (int)$day);
	}
	
	function reminderUItoDB($rmd){
		global $taskReminders;
		if ($rmd == $taskReminders[0]) {
			$rmd = -1;
		} else if ($rmd == $taskReminders[1]) {
			$rmd = 0;
		} else if($rmd == $taskReminders[2]){
			$rmd = 15;
		} else if($rmd == $taskReminders[3]){
			$rmd = 60;
		}else if($rmd == $taskReminders[4]){
			$rmd = 1440;
		}else if($rmd == $taskReminders[5]){
			$rmd = 2880;
		}else{
			$rmd = -1;
		}
		return $rmd;
	}
	
	function reminderDBtoUI($rmd){
		global $taskReminders;
		if ($rmd == -1) {
			$rmd = $taskReminders[0];
		} else if ($rmd == 0) {
			$rmd = $taskReminders[1];
		} else if($rmd == 15){
			$rmd = $taskReminders[2];
		} else if($rmd == 60){
			$rmd = $taskReminders[3];
		}else if($rmd == 1440){
			$rmd = $taskReminders[4];
		}else if($rmd == 2880){
			$rmd = $taskReminders[5];
		}else{
			$rmd = $taskReminders[0];
		}
		return $rmd;
	}
	
	function repeatDBtoUI($rpt){
		global $repeats;
		if ($rpt == 0) {
			$rpt = $repeats[0];
		}else if ($rpt == 60) {
			$rpt = $repeats[1];
		}else if ($rpt == 120) {
			$rpt = $repeats[2];
		}else if ($rpt == -3) {
			$rpt = $repeats[3];
		}else if ($rpt == -4) {
			$rpt = $repeats[4];
		}else if ($rpt == -5) {
			$rpt = $repeats[5];
		}else if ($rpt == -6) {
			$rpt = $repeats[6];
		}else{
			$rpt = $repeats[0];
		}
		return $rpt;
	}
	
	function repeatUItoDB($rpt){
		global $repeats;
		if ($rpt == $repeats[0]) {
			$rpt = 0;
		}else if ($rpt == $repeats[1]) {
			$rpt = 60;
		}else if ($rpt == $repeats[2]) {
			$rpt = 120;
		}else if ($rpt == $repeats[3]) {
			$rpt = -3;
		}else if ($rpt == $repeats[4]) {
			$rpt = -4;
		}else if ($rpt == $repeats[5]) {
			$rpt = -5;
		}else if ($rpt == $repeats[6]) {
			$rpt = -6;
		}else{
			$rpt = $repeats[0];
		}
		return $rpt;
	}
?>