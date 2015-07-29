<?php
/**
 *
 * @api {POST} /signup Create a Capira Account
 * @apiName create
 * @apiGroup User
 *
 * @apiParam {String} email 	Password of the Account
 * @apiParam {String} name 		Username of the Account
 * @apiParam {String} password 	Password of the Account
 *
 * @apiSuccess {User} User The User 
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
 *     HTTP/1.1 200 SUCCESS
 *     {
 *       "name": "CapiraUser",
 *       "id": "12",
 *       "email": "user@capira.de"
 *     }
 * 
 * @apiErrorExample {json} Error-Response:
 *     HTTP/1.1 401 Unauthorized
 *     {
 *       "error": "You are not permitted to do this operation!"
 *     }
 *
 */
$app->post('/signup', function () use ($user) {
	$request = json_decode(file_get_contents("php://input"), true);
	echo $user->register($request['name'], $request['email'], $request['password']);
});




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
 * @apiPermission logged-in
 * 
 */
$app->get('/me', function () use ($user){
	echo $user->json_object();
});




