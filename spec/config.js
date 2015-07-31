'use strict';
var user = require('./user.js');
var request = require('request');

var apiUrl = process.env.URL;
apiUrl = apiUrl ? apiUrl : 'http://capira.de/build/api_v2';

module.exports = {
    usersFactory: user(apiUrl),
    request: function(method,url,body,onResponse){
    	request({
            method: method,
            uri: apiUrl + url,
            json: true,
            body: body
        },onResponse);
    }
};

console.log(
	'  **************************************\n'+
	'  **                                  **\n'+
	'  **     Capira API Testing Suite     **\n'+
	'  **                                  **\n'+
	'  **************************************\n'+
	'  Configuration: \n'+
	'  Server URL: '+module.exports.url+'\n'+
	'\n');