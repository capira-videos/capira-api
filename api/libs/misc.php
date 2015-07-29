<?php

if(!defined('VALID_INCLUDE')) {
	exit;
}

if (!function_exists('boolval')) {
        function boolval($val) {
                return  ($val == true);
        }
}

function check_channel_privileges($id, $level)
{
	global $user;
	if(!$user->has_privilege($id, $level, true)) {
		header("HTTP/1.1 401 Unauthorized");
		echo NOT_PERMITTED;
		exit;
	}
}

function check_unit_privileges($id, $level)
{
	global $user;
	if(!$user->has_privilege($id, $level, false)) {
		header("HTTP/1.1 401 Unauthorized");
		echo NOT_PERMITTED;
		exit;
	}
}

function random_0_1()
{   // auxiliary function
    // returns random number with flat distribution from 0 to 1
    return (float)rand()/(float)getrandmax();
}

?>