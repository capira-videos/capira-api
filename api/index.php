<?php
require 'vendor/autoload.php';

$app = new \Slim\Slim();

include_once 'common.php';

include_once 'routes/user-api.php';
include_once 'routes/channel-api.php';
include_once 'routes/unit-api.php';

$app->run();

?>