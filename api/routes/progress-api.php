<?php
require 'libs/progress.php';

/**
 *
 * @api {POST} /progress/unit 	Unit Progress
 * @apiName unitProgress
 * @apiGroup Progress
 * @apiVersion 1.0.0
 *
 * @apiParam {String} title   	Title of new Channel
 * @apiParam {Number} parent  	Parent Id of new Channel
 *
 * @apiPermission Anonymous User
 *
 * @apiParamExample {json} Request-Example:
 *     {
 *       "unit": "42"
 *     }
 *
 * @apiSuccessExample {json} Success-Response:
 *     HTTP/1.1 200 OK
 * 
 * @apiErrorExample {json} Error-Response:
 *     HTTP/1.1 401 Unauthorized
 *     {
 *       "error": "You are not logged in!"
 *     }
 *
 */
$app->post('/progress/unit', function () {
	$progress = json_decode(file_get_contents("php://input"),true);
    setProgressViewed($progress['unit']);
});

$app->post('/progress/layer', function () {
	$progress = json_decode(file_get_contents("php://input"),true);
	setProgress($progress['layer'], $progress['time'], $progress['success'], $progress['score']);
});

$app->get('/progress/channel/:id', function ($id) {
	getProgressChannel($id);
});

$app->get('/progress/unit/:id(/details)', function ($id) {
	getProgressUnit($id, (isset($_GET['details']) && $_GET['details'] == 1) ? true : false);
});



?>