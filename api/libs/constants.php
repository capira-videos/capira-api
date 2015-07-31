<?php

if (!defined('VALID_INCLUDE')) {
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

// Error Messages
define('NOT_PERMITTED', '{"error": "You are not permitted to do this operation!"}');
define('NOT_LOGGED_IN', '{"error": "You are not logged in!"}');
define('NOT_VALID_EMAIL', '{"error": "This is not a valid Email address!"}');
define('NOT_NAME_UNIQUE', '{"error": "This username already exists!"}');
define('NOT_EMAIL_UNIQUE', '{"error": "This email address is already in use!"}');
define('MALFORMED_REQUEST', '{"error": "This Request was not valid!"}');

?>