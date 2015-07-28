<?php
require_once __DIR__ . '/vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Silex\Application;

$app = new Silex\Application();

    
        
            

        /**
         * @api GET /channels Request User information
         * @apiName findChannels
         * @apiGroup User
         *
         * @apiParam {Number} id Users unique ID.
         *
         * @apiSuccess {String} firstname Firstname of the User.
         * @apiSuccess {String} lastname  Lastname of the User.
         * @apiErrorExample {json} Error-Response:
         *     HTTP/1.1 404 Not Found
         *     {
         *       "error": "UserNotFound"
         *     }
         */

        $app->GET('/channels', function(Application $app, Request $request) {
            $tags = $request->get('tags');    $limit = $request->get('limit');    
            
            return new Response('test How about implementing findChannels as a GET method ?');
            });

            

        /**
         * @api POST /channels Request User information
         * @apiName addChannel
         * @apiGroup User
         *
         * @apiParam {Number} id Users unique ID.
         *
         * @apiSuccess {String} firstname Firstname of the User.
         * @apiSuccess {String} lastname  Lastname of the User.
         * @apiErrorExample {json} Error-Response:
         *     HTTP/1.1 404 Not Found
         *     {
         *       "error": "UserNotFound"
         *     }
         */

        $app->POST('/channels', function(Application $app, Request $request) {
            
            
            return new Response('test How about implementing addChannel as a POST method ?');
            });

            

        /**
         * @api GET /channels/{id} Request User information
         * @apiName findChannelById
         * @apiGroup User
         *
         * @apiParam {Number} id Users unique ID.
         *
         * @apiSuccess {String} firstname Firstname of the User.
         * @apiSuccess {String} lastname  Lastname of the User.
         * @apiErrorExample {json} Error-Response:
         *     HTTP/1.1 404 Not Found
         *     {
         *       "error": "UserNotFound"
         *     }
         */

        $app->GET('/channels/{id}', function(Application $app, Request $request, $id) {
            
            
            return new Response('test How about implementing findChannelById as a GET method ?');
            });

            

        /**
         * @api DELETE /channels/{id} Request User information
         * @apiName deleteChannel
         * @apiGroup User
         *
         * @apiParam {Number} id Users unique ID.
         *
         * @apiSuccess {String} firstname Firstname of the User.
         * @apiSuccess {String} lastname  Lastname of the User.
         * @apiErrorExample {json} Error-Response:
         *     HTTP/1.1 404 Not Found
         *     {
         *       "error": "UserNotFound"
         *     }
         */

        $app->DELETE('/channels/{id}', function(Application $app, Request $request, $id) {
            
            
            return new Response('test How about implementing deleteChannel as a DELETE method ?');
            });

            
        
    

$app->run();