<?php 

session_set_cookie_params(3600*24*30,'/');
// important
session_start();

error_reporting(E_ALL);
ini_set("display_errors", 1);

define('VALID_INCLUDE', true);

$mysqli = new mysqli('sql247.your-server.de', 'capira_6_w', 'WBX1V18QCUWZk2DJ', 'capira_db6');
if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}

if (!$mysqli->set_charset("utf8")) {
    printf("Error loading character set utf8: %s\n", $mysqli->error);
}

require('libs/constants.php');
require('libs/database.php');
require('libs/misc.php');
require('libs/password.php');
require('libs/user.php');

$user = new User();




//delete anonymous _inactive_ users from today
$query = "DELETE FROM Users USING Users
LEFT JOIN UserData
	ON UserData.id=Users.id
LEFT JOIN UnitProgress
	ON UnitProgress.userId=Users.id
        WHERE ISNULL(UserData.name)
        	AND ISNULL(UnitProgress.userId)
        	AND DATE_SUB(CURRENT_TIMESTAMP,INTERVAL 60*60 SECOND) >= last_login";



/* Prepared statement, stage 1: prepare */
if (!($mysqli->query($query))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;

}

//delete 30 day old anonymous _active_ users
$query = "DELETE FROM Users USING Users LEFT JOIN UserData ON UserData.id=Users.id WHERE ISNULL(UserData.name) AND DATE_SUB(CURDATE(),INTERVAL 30 DAY) >= last_login";

/* Prepared statement, stage 1: prepare */
if (!($mysqli->query($query))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}

?>
