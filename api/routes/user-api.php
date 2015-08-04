<?php

/**
 *
 *
 * @api {POST} /signup Create a Capira User
 * @apiName createUser
 * @apiGroup User
 * @apiVersion 1.0.0
 *
 * @apiParam {String} email 		Email Address of the User
 * @apiParam {String} name 			Username of the User
 * @apiParam {String} password 		Password of the User
 *
 * @apiSuccess (Created 201) {User} User 	The created User
 * @apiPermission none
 * @apiDescription Create a Capira User. If successful, you are logged in. name and password must be unique.
 * @apiParamExample {json} Request-Example:
 *     {
 *       "name": "CapiraUser",
 *       "password": "<<supersecretpassword>>",
 *       "email": "user@capira.de"
 *     }
 *
 * @apiSuccessExample {json} Success-Response:
 *     HTTP/1.1 201 Created
 *     {
 *       "name": "CapiraUser",
 *       "id": "12",
 *       "email": "user@capira.de"
 *     }
 *
 * @apiErrorExample {json} Error-Response:
 *     HTTP/1.1 406 Not Acceptable
 *     {
 *       "error": "This name already exists!"
 *     }
 *
 * @apiErrorExample {json} Error-Response:
 *     HTTP/1.1 406 Not Acceptable
 *     {
 *       "error": "This is not a valid Email address!"
 *     }
 *
 * @apiErrorExample {json} Error-Response:
 *     HTTP/1.1 406 Not Acceptable
 *     {
 *       "error": "This email address is already in use!"
 *     }
 *
 */
$app->post('/signup', function () use ($user) {
	$request = json_decode(file_get_contents("php://input"), true);
	$user->register($request['name'], $request['email'], $request['password']);
	header("HTTP/1.1 201 Created");
	echo $user->json_object();
	exit;
});

/**
 *
 * @api {POST} /login Log in as Capira User
 * @apiName login
 * @apiGroup User
 * @apiVersion 1.0.0
 *
 * @apiParam {String} name 		Username of the User
 * @apiParam {String} password 	Password of the User
 *
 * @apiSuccess {Object} User The User
 * @apiPermission 	  none
 *
 */
$app->post('/login', function () use ($app, $user) {
	$channel = $app->request->getBody();
	echo $user->login($channel['name'], $channel['password']);
	//TODO: unset password ?
});

/**
 *
 * @api {POST} /logout Log out of Capira.
 * @apiName logout
 * @apiGroup User
 * @apiVersion 1.0.0
 *
 * @apiPermission Authenticated User
 *
 */
$app->post('/logout', function () use ($user) {
	echo $user->logout();
});

/**
 *
 * @api {GET} /me 	Fetch profile
 * @apiName fetchProfile
 * @apiGroup User
 * @apiVersion 1.0.0
 * @apiPermission Authenticated User
 *
 */
$app->get('/me', function () use ($user) {
	echo $user->json_object();
});

/**
 *
 * @api {GET} /users/:query 	Query for Users by name and email
 * @apiName queryForUsers
 * @apiGroup User
 * @apiVersion 1.0.0
 * @apiPermission Authenticated User
 *
 */
$app->get('/users/:query', function ($query) {
	include_once 'libs/users.php';
	echo json_encode(queryForUsers($query));
});
