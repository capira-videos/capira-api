<?php

require('common.php');
require('libraries/search.php');

if(!$user->check_angular_request()) {
	exit;
}

$page = 0;
if(isset($_GET['page'])) {
	$page = intval($_GET['page']);
}
echo json_encode(queryString($_GET['query'], $page*50, 50));

?>