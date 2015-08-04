<?php

/**
 * @apiDefine NotPermitted
 * @apiErrorExample {json} Error-Response:
 *     Error 401: Unauthorized
 *     {
 *       "error": "You are not permitted to do this operation!"
 *     }
 *
 *
 */

/**
 * @apiDefine MissingParameter
 * @apiErrorExample {json} Error-Response:
 *     Error 400: Bad Request
 *     {
 *       "error": "This Request was not valid!"
 *     }
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
 * @apiSuccess {Object} Channel Channel and its Sub-Channels and Units
 *
 * @apiPermission none
 *
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
 * @apiSuccess {Object} Channel Channel and its Sub-Channels and Units
 *
 * @apiPermission authenticated User
 * @apiDescription pretty much the same API as fetchChannelById, except for additonally fields editor, author and parentAdmin
 *
 */
$app->get('/channel/editor/:id', function ($id) {
	include_once 'libs/channel.php';
	echo json_encode(getChannel($id, true));
});

/**
 *
 * @api {POST} /channel 		Create a new channel
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
 *     201 OK
 *     {
 *       "title": "Capira Channel",
 *       "id": "42",
 *       "parent": "314"
 *     }
 * @apiUse NotPermitted
 * @apiUse MissingParameter
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
 * @apiUse NotPermitted
 * @apiUse MissingParameter
 * @apiSuccessExample {json} Success-Response:
 *  200 OK
 *     {
 *       "title": "Capira Channel",
 *       "id": "42",
 *       "published": "1",
 *     }
 *
 */
$app->put('/channel', function () use ($app) {
	include_once 'libs/channel.php';
	echo json_encode(updateChannel($app->request->getBody()));
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
 * @apiUse NotPermitted
 * @apiUse MissingParameter
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
	echo json_encode(updateChannelParent($app->request->getBody()));
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
 *     }
 *
 * @apiPermission Author of the Parent Channel
 * @apiUse NotPermitted
 * @apiUse MissingParameter
 *
 */
$app->delete('/channel', function () use ($app) {
	include_once 'libs/channel.php';
	deleteChannel($app->request->getBody());
});

/**
 *
 * @api {PUT} /channel/sorting 		Sort the Channel
 * @apiName sortChannel
 * @apiGroup Channel
 * @apiVersion 1.0.0
 * @apiDescription Sort the order of Units and Subchannels of a Channel.
 * You put in a whole channel and the Server will iterate over all Subchannels and Units and will set their view_index to the according index in the given array.
 *
 *
 * @apiPermission Author of the Channel
 * @apiUse NotPermitted
 * @apiUse MissingParameter
 *
 */
$app->put('/channel/sorting', function () use ($app) {
	include_once 'libs/channel.php';
	updateOrder($app->request->getBody());
});

/**
 *
 * @api {DELETE} //channel/:channelId/unit/:unitId  		Delete a Unit from Folder
 * @apiName deleteUnitFromFolder
 * @apiGroup Channel
 * @apiVersion 1.0.0
 *
 * @apiParam {Number} id   			Id of Unit to delete
 * @apiParamExample {json} Request-Example:
 *     {
 *       "id": "42",
 *     }
 *
 * @apiPermission Author of the Parent Channel
 * @apiUse NotPermitted
 * @apiUse MissingParameter
 *
 */
$app->delete('/channel/:channelId/unit/:unitId', function ($unitId, $channelId) use ($app) {
	include_once 'libs/unit.php';
	deleteUnitFromChannel($unitId, $channelId);
});

?>