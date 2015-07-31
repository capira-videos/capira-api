'use strict';
var user = require('./user.js');
var apiUrl = process.env.URL;
apiUrl = apiUrl ? apiUrl : 'http://capira.de/build/api_v2';
module.exports = {
    url: apiUrl,
    usersFactory: user(apiUrl)
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