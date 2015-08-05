<?php

/**
 *
 * @api {GET} /permissions/channel/:id 	Fetch all Authors of a Channel
 * @apiName fetchAuthorsByChannelId
 * @apiGroup Permissions
 *
 * @apiParam {Number} id 				Id of target Channel
 * @apiPermission Admin of target Channel
 *
 */
$app->get('/permissions/channel/:id', function ($id) use ($app, $user) {
	include_once 'libs/permissionManagement.php';
	$manager = new Permissions($user);
	echo json_encode($manager->list_channel_permissions($id));
});

/**
 *
 * @api {POST} /permissions/channel/  	Set Channel Permissions
 * @apiName fetchAuthorsByChannelId
 * @apiGroup Permissions
 *
 * @apiParam {Number} id 				Id of target Channel
 * @apiPermission Admin of target Channel
 *
 *
 */
$app->post('/permissions/channel/:channelId/author/:userId', function ($channelId, $userId) use ($app, $user) {
	include_once 'libs/permissionManagement.php';
	$manager = new Permissions($user);
	echo json_encode($manager->set_channel_permissions($channelId, $userId));
});

$app->post('/permissions/channel/:channelId/admin/:userId', function ($channelId, $userId) use ($app, $user) {
	include_once 'libs/permissionManagement.php';
	$manager = new Permissions($user);
	echo json_encode($manager->set_channel_permissions($channelId, $userId, true));
});

$app->delete('/permissions/channel/:channelId/author/:userId', function ($channelId, $userId) use ($app, $user) {
	include_once 'libs/permissionManagement.php';
	$manager = new Permissions($user);
	echo json_encode($manager->remove_channel_permissions($channelId, $userId));
});

$app->get('/permissions/unit/:id', function ($id) use ($app, $user) {
	include_once 'libs/permissionManagement.php';
	$manager = new Permissions($user);
	echo json_encode($manager->list_unit_permissions($id));
});

$app->post('/permissions/unit/:unitId/author/:userId', function ($channelId, $userId) use ($app, $user) {
	include_once 'libs/permissionManagement.php';
	$manager = new Permissions($user);
	echo json_encode($manager->set_unit_permissions($unitId, $userId));
});

$app->post('/permissions/unit/:unitId/admin/:userId', function ($channelId, $userId) use ($app, $user) {
	include_once 'libs/permissionManagement.php';
	$manager = new Permissions($user);
	echo json_encode($manager->set_unit_permissions($unitId, $userId, true));
});

$app->delete('/permissions/unit/:unitId/author/:userId', function ($unitId, $userId) use ($app, $user) {
	include_once 'libs/permissionManagement.php';
	$manager = new Permissions($user);
	echo json_encode($manager->remove_unit_permissions($unitId, $userId));
});

?>
