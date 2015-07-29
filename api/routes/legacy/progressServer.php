<?php
require 'common.php';
require 'libraries/progress.php';



if(!$user->check_angular_request()) {
	exit;
}

if(!$user->logged_in()&&! ($user->userid()>0)) {
	exit;
}



$method = $_SERVER['REQUEST_METHOD'];
$request = explode("/", substr(@$_SERVER['PATH_INFO'], 1));

switch ($method) {
	case 'POST' :
		$data = json_decode(file_get_contents("php://input"), true);
		if(isset($data['unit'])) {
			setProgressViewed($data['unit']);
		}
		if(isset($data['layer'])) {
			setProgress($data['layer'], $data['time'], $data['success'], $data['score']);
		}
		break;
	case 'GET' :
		if(isset($_GET['channel'])) {
			getProgressChannel($_GET['channel']);
		} else {
			getProgressUnit($_GET['unit'], (isset($_GET['details']) && $_GET['details'] == 1) ? true : false);
		}
		break;
}

?>