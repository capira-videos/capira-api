<?php
/**
 *
 * @api {POST} /user/login Log into Capira User Account
 * @apiName login
 * @apiGroup User-API
 *
 * @apiParam {String} name 		Username of the Account
 * @apiParam {String} password 	Password of the Account
 *
 * @apiSuccess {User} User The User 
 * @apiPermission none
 *
 */
$app->post('/user/login', function () use ($user) {
	$request = json_decode(file_get_contents("php://input"), true);
    echo $user->login($request['name'], $request['password']);
});


/**
 *
 * @api {POST} /user/logout Log out of Capira User Account
 * @apiName logout
 * @apiGroup User-API
 *
 * @apiPermission none
 *
 */
$app->post('/user/logout', function () use ($user) {
	echo $user->logout();
});

/**
 *
 * @api {POST} /user/register Create a Capira User Account
 * @apiName create
 * @apiGroup User-API
 *
 * @apiParam {String} email 	Password of the Account
 * @apiParam {String} name 		Username of the Account
 * @apiParam {String} password 	Password of the Account
 *
 * @apiSuccess {User} User The User 
 * @apiPermission none
 *
 */
$app->post('/user/register', function () use ($user) {
	$request = json_decode(file_get_contents("php://input"), true);
	echo $user->register($request['name'], $request['email'], $request['password']);
});

/**
 *
 * @api {GET} /user/profile 	Fetch profile
 * @apiName fetchProfile
 * @apiGroup User-API
 * @apiPermission logged-in
 * 
 */
$app->get('/user/profile', function () use ($user){
	echo $user->json_object();
});




