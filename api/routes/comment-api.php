<?php

$app->get('/comments/unit/:id', function ($id) {
	include_once 'libs/comment.php';
	echo json_encode(fetch_comments_unit($id));
});

$app->post('/comments/unit/', function ($id) {
	include_once 'libs/comment.php';

	$request = get_request_json();
	echo json_encode(insert_comment($userId, $request['comment'], $request['unit']));
});

$app->put('/comments/unit/', function ($id) {
	include_once 'libs/comment.php';

	$request = get_request_json();
	echo json_encode(update_comment($userId, $request['comment'], $request['unit']));
});

$app->post('/comments/unit/response', function ($id) {
	include_once 'libs/comment.php';
	$request = get_request_json();
	echo json_encode(insert_response($userId, $request['comment'], $request['unit']));
});

$app->post('/comments/rate', function ($id) {
	include_once 'libs/comment.php';
	$request = get_request_json();
	echo json_encode(rate_comment($userId, $request['comment'], $request['unit']));
});