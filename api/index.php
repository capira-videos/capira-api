<?php
/*
 * If you prefer to use a PHP session,
 * you must configure and start a native PHP session with session_start()
 * before you instantiate the Slim application.
 * ( http://docs.slimframework.com/sessions/native/ )
 */

include_once 'common.php';

require 'vendor/autoload.php';

$app = new \Slim\Slim();
$app->add(new \Slim\Middleware\ContentTypes());
$app->contentType('application/json; charset=utf-8');

include 'routes/user-api.php';
include 'routes/channel-api.php';
include 'routes/unit-api.php';
include 'routes/permissions-api.php';
//TODO: Implement Comments-API
//TODO: Implement Progress-API

$app->run();

//TODO: Remove redirects from Server - direct links are 1.4s faster then the redirected

?>