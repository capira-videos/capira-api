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
		echo 'Sie haben leider keine Rechte hierfür. <a href="javascript:history.back()">Zurück</a>';
		exit;
	}
}

function check_unit_privileges($id, $level)
{
	global $user;
	if(!$user->has_privilege($id, $level, false)) {
		echo 'Sie haben leider keine Rechte hierfür. <a href="javascript:history.back()">Zurück</a>';
		exit;
	}
}

function random_0_1()
{   // auxiliary function
    // returns random number with flat distribution from 0 to 1
    return (float)rand()/(float)getrandmax();
}

?>