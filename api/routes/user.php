<?php
/**
 *
 * @api {POST} /login Log into Capira Account
 * @apiName login
 * @apiGroup User
 *
 * @apiParam {String} name 		Username of the Account
 * @apiParam {String} password 	Password of the Account
 *
 * @apiSuccess {User} User The User 
 * @apiPermission none
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
 *
 * @apiPermission none
 *
 */
$app->post('/logout', function () use ($user) {
	echo $user->logout();
});

/**
 *
 * @api {POST} /register Create a Capira Account
 * @apiName create
 * @apiGroup User
 *
 * @apiParam {String} email 	Password of the Account
 * @apiParam {String} name 		Username of the Account
 * @apiParam {String} password 	Password of the Account
 *
 * @apiSuccess {User} User The User 
 * @apiPermission none
 *
 */
$app->post('/register', function () use ($user) {
	$request = json_decode(file_get_contents("php://input"), true);
	echo $user->register($request['name'], $request['email'], $request['password']);
});

/**
 *
 * @api {GET} /me 	Fetch profile
 * @apiName fetchProfile
 * @apiGroup User
 * @apiPermission logged-in
 * 
 */
$app->get('/me', function () use ($user){
	echo $user->json_object();
});




