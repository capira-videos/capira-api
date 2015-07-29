<?php

include_once 'libs/channel.php';

/**
 * @api {GET} /channel/:id Fetch a Channel by Id
 * @apiName fetchChannelById
 * @apiGroup Channel-API
 *
 * @apiParam {Number} id Channels unique ID.
 *
 * @apiSuccess {Channel} Channel Channel and its Sub-Channels and Units
 *
 * @apiPermission none
 * 
 */
$app->get('/channel/:id', function ($id) {
    getFolder($id);
});


/**
 *
 * @api {POST} /channel/ 		Create a new channel
 * @apiName createChannel
 * @apiGroup Channel-API
 *
 * @apiParam {String} title   	Title of new Channel
 * @apiParam {Number} parent  	Parent Id of new Channel
 *
 * @apiPermission Author of the parent Channel
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
 * @apiGroup Channel-API
 *
 * @apiParam {String} title   	Title of new Channel
 * @apiParam {Number} parent  	Parent Id of new Channel
 *
 * @apiPermission Author of the Channel
 *
 */
$app->put('/channel/', function () {
	$folder = json_decode(file_get_contents("php://input"),true);
    updateFolder($folder);
});


/**
 *
 * @api {DELETE} /channel/ 		DELETE a channel
 * @apiName deleteChannel
 * @apiGroup Channel-API
 *
 * @apiParam {Number} id   		Id of Channel to delete
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