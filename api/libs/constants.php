<?php

if(!defined('VALID_INCLUDE')) {
	exit;
}

// Privileges
define('ANONYMOUS', -1); // essentially only viewing
define('ADMIN', 100); // godmode
define('USER', 1);
define('AUTHOR', 10);
define('UNIT_ADMIN', 10);

// Return values
define('DUPLICATE_NAME', -2);
define('DUPLICATE_EMAIL', -1);
define('INVALID_EMAIL', -3);
define('SUCCESS', 42);
define('SUCCESS_REGISTER', 43);
define('FAILED', -42);
define('LOGGED_OUT', 13);

?>