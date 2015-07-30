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
	getFolder($id);
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
$app->post('/channel/', function () {
	include_once 'libs/channel.php';
	$folder = get_request_json();
	createFolder($folder);
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
 *       "parent": "314",
 *       "units": [],
 *       "channels":[],
 *     }
 *
 */
$app->put('/channel/', function () {
	include_once 'libs/channel.php';
	$folder = get_request_json();
	updateFolder($folder);
});

/**
 *
 * @api {DELETE} /channel/:id 		Delete a channel
 * @apiName deleteChannel
 * @apiGroup Channel
 * @apiVersion 1.0.0
 *
 * @apiParam {Number} id   			Id of Channel to delete
 *
 * @apiPermission Author of the Parent Channel
 *
 */
$app->delete('/channel/:id', function ($id) {
	include_once 'libs/channel.php';
	deleteChannel($id);
});

$app->put('/channel/:id/sorting', function ($id) {
	include_once 'libs/channel.php';
	updateOrder();
});

?>