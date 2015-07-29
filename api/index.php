<?php
require 'vendor/autoload.php';

$app = new \Slim\Slim();

include_once 'common.php';

include_once 'routes/user.php';
include_once 'routes/channel.php';
include_once 'routes/unit.php';

$app->run();

?>