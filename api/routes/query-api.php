<?php

/**
 *
 * @api {GET} /query/users/:query 		Query for Users
 * @apiName queryForUsers
 * @apiGroup Query
 * @apiPermission logged-in
 * 
 */
$app->get('/query/users/:query', function ($query) use ($user){
	include_once 'libs/users.php';
	echo json_encode(queryForUsers($query));
});


/**
 *
 * @api {GET} /query/content/:query 	Query Content
 * @apiName queryForContent
 * @apiGroup Query
 * @apiPermission logged-in
 * @apiDescription Querys for Units and Channels.
 * 
 */
$app->get('/query/content/:query(:/page)', function ($query) use ($user,$page=0){
	require('libs/search.php');
	$page = 0;
	echo json_encode(queryString($_GET['query'], $page*50, 50));
});

?>