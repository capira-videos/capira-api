<?php

/**
 *
 *
 * @api {POST} /signup Create a Capira Account
 * @apiName create
 * @apiGroup User
 * @apiVersion 1.0.0
 *
 * @apiParam {String} email 		Email Address of the Account
 * @apiParam {String} name 			Username of the Account
 * @apiParam {String} password 		Password of the Account
 *
 * @apiSuccess (Created 201) {User} User 	The created User 
 * @apiPermission none
 * @apiDescription Create a Capira Account. If successful, you are logged in. name and password must be unique.   
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
 * @api {POST} /login Log into Capira Account
 * @apiName login
 * @apiGroup User
 * @apiVersion 1.0.0
 *
 * @apiParam {String} name 		Username of the Account
 * @apiParam {String} password 	Password of the Account
 *
 * @apiSuccess {User} User The User 
 * @apiPermission 	  none
 *
 */
$app->post('/login', function () use ($user) {
	$request = json_decode(file_get_contents("php://input"), true);
    echo $user->login($request['name'], $request['password']);
});


/**
 *
 * @api {POST} /logout Log out of Capira Account
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
$app->get('/me', function () use ($user){
	echo $user->json_object();
});




