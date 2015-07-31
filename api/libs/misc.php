<?php

if (!defined('VALID_INCLUDE')) {
	exit;
}

if (!function_exists('boolval')) {
	function boolval($val) {
		return ($val == true);
	}
}

function check_channel_privileges($id, $level) {
	global $user;
	if (!$user->has_privilege($id, $level, true)) {
		header("HTTP/1.1 401 Unauthorized");
		echo NOT_PERMITTED;
		exit;
	}
}

function check_unit_privileges($id, $level) {
	global $user;
	if (!$user->has_privilege($id, $level, false)) {
		header("HTTP/1.1 401 Unauthorized");
		echo NOT_PERMITTED;
		exit;
	}
}

function check_logged_in() {
	global $user;
	if (!$user->logged_in() && !($user->userid() > 0)) {
		header("HTTP/1.1 401 Unauthorized");
		echo NOT_LOGGED_IN;
		exit;
	}
}

function check_valid_email($email) {
	if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
		header("HTTP/1.1 406 Not Acceptable");
		echo NOT_VALID_EMAIL;
		exit;
	}
}

function malformed_request($string) {
	header("HTTP/1.1 400 Bad Request");
	echo MALFORMED_REQUEST;
	exit;
}

function random_0_1() {
	// auxiliary function
	// returns random number with flat distribution from 0 to 1
	return (float) rand() / (float) getrandmax();
}

function get_request_json() {
	return json_decode(file_get_contents("php://input"), true);
}

?>