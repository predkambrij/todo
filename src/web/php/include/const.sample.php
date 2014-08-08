<?php
/**
 * const.php
 *
 * This file is intended to group all constants to
 * make it easier for the site administrator to tweak
 * the login script.
 *
 */
 
/**
 * Database Constants - these constants are required
 * in order for there to be a successful connection
 * to the MySQL database. Make sure the information is
 * correct.
 */
define("DB_SERVER", "localhost");
define("DB_USER", "...");
define("DB_PASS", "...");
define("DB_NAME", "..");

// set your AccountSid and AuthToken from www.twilio.com/user/account
$AccountSid = "";
$AuthToken = "";

# get twilio phone number with messaging capability https://www.twilio.com/user/account/phone-numbers/incoming
$twilio_from = "";
$twilio_to = "";

/**
 * Database Table Constants - these constants
 * hold the names of all the database tables used
 * in the script.
 */
define("TBL_APP", "app_info");
define("TBL_CATEGORY",  "category");
define("TBL_TASK", "task");
define("TBL_USERS",  "user");
define("TBL_PERTASK",  "periodic_task");

/**
 * Cookie Constants - these are the parameters
 * to the setcookie function call, change them
 * if necessary to fit your website. If you need
 * help, visit www.php.net for more info.
 * <http://www.php.net/manual/en/function.setcookie.php>
 */
define("COOKIE_EXPIRE", 60*60*24*30);  // 30 days by default
define("COOKIE_PATH", "/");  // Avaible in whole domain

/**
 * Email Constants - these specify what goes in
 * the from field in the emails that the script
 * sends to users, and whether to send a
 * welcome email to newly registered users.
 */
define("EMAIL_FROM_NAME", "TODO.si");
define("EMAIL_FROM_ADDR", "no-reply@todo.si");
define("EMAIL_WELCOME", true);

/**
 * This constant forces all users to have
 * lowercase usernames, capital letters are
 * converted automatically.
 */
define("ALL_LOWERCASE", false);

define("DB_CONNECT", true);


//date_default_timezone_set('UTC');

$reminderTypes 		= array("None","SMS", "Email");
$categoryTypes  	= array("ScheduledTasks", "Task List");
$priorities			= array(1,2,3,4,5);
$taskReminders		= array("None", "At the time of event", "15 minutes before", "An hour before", "1 day before", "2 days before");
$repeats			= array("Never", "Every hour", "Every 2 hours", "Every day", "Every week", "Every month", "Every Year");

/**
 * Useful function library
 */
include('utils.php');

/**
 * Report all errors
 * For development purposes only
 */
// error_reporting(E_ALL);
// error_reporting(0);
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 'stdout');

// set default timezone
date_default_timezone_set("Europe/Ljubljana");

?>
