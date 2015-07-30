<?php

/**
 *
 * @apiDefine Unauthorized Operation 
 *
 */

include_once 'libs/channel.php';

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
	$folder = json_decode(file_get_contents("php://input"),true);
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
	$folder = json_decode(file_get_contents("php://input"),true);
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
	deleteChannel($id);
});


$app->post('/channel/:id/sorting', function ($id) {
    updateOrder();
});




?>