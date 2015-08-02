'use strict';
require('jasmine-expect');
it = require('jasmine-async-errors').wrap(it);
var req = require('request');

var user = require('./user-utils.js');

var apiUrl = process.env.URL;
var defaultUrl = 'http://localhost:8888/api';
//var defaultUrl = 'http://capira.de/build/api_v2';
apiUrl = apiUrl ? apiUrl : defaultUrl;


module.exports={
    usersFactory: user(apiUrl),
    request: function(method, url, body, onResponse, user) {
        req({
            method: method,
            uri: apiUrl + url,
            json: true,
            body: body,
            headers: {
                Cookie: user ? user.cookie : ''
            }
        }, function(error, response, body){
            if(response.statusCode===400 && response.statusCode===500){
                console.log(
                    '>>>>>>>>>>>>>>>>>>>>>>>>> BEGIN HTTP Error <<<<<<<<<<<<<<<<<<<<<<<<<\n\n'+
                    body+'\n\n'+
                    '>>>>>>>>>>>>>>>>>>>>>>>>> END HTTP Error <<<<<<<<<<<<<<<<<<<<<<<<<\n\n');
            };
            onResponse(error, response, body);
        });
    }
};



console.log(
    '  **************************************\n' +
    '  **                                  **\n' +
    '  **     Capira API Testing Suite     **\n' +
    '  **                                  **\n' +
    '  **************************************\n' +
    '  Configuration: \n' +
    '  Server URL: ' + apiUrl + '\n' +
    '\n');
