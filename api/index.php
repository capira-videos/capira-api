<?php
/*
 * If you prefer to use a PHP session,
 * you must configure and start a native PHP session with session_start()
 * before you instantiate the Slim application.
 * ( http://docs.slimframework.com/sessions/native/ )
 */
include_once 'common.php';

require 'vendor/autoload.php';
require 'middleware.php';

$app = new \Slim\Slim();
$app->add(new \Slim\middleware\ContentTypes());
$app->contentType('application/json');

include_once 'routes/user-api.php';
include_once 'routes/channel-api.php';
include_once 'routes/unit-api.php';

$app->run();

?>