<?php

/**
 *
 * @apiDefine Unauthorized Operation
 *
 */

/**
 * @api {GET} /channel/:id Fetch a Channel by Id
 * @apiName fetchChannelById
 * @apiGroup Channel
 * @apiVersion 1.0.0
 *
 * @apiParam {Number} id Channels unique ID.
 *
 * @apiSuccess {Channel} Channel Channel and its Sub-Channels and Units
 *
 * @apiPermission none
 * @apiSampleRequest http://192.168.178.83:8888/api/channel/
 */
$app->get('/channel/:id', function ($id) {
	include_once 'libs/channel.php';
	echo json_encode(getChannel($id));
});

/**
 * @api {GET} /channel/editor/:id Fetch a Channel with current User's Permissions by Channel's Id
 * @apiName fetchChannelWithPermissionsById
 * @apiGroup Channel
 * @apiVersion 1.0.0
 *
 * @apiParam {Number} id Channels unique ID.
 *
 * @apiSuccess {Channel} Channel Channel and its Sub-Channels and Units
 *
 * @apiPermission authenticated User
 * @apiDescription pretty much the same API as fetchChannelById, except for additonally fields editor, author and parentAdmin
 */
$app->get('/channel/editor/:id', function ($id) {
	include_once 'libs/channel.php';
	echo json_encode(getChannel($id, true));
});

/**
 *
 * @api {POST} /channel/ 		Create a new channel
 * @apiName createChannel
 * @apiGroup Channel
 * @apiVersion 1.0.0
 *
 * @apiParam {String} title   	Title of new Channel
 * @apiParam {Number} parent  	Parent Id of new Channel
 *
 * @apiPermission Author of the parent Channel
 *
 *
 * @apiParamExample {json} Request-Example:
 *     {
 *       "title": "Capira Channel",
 *       "parent": "314"
 *     }
 *
 * @apiSuccessExample {json} Success-Response:
 *     HTTP/1.1 201 OK
 *     {
 *       "title": "Capira Channel",
 *       "id": "42",
 *       "parent": "314"
 *     }
 *
 * @apiErrorExample {json} Error-Response:
 *     HTTP/1.1 401 Unauthorized
 *     {
 *       "error": "You are not permitted to do this operation!"
 *     }
 *
 */
$app->post('/channel', function () use ($app) {
	include_once 'libs/channel.php';
	$channel = $app->request->getBody();
	echo json_encode(createChannel($channel));
});

/**
 *
 * @api {PUT} /channel/ 		Update a channel
 * @apiName updateChannel
 * @apiGroup Channel
 * @apiVersion 1.0.0
 *
 * @apiParam {String} title   	Title of new Channel
 * @apiParam {Number} parent  	Parent Id of new Channel
 *
 * @apiPermission Author of the Channel
 * @apiSuccessExample {json} Success-Response:
 *  HTTP/1.1 200 OK
 *     {
 *       "title": "Capira Channel",
 *       "id": "42",
 *       "published": "1",
 *     }
 *
 */
$app->put('/channel', function () use ($app) {
	include_once 'libs/channel.php';
	$channel = $app->request->getBody();
	echo json_encode(updateChannel($channel));
});

/**
 *
 * @api {PUT} /channel/parent 		Update a channel parent
 * @apiName updateChannelParent
 * @apiGroup Channel
 * @apiVersion 1.0.0
 *
 * @apiParam {Number} id   	    Id of the Channel
 * @apiParam {Number} parent  	New Parent of the Channel
 *
 * @apiPermission Author of the Channel
 * @apiSuccessExample {json} Success-Response:
 *  HTTP/1.1 200 OK
 *     {
 *       "title": "Capira Channel",
 *       "id": "42",
 *       "published": "1",
 *     }
 *
 */
$app->put('/channel/parent', function () use ($app) {
	include_once 'libs/channel.php';
	$channel = $app->request->getBody();
	echo json_encode(updateChannelParent($channel));
});

/**
 *
 * @api {DELETE} /channel/:id 		Delete a channel
 * @apiName deleteChannel
 * @apiGroup Channel
 * @apiVersion 1.0.0
 *
 * @apiParam {Number} id   			Id of Channel to delete
 * @apiParamExample {json} Request-Example:
 *     {
 *       "id": "42",
 *       "parent": "314"
 *     }
 *
 * @apiPermission Author of the Parent Channel
 *
 * @apiErrorExample {json} Error-Response:
 *     HTTP/1.1 401 Unauthorized
 *     {
 *       "error": "You are not permitted to do this operation!"
 *     }
 *
 */
$app->delete('/channel', function () use ($app) {
	$channel = $app->request->getBody();
	include_once 'libs/channel.php';
	deleteChannel($channel);
});

$app->put('/channel/:id/sorting', function ($id) {
	include_once 'libs/channel.php';
	updateOrder();
});

?>