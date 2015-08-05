<?php

/**
 * @api {GET} /unit/:id 			Fetch a Unit by Id
 * @apiName fetchUnitById
 * @apiGroup Unit
 *
 * @apiParam {Number} id 			Units unique ID.
 * @apiParam {Number} channeldId 	Id of Parent Channel.
 *
 * @apiSuccess {Unit} 				Unit 	Unit and its Overlays
 *
 */
$app->get('/unit/:id(/:channelId)', function ($id, $channelId = 0) {
	include_once 'libs/unit.php';
	echo json_encode(getUnit($id, $channelId));
});

/**
 * @api {POST} /unit	 	Create a Unit
 * @apiName createUnit
 * @apiGroup Unit
 *
 *
 * @apiSuccess {Unit} Unit 	Unit
 * @apiPermission Author of the Unit
 *
 */
$app->post('/unit', function () use ($app) {
	include_once 'libs/unit.php';
	echo json_encode(createUnit($app->request->getBody()));
});

/**
 * @api {PUT} /unit	 	Update a Unit
 * @apiName updateUnit
 * @apiGroup Unit
 *
 *
 * @apiSuccess {Unit} Unit 	Unit
 * @apiPermission Author of the Unit
 * @apiDescription Here you can describe the function.
 *
 */
$app->put('/unit', function () use ($app) {
	include_once 'libs/unit.php';
	updateUnit($app->request->getBody());
});

/**
 * @api {PUT} /unit/parent	 	Update a Unit's Parent
 * @apiName updateUnitParent
 * @apiGroup Unit
 *
 *
 * @apiSuccess {Unit} Unit 	Unit
 * @apiPermission Author of the Unit
 * @apiDescription Here you can describe the function.
 *
 */
$app->put('/unit/parent', function () use ($app) {
	include_once 'libs/unit.php';
	updateUnitParent($app->request->getBody());
});

/**
 *
 * @api {DELETE} /unit/:id  		Delete a Unit
 * @apiName deleteUnit
 * @apiGroup Channel
 * @apiVersion 1.0.0
 * @apiDescription Delete this unit entirly. This means all Overlays and Items as well as well as from all Channels.
 *
 * @apiParam {Number} id   			Id of Unit to delete
 *
 * @apiPermission Author of the Parent Channel
 * @apiUse NotPermitted
 * @apiUse MissingParameter
 *
 */
$app->delete('/unit/:id', function ($id) use ($app) {
	include_once 'libs/unit.php';
	deleteUnit($id);
});
?>